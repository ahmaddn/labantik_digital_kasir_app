<?php

namespace App\Livewire\Management;

use App\Models\CashierTask;
use App\Models\Jurusan;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CashierTasks extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedJurusanId = '';
    public $date = '';
    public $taskName = '';
    public $description = '';
    public $assignedTo = '';

    // Modal UI states
    public $showCreateModal = false;
    public $showDeleteModal = false;
    public $deletingTaskId = null;
    public $showRejectModal = false;
    public $rejectingTaskId = null;
    public $rejectionNote = '';

    protected $rules = [
        'assignedTo' => 'required|exists:users,id',
        'date' => 'required|date',
        'taskName' => 'required|string|max:255',
        'description' => 'nullable|string',
    ];

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->selectedJurusanId = session('active_jurusan_id') ?? '';
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function resetForm()
    {
        $this->assignedTo = '';
        $this->taskName = '';
        $this->description = '';
        $this->date = now()->toDateString();
    }

    public function saveTask()
    {
        $this->validate();

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;
        if (!$activeJurusanId) {
            $this->dispatch('toast', message: 'Pilih jurusan terlebih dahulu.', type: 'danger');
            return;
        }

        $task = CashierTask::create([
            'jurusan_id' => $activeJurusanId,
            'assigned_to' => $this->assignedTo,
            'date' => $this->date,
            'task_name' => $this->taskName,
            'description' => $this->description,
            'created_by' => auth()->id(),
        ]);

        // Send a global notification
        \App\Models\Notification::create([
            'user_id' => $this->assignedTo,
            'title' => 'Tugas Baru Ditugaskan',
            'body' => 'Anda mendapatkan tugas: "' . $this->taskName . '" pada tanggal ' . \Carbon\Carbon::parse($this->date)->format('d M Y'),
            'type' => 'task',
            'action_url' => '/cashier'
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        $this->dispatch('toast', message: 'Tugas harian kasir berhasil ditambahkan!');
    }

    public function confirmDelete($id)
    {
        $this->deletingTaskId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteTask()
    {
        if ($this->deletingTaskId) {
            CashierTask::findOrFail($this->deletingTaskId)->delete();
            $this->showDeleteModal = false;
            $this->deletingTaskId = null;
            $this->dispatch('toast', message: 'Tugas berhasil dihapus.');
        }
    }

    public function approveTask($id)
    {
        $task = CashierTask::findOrFail($id);

        if ($task->approval_status !== 'pending') {
            $this->dispatch('toast', message: 'Tugas ini tidak sedang menunggu review.', type: 'warning');
            return;
        }

        $task->update([
            'approval_status' => 'approved',
            'rejection_note' => null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Award gamification points only after admin approval
        $cashier = User::find($task->assigned_to);
        if ($cashier) {
            $cashier->increment('pending_points', 10);
            $cashier->increment('streak', 1);
        }

        \App\Models\Notification::create([
            'user_id' => $task->assigned_to,
            'title' => 'Tugas Disetujui',
            'body' => 'Laporan tugas "' . $task->task_name . '" telah di-ACC admin. +10 poin untukmu!',
            'type' => 'task',
            'action_url' => '/my-tasks'
        ]);

        $this->dispatch('toast', message: 'Tugas disetujui, poin diberikan ke kasir.');
    }

    public function openRejectModal($id)
    {
        $this->rejectingTaskId = $id;
        $this->rejectionNote = '';
        $this->showRejectModal = true;
    }

    public function rejectTask()
    {
        $this->validate([
            'rejectionNote' => 'required|string|min:5',
        ], [], ['rejectionNote' => 'catatan penolakan']);

        $task = CashierTask::findOrFail($this->rejectingTaskId);

        // Reopen the task so the cashier must revise & resubmit
        $task->update([
            'approval_status' => 'rejected',
            'rejection_note' => $this->rejectionNote,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'is_completed' => false,
        ]);

        \App\Models\Notification::create([
            'user_id' => $task->assigned_to,
            'title' => 'Tugas Ditolak — Perlu Revisi',
            'body' => 'Laporan tugas "' . $task->task_name . '" ditolak: ' . $this->rejectionNote . '. Silakan revisi & kirim ulang.',
            'type' => 'task',
            'action_url' => '/my-tasks'
        ]);

        $this->showRejectModal = false;
        $this->rejectingTaskId = null;
        $this->rejectionNote = '';
        $this->dispatch('toast', message: 'Tugas ditolak & dikembalikan ke kasir untuk direvisi.');
    }

    public function render()
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan'])) {
            abort(403, 'Unauthorized.');
        }

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;

        // Fetch Cashiers for dropdown selection
        $cashiers = User::whereHas('roles', function($q) use ($activeJurusanId) {
            $q->where('roles.name', 'kasir')
              ->when($activeJurusanId, function($sq) use ($activeJurusanId) {
                  $sq->where('role_user.jurusan_id', $activeJurusanId);
              });
        })->get();

        // Query Tasks
        $tasks = CashierTask::with(['user', 'creator', 'reviewer'])
            ->when($activeJurusanId, function($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
            ->when($this->search, function($q) {
                $q->where('task_name', 'like', '%'.$this->search.'%')
                  ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.management.cashier-tasks', [
            'tasks' => $tasks,
            'cashiers' => $cashiers,
            'jurusans' => Jurusan::all(),
        ])->layout('layouts.app', ['title' => 'Tugas Kasir']);
    }
}
