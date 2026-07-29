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

        CashierTask::create([
            'jurusan_id' => $activeJurusanId,
            'assigned_to' => $this->assignedTo,
            'date' => $this->date,
            'task_name' => $this->taskName,
            'description' => $this->description,
            'created_by' => auth()->id(),
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
        $tasks = CashierTask::with(['user', 'creator'])
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
