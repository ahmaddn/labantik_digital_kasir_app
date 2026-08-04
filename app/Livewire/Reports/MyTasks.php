<?php

namespace App\Livewire\Reports;

use App\Models\CashierAttendance;
use App\Models\CashierTaskDefinition;
use App\Models\CashierTaskAssignment;
use App\Models\CashierTaskSubmission;
use App\Models\Notification;
use App\Services\CashierTaskService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MyTasks extends Component
{
    use WithFileUploads, WithPagination;

    public bool $showTaskDetailModal = false;
    public bool $showSubmissionModal = false;
    public $selectedAssignmentId = null;
    public $selectedAssignment = null;
    public string $submissionReport = '';
    public $submissionProofImage = null;
    public string $activeTab = 'today';

    public function showTaskDetail($assignmentId): void
    {
        $assignment = CashierTaskAssignment::with(['taskDefinition', 'latestSubmission'])
            ->where('assigned_to', auth()->id())
            ->where('id', $assignmentId)
            ->first();

        if ($assignment) {
            $this->selectedAssignment = $assignment;
            $this->showTaskDetailModal = true;
        }
    }

    public function selectTaskForSubmission($assignmentId): void
    {
        $assignment = CashierTaskAssignment::with(['taskDefinition', 'submissions'])
            ->where('assigned_to', auth()->id())
            ->where('id', $assignmentId)
            ->first();

        if (!$assignment) {
            return;
        }

        // Update assignment status to 'started' jika masih 'new'
        if ($assignment->assignment_status === 'new') {
            $assignment->update(['assignment_status' => 'started']);
        }

        $this->selectedAssignmentId = $assignmentId;
        $this->selectedAssignment = $assignment;

        // Pre-fill dari previous submission jika ada (untuk revisi)
        $latestSubmission = $assignment->latestSubmission;
        if ($latestSubmission && $latestSubmission->approval_status === 'rejected') {
            $this->submissionReport = $latestSubmission->report ?? '';
            // Keep old image jika tidak upload yg baru
        } else {
            $this->submissionReport = '';
        }

        $this->submissionProofImage = null;
        $this->showSubmissionModal = true;
    }

    public function submitTaskCompletion(): void
    {
        $assignment = CashierTaskAssignment::with('taskDefinition')
            ->where('assigned_to', auth()->id())
            ->where('id', $this->selectedAssignmentId)
            ->first();

        if (!$assignment) {
            return;
        }

        $taskDef = $assignment->taskDefinition;
        $latestSubmission = $assignment->latestSubmission;

        // Check if latest submission is already approved
        if ($latestSubmission && $latestSubmission->approval_status === 'approved') {
            $this->dispatch('toast', message: 'Tugas sudah disetujui admin dan tidak bisa diubah.', type: 'warning');
            return;
        }

        // Validation
        $rules = [
            'submissionReport' => 'required|string|min:5',
        ];

        if ($taskDef->requires_proof) {
            // New submission requires proof
            if (!$latestSubmission) {
                $rules['submissionProofImage'] = 'required|image|max:2048';
            } else {
                // Revision: proof is optional if already provided
                $rules['submissionProofImage'] = 'nullable|image|max:2048';
            }
        } else {
            $rules['submissionProofImage'] = 'nullable';
        }

        $this->validate($rules);

        $proofImagePath = null;
        if ($this->submissionProofImage) {
            $proofImagePath = $this->submissionProofImage->store('tasks/proofs', 'public');
        }

        $service = app(CashierTaskService::class);
        $submission = $service->submitTaskCompletion(
            $assignment,
            $this->submissionReport,
            $proofImagePath
        );

        // Notify admin who created the task
        $isRevision = $latestSubmission && $latestSubmission->approval_status === 'rejected';
        Notification::create([
            'user_id' => $taskDef->created_by,
            'title' => $isRevision ? 'Revisi Tugas Menunggu ACC' : 'Laporan Tugas Menunggu ACC',
            'body' => auth()->user()->name . ' menyelesaikan tugas "' . $taskDef->task_name . '". Silakan review & ACC.',
            'type' => 'task',
            'action_url' => '/management/tasks',
        ]);

        $this->showSubmissionModal = false;
        $this->reset(['selectedAssignmentId', 'selectedAssignment', 'submissionReport', 'submissionProofImage']);
        $this->dispatch('toast', message: $isRevision
            ? 'Revisi tugas berhasil dikirim, menunggu ACC admin.'
            : 'Laporan tugas berhasil dikirim, menunggu ACC admin!');
    }

    public function render()
    {
        $userId = auth()->id();
        $activeJurusanId = session('active_jurusan_id');
        $today = now()->toDateString();

        // Get task assignments untuk kasir ini
        $todayAssignments = CashierTaskAssignment::with([
            'taskDefinition',
            'submissions' => fn($q) => $q->orderBy('submission_version', 'desc'),
        ])
            ->where('assigned_to', $userId)
            ->whereHas('taskDefinition', function ($q) use ($today) {
                $q->where(function ($sq) use ($today) {
                    // Tasks for today or routine tasks not yet approved
                    $sq->where('date', $today)
                        ->orWhere(function ($sq2) {
                            $sq2->where('is_routine', true)
                                ->whereNotIn('id', function ($q3) {
                                    $q3->select('task_definition_id')
                                        ->from('cashier_task_submissions')
                                        ->where('approval_status', 'approved');
                                });
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Compute deadline untuk routine tasks
        foreach ($todayAssignments as $assignment) {
            if ($assignment->taskDefinition->is_routine && !$assignment->taskDefinition->deadline_at) {
                $attendance = CashierAttendance::where('user_id', $userId)
                    ->where('date', $assignment->taskDefinition->date)
                    ->orderBy('clock_in', 'asc')
                    ->first();

                if ($attendance && $attendance->clock_in) {
                    $assignment->taskDefinition->computed_deadline = Carbon::parse($attendance->clock_in)->addHours(8);
                } else {
                    $assignment->taskDefinition->computed_deadline = null;
                }
            }
        }

        // History: assignments dari hari sebelumnya atau yang sudah approved
        $historyAssignments = CashierTaskAssignment::with([
            'taskDefinition',
            'submissions' => fn($q) => $q->orderBy('submission_version', 'desc'),
        ])
            ->where('assigned_to', $userId)
            ->whereHas('taskDefinition', function ($q) use ($today) {
                $q->where(function ($sq) use ($today) {
                    $sq->where('date', '<', $today)
                        ->orWhereHas('submissions', fn($q2) => $q2->where('approval_status', 'approved'));
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($historyAssignments as $assignment) {
            if ($assignment->taskDefinition->is_routine && !$assignment->taskDefinition->deadline_at) {
                $attendance = CashierAttendance::where('user_id', $userId)
                    ->where('date', $assignment->taskDefinition->date)
                    ->orderBy('clock_in', 'asc')
                    ->first();

                if ($attendance && $attendance->clock_in) {
                    $assignment->taskDefinition->computed_deadline = Carbon::parse($attendance->clock_in)->addHours(8);
                } else {
                    $assignment->taskDefinition->computed_deadline = null;
                }
            }
        }

        return view('livewire.reports.my-tasks', [
            'todayAssignments' => $todayAssignments,
            'historyAssignments' => $historyAssignments,
        ])->layout('layouts.app', ['title' => 'Tugas Saya']);
    }
}
