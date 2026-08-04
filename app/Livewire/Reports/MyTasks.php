<?php

namespace App\Livewire\Reports;

use App\Models\CashierAttendance;
use App\Models\CashierTask;
use App\Models\Notification;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MyTasks extends Component
{
    use WithFileUploads, WithPagination;

    // Task completion modal state
    public bool $showTaskCompletionModal = false;

    public bool $showTaskDetailModal = false;

    public $selectedTaskId = null;

    public $selectedTaskModel = null;

    public string $taskCompletionReport = '';

    public $taskProofImage = null;

    public string $activeTab = 'today';

    public function showTaskDetail($taskId): void
    {
        $task = CashierTask::where('assigned_to', auth()->id())
            ->where('id', $taskId)
            ->first();

        if ($task) {
            $fresh = $task->fresh();

            // Compute deadline for routine tasks: 8 hours after first clock-in of the day
            if ($fresh->is_routine && ! $fresh->deadline_at) {
                $attendance = CashierAttendance::where('user_id', auth()->id())
                    ->where('date', $fresh->date)
                    ->orderBy('clock_in', 'asc')
                    ->first();

                if ($attendance && $attendance->clock_in) {
                    $fresh->computed_deadline = Carbon::parse($attendance->clock_in)->addHours(8);
                } else {
                    $fresh->computed_deadline = null;
                }
            }

            $this->selectedTaskModel = $fresh;
            $this->showTaskDetailModal = true;
        }
    }

    public function selectTaskForCompletion($taskId): void
    {
        $task = CashierTask::where('assigned_to', auth()->id())
            ->where('id', $taskId)
            ->first();

        if ($task) {
            if ($task->status === 'new') {
                $task->update(['status' => 'pending']);
            }

            $this->selectedTaskId = $taskId;
            $fresh = $task->fresh();

            // Compute deadline for routine tasks: 8 hours after first clock-in of the day
            if ($fresh->is_routine && ! $fresh->deadline_at) {
                $attendance = CashierAttendance::where('user_id', auth()->id())
                    ->where('date', $fresh->date)
                    ->orderBy('clock_in', 'asc')
                    ->first();

                if ($attendance && $attendance->clock_in) {
                    $fresh->computed_deadline = Carbon::parse($attendance->clock_in)->addHours(8);
                } else {
                    $fresh->computed_deadline = null;
                }
            }

            $this->selectedTaskModel = $fresh;
            $this->taskCompletionReport = $fresh->completion_report ?? '';
            $this->taskProofImage = null;
            $this->showTaskCompletionModal = true;
        }
    }

    public function submitTaskCompletion(): void
    {
        $task = CashierTask::where('assigned_to', auth()->id())
            ->where('id', $this->selectedTaskId)
            ->first();

        if (! $task) {
            return;
        }

        // Approved tasks are locked, no resubmission allowed
        if ($task->approval_status === 'approved') {
            $this->dispatch('toast', message: 'Tugas sudah disetujui admin dan tidak bisa diubah.', type: 'warning');

            return;
        }

        $rules = [
            'taskCompletionReport' => 'required|string|min:5',
        ];

        // A new proof image is required on first submission if requires_proof is true; on revision the old one may be kept
        if ($task->requires_proof) {
            if (! $task->proof_image) {
                $rules['taskProofImage'] = 'required|image|max:2048'; // Max 2MB
            } else {
                $rules['taskProofImage'] = 'nullable|image|max:2048';
            }
        } else {
            $rules['taskProofImage'] = 'nullable';
        }

        $this->validate($rules);

        $data = [
            'completion_report' => $this->taskCompletionReport,
            'is_completed' => true,
            'completed_at' => now(),
            'status' => 'pending',
            'approval_status' => 'pending',
        ];

        if ($this->taskProofImage) {
            $path = $this->taskProofImage->store('tasks/proofs', 'public');
            $data['proof_image'] = $path;
        }

        $isRevision = $task->approval_status === 'rejected';

        $task->update($data);

        // Notify the admin who created the task to review it
        Notification::create([
            'user_id' => $task->created_by,
            'title' => $isRevision ? 'Revisi Tugas Menunggu ACC' : 'Laporan Tugas Menunggu ACC',
            'body' => auth()->user()->name . ' menyelesaikan tugas "' . $task->task_name . '". Silakan review & ACC.',
            'type' => 'task',
            'action_url' => '/management/tasks',
        ]);

        $this->showTaskCompletionModal = false;
        $this->reset(['selectedTaskId', 'selectedTaskModel', 'taskCompletionReport', 'taskProofImage']);
        $this->dispatch('toast', message: $isRevision
            ? 'Revisi tugas berhasil dikirim, menunggu ACC admin.'
            : 'Laporan tugas berhasil dikirim, menunggu ACC admin!');
    }

    public function render()
    {
        $userId = auth()->id();
        $activeJurusanId = session('active_jurusan_id');
        $today = now()->toDateString();

        // Auto-assign routine tasks of today to the logged in cashier if they are scheduled but don't have them yet
        $isScheduledToday = \App\Models\CashierSchedule::where('user_id', $userId)
            ->where('jurusan_id', $activeJurusanId)
            ->where('date', $today)
            ->exists();

        if ($isScheduledToday) {
            // Find all unique routine tasks in this jurusan (from any date)
            $routineTemplates = CashierTask::where('jurusan_id', $activeJurusanId)
                ->where('is_routine', true)
                ->get()
                ->groupBy('group_id');

            foreach ($routineTemplates as $groupId => $tasks) {
                // Check if this cashier already has this routine task cloned for TODAY
                $hasTaskToday = CashierTask::where('assigned_to', $userId)
                    ->where('group_id', $groupId)
                    ->where('date', $today)
                    ->exists();

                if (!$hasTaskToday && $tasks->isNotEmpty()) {
                    // Clone one of the tasks in the group for this user with TODAY's date
                    $template = $tasks->first();
                    CashierTask::create([
                        'jurusan_id' => $template->jurusan_id,
                        'group_id' => $template->group_id,
                        'assigned_to' => $userId,
                        'date' => $today, // Today's date!
                        'task_name' => $template->task_name,
                        'description' => $template->description,
                        'deadline_at' => null, // Dynamic deadline (8 hours from clock-in)
                        'status' => 'new',
                        'priority' => $template->priority,
                        'category' => $template->category,
                        'is_routine' => true,
                        'requires_proof' => $template->requires_proof,
                        'created_by' => $template->created_by,
                    ]);
                }
            }
        }

        $todayTasks = CashierTask::where('assigned_to', $userId)
            ->where(function ($query) {
                $query->where('date', now()->toDateString())
                    ->orWhere(function ($q) {
                        $q->where('is_routine', true)
                            ->where('approval_status', '!=', 'approved');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Compute computed_deadline for routine tasks per user
        foreach ($todayTasks as $t) {
            if ($t->is_routine && ! $t->deadline_at) {
                $attendance = CashierAttendance::where('user_id', $userId)
                    ->where('date', $t->date)
                    ->orderBy('clock_in', 'asc')
                    ->first();

                if ($attendance && $attendance->clock_in) {
                    $t->computed_deadline = Carbon::parse($attendance->clock_in)->addHours(8);
                } else {
                    $t->computed_deadline = null;
                }
            }
        }

        $historyTasks = CashierTask::with('reviewer')
            ->where('assigned_to', $userId)
            ->where('date', '<', now()->toDateString())
            ->where(function ($query) {
                $query->where('is_routine', false)
                    ->orWhere('approval_status', 'approved');
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($historyTasks as $t) {
            if ($t->is_routine && ! $t->deadline_at) {
                $attendance = CashierAttendance::where('user_id', $userId)
                    ->where('date', $t->date)
                    ->orderBy('clock_in', 'asc')
                    ->first();

                if ($attendance && $attendance->clock_in) {
                    $t->computed_deadline = Carbon::parse($attendance->clock_in)->addHours(8);
                } else {
                    $t->computed_deadline = null;
                }
            }
        }

        return view('livewire.reports.my-tasks', [
            'todayTasks' => $todayTasks,
            'historyTasks' => $historyTasks,
        ])->layout('layouts.app', ['title' => 'Tugas Saya']);
    }
}
