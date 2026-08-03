<?php

namespace App\Livewire\Reports;

use App\Models\CashierTask;
use App\Models\Notification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MyTasks extends Component
{
    use WithFileUploads, WithPagination;

    // Task completion modal state
    public bool $showTaskCompletionModal = false;

    public $selectedTaskId = null;

    public $selectedTaskModel = null;

    public string $taskCompletionReport = '';

    public $taskProofImage = null;

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
            $this->selectedTaskModel = $task->fresh();
            $this->taskCompletionReport = $task->completion_report ?? '';
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

        // A new proof image is required on first submission; on revision the old one may be kept
        if (! $task->proof_image) {
            $rules['taskProofImage'] = 'required|image|max:2048'; // Max 2MB
        } else {
            $rules['taskProofImage'] = 'nullable|image|max:2048';
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

        $todayTasks = CashierTask::where('assigned_to', $userId)
            ->where('date', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->get();

        $historyTasks = CashierTask::with('reviewer')
            ->where('assigned_to', $userId)
            ->where('date', '<', now()->toDateString())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.reports.my-tasks', [
            'todayTasks' => $todayTasks,
            'historyTasks' => $historyTasks,
        ])->layout('layouts.app', ['title' => 'Tugas Saya']);
    }
}
