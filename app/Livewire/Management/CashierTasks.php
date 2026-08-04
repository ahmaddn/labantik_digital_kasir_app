<?php

namespace App\Livewire\Management;

use App\Models\CashierSchedule;
use App\Models\CashierTaskDefinition;
use App\Models\CashierTaskAssignment;
use App\Models\CashierTaskSubmission;
use App\Models\Jurusan;
use App\Models\Notification;
use App\Models\TaskCategory;
use App\Models\User;
use App\Services\CashierTaskService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class CashierTasks extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $selectedJurusanId = '';
    public $activeTab = 'active'; // 'active', 'pending_review', 'history'
    public $date = '';
    public $taskName = '';
    public $description = '';
    public $assignedTo = [];
    public $assigneeMode = 'scheduled'; // 'scheduled' atau 'all'
    public $category = '';
    public $showAddCategoryModal = false;
    public $newCategoryName = '';
    public $priority = 'medium';
    public $deadlineAt = '';
    public $isRoutine = false;
    public $requiresProof = true;

    // Modal states
    public $showCreateModal = false;
    public $showDeleteModal = false;
    public $deletingTaskId = null;
    public $showReviewModal = false;
    public $reviewingTaskId = null;
    public $showRejectModal = false;
    public $rejectingTaskId = null;
    public $rejectionNote = '';
    public $isEditMode = false;
    public $editingTaskId = null;

    // Review modal states
    public $currentReviewingSubmissions = [];
    public $selectedSubmissionForReview = null;

    // Pagination
    public $pendingAssignees = [];
    public $showConfirmModal = false;

    protected $rules = [
        'date' => 'required|date',
        'taskName' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category' => 'nullable|string|max:255',
        'priority' => 'required|in:low,medium,high,critical',
        'deadlineAt' => 'nullable|date',
        'isRoutine' => 'boolean',
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
        $this->reset([
            'taskName',
            'description',
            'assignedTo',
            'category',
            'priority',
            'deadlineAt',
            'isRoutine',
            'requiresProof',
            'isEditMode',
            'editingTaskId',
            'assigneeMode',
            'pendingAssignees',
        ]);
        $this->date = now()->toDateString();
        $this->priority = 'medium';
        $this->requiresProof = true;
    }

    public function setAssigneeMode($mode)
    {
        $this->assigneeMode = $mode;
        $this->assignedTo = [];
    }

    public function openAddCategoryModal()
    {
        $this->newCategoryName = '';
        $this->showAddCategoryModal = true;
    }

    public function storeCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255',
        ], [], ['newCategoryName' => 'nama kategori']);

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;

        TaskCategory::create([
            'jurusan_id' => $activeJurusanId,
            'name' => $this->newCategoryName,
            'created_by' => auth()->id(),
        ]);

        $this->dispatch('toast', message: 'Kategori berhasil ditambahkan!');
        $this->showAddCategoryModal = false;
        $this->newCategoryName = '';
    }

    public function prepareTask()
    {
        $this->validate();

        if (!$this->isRoutine && count($this->assignedTo) > 1) {
            // Confirm modal untuk multiple assignees
            $this->pendingAssignees = (array) $this->assignedTo;
            $this->showConfirmModal = true;
            return;
        }

        $this->finalSaveTask();
    }

    public function finalSaveTask()
    {
        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;
        if (!$activeJurusanId) {
            $this->dispatch('toast', message: 'Pilih jurusan terlebih dahulu.', type: 'danger');
            return;
        }

        $service = app(CashierTaskService::class);

        try {
            if ($this->isEditMode) {
                // Edit mode: update task definition
                $taskDef = CashierTaskDefinition::findOrFail($this->editingTaskId);
                
                $taskDef->update([
                    'task_name' => $this->taskName,
                    'description' => $this->description,
                    'date' => $this->date,
                    'priority' => $this->priority,
                    'category' => $this->category,
                    'deadline_at' => $this->deadlineAt,
                    'requires_proof' => $this->requiresProof,
                ]);

                // Clear old assignments dan buat baru (if assignees changed)
                if (!$this->isRoutine) {
                    $taskDef->assignments()->delete();
                    $assignees = $this->pendingAssignees ?: (array) $this->assignedTo;
                    $service->assignTaskToUsers($taskDef, $assignees);
                } else {
                    // Routine task: ensure all current kasir have assignment
                    $service->assignTaskToAllCashiers($taskDef);
                }

                $message = 'Tugas berhasil diperbarui!';
            } else {
                // Create mode
                $taskDef = $service->createTask([
                    'jurusan_id' => $activeJurusanId,
                    'task_name' => $this->taskName,
                    'description' => $this->description,
                    'date' => $this->date,
                    'priority' => $this->priority,
                    'category' => $this->category,
                    'is_routine' => $this->isRoutine,
                    'requires_proof' => $this->requiresProof,
                    'deadline_at' => $this->deadlineAt,
                    'created_by' => auth()->id(),
                ]);

                if ($this->isRoutine) {
                    $service->assignTaskToAllCashiers($taskDef);
                    $cashiers = User::whereHas('roles', function ($q) use ($activeJurusanId) {
                        $q->where('roles.name', 'kasir')
                            ->where('role_user.jurusan_id', $activeJurusanId);
                    })->get();

                    foreach ($cashiers as $cashier) {
                        Notification::create([
                            'user_id' => $cashier->id,
                            'title' => 'Tugas Rutin Baru',
                            'body' => 'Anda mendapatkan tugas rutin: "' . $this->taskName . '" untuk tanggal ' . Carbon::parse($this->date)->format('d M Y'),
                            'type' => 'task',
                            'action_url' => '/my-tasks',
                        ]);
                    }

                    $message = 'Tugas rutin harian berhasil ditambahkan untuk semua kasir.';
                } else {
                    $assignees = $this->pendingAssignees ?: (array) $this->assignedTo;
                    $service->assignTaskToUsers($taskDef, $assignees);

                    foreach ($assignees as $assigneeId) {
                        Notification::create([
                            'user_id' => $assigneeId,
                            'title' => 'Tugas Baru Ditugaskan',
                            'body' => 'Anda mendapatkan tugas: "' . $this->taskName . '" pada tanggal ' . Carbon::parse($this->date)->format('d M Y'),
                            'type' => 'task',
                            'action_url' => '/my-tasks',
                        ]);
                    }

                    $count = count($assignees);
                    $message = $count > 1 ? $count . ' tugas berhasil ditambahkan untuk kasir terpilih.' : 'Tugas harian kasir berhasil ditambahkan!';
                }
            }

            $this->showConfirmModal = false;
            $this->pendingAssignees = [];
            $this->showCreateModal = false;
            $this->resetForm();
            $this->dispatch('toast', message: $message);
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function editTask($id)
    {
        $taskDef = CashierTaskDefinition::findOrFail($id);
        
        $this->isEditMode = true;
        $this->editingTaskId = $id;
        $this->taskName = $taskDef->task_name;
        $this->description = $taskDef->description;
        $this->date = $taskDef->date->toDateString();
        $this->priority = $taskDef->priority;
        $this->category = $taskDef->category;
        $this->isRoutine = $taskDef->is_routine;
        $this->requiresProof = $taskDef->requires_proof;
        $this->deadlineAt = $taskDef->deadline_at?->format('Y-m-d\TH:i');

        if (!$this->isRoutine) {
            $this->assignedTo = $taskDef->assignments()
                ->pluck('assigned_to')
                ->toArray();
        }

        $this->showCreateModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deletingTaskId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteTask()
    {
        if ($this->deletingTaskId) {
            $taskDef = CashierTaskDefinition::findOrFail($this->deletingTaskId);
            
            // Delete associated assignments dan submissions
            CashierTaskAssignment::where('task_definition_id', $taskDef->id)->delete();
            $taskDef->delete();

            $this->dispatch('toast', message: 'Tugas berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deletingTaskId = null;
    }

    public function openReviewModal($id)
    {
        $taskDef = CashierTaskDefinition::findOrFail($id);
        $this->reviewingTaskId = $id;

        $service = app(CashierTaskService::class);
        
        // Get ONLY submissions yang pending (bukan semua assignments)
        $allSubmissions = $service->getSubmissionsForTask($taskDef)
            ->where('approval_status', 'pending')
            ->values()
            ->toArray();
            
        $this->currentReviewingSubmissions = $allSubmissions;

        // Auto-select first submission if available
        if (count($this->currentReviewingSubmissions) > 0) {
            $this->selectedSubmissionForReview = $this->currentReviewingSubmissions[0]['id'];
        }

        $this->showReviewModal = true;
    }

    public function selectSubmissionForReview($submissionId)
    {
        $this->selectedSubmissionForReview = $submissionId;
    }

    public function approveSubmission()
    {
        if (!$this->selectedSubmissionForReview) {
            $this->dispatch('toast', message: 'Pilih submission terlebih dahulu.', type: 'warning');
            return;
        }

        $submission = CashierTaskSubmission::findOrFail($this->selectedSubmissionForReview);

        if ($submission->approval_status !== 'pending') {
            $this->dispatch('toast', message: 'Submission ini tidak sedang menunggu review.', type: 'warning');
            return;
        }

        $service = app(CashierTaskService::class);
        $service->approveSubmission($submission, auth()->id());

        // Notify kasir
        Notification::create([
            'user_id' => $submission->submitted_by,
            'title' => 'Tugas Disetujui',
            'body' => 'Laporan tugas "' . $submission->assignment->taskDefinition->task_name . '" telah di-ACC admin.',
            'type' => 'task',
            'action_url' => '/my-tasks',
        ]);

        $this->dispatch('toast', message: 'Submission disetujui!');
        $this->openReviewModal($this->reviewingTaskId); // Refresh submissions list
    }

    public function openRejectModal()
    {
        if (!$this->selectedSubmissionForReview) {
            $this->dispatch('toast', message: 'Pilih submission terlebih dahulu.', type: 'warning');
            return;
        }

        $this->rejectingTaskId = $this->selectedSubmissionForReview;
        $this->rejectionNote = '';
        $this->showRejectModal = true;
    }

    public function rejectSubmission()
    {
        $this->validate([
            'rejectionNote' => 'required|string|min:5',
        ]);

        $submission = CashierTaskSubmission::findOrFail($this->rejectingTaskId);

        $service = app(CashierTaskService::class);
        $service->rejectSubmission($submission, $this->rejectionNote, auth()->id());

        // Notify kasir
        Notification::create([
            'user_id' => $submission->submitted_by,
            'title' => 'Tugas Ditolak — Perlu Revisi',
            'body' => 'Laporan tugas "' . $submission->assignment->taskDefinition->task_name . '" ditolak: ' . $this->rejectionNote . '. Silakan revisi & kirim ulang.',
            'type' => 'task',
            'action_url' => '/my-tasks',
        ]);

        $this->dispatch('toast', message: 'Submission ditolak & dikembalikan ke kasir untuk direvisi.');
        $this->showRejectModal = false;
        $this->rejectingTaskId = null;
        $this->rejectionNote = '';
        $this->openReviewModal($this->reviewingTaskId); // Refresh submissions list
    }

    public function render()
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan'])) {
            abort(403, 'Unauthorized.');
        }

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;

        // Fetch Cashiers for dropdown selection
        if ($this->assigneeMode === 'scheduled') {
            $scheduledIds = CashierSchedule::where('date', now()->toDateString())
                ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                    $q->where('jurusan_id', $activeJurusanId);
                })->pluck('user_id')->unique()->values();

            $cashiers = User::whereIn('id', $scheduledIds)
                ->whereHas('roles', function ($q) use ($activeJurusanId) {
                    $q->where('roles.name', 'kasir')
                        ->when($activeJurusanId, function ($sq) use ($activeJurusanId) {
                            $sq->where('role_user.jurusan_id', $activeJurusanId);
                        });
                })->get();
        } else {
            $cashiers = User::whereHas('roles', function ($q) use ($activeJurusanId) {
                $q->where('roles.name', 'kasir')
                    ->when($activeJurusanId, function ($sq) use ($activeJurusanId) {
                        $sq->where('role_user.jurusan_id', $activeJurusanId);
                    });
            })->get();
        }

        // Categories
        $categories = TaskCategory::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            $q->where('jurusan_id', $activeJurusanId);
        })->orderBy('name')->pluck('name')->all();

        // Query Task Definitions based on active tab
        $with = [
            'assignments',
            'assignments.assignee',
            'assignments.submissions',
            'assignments.latestSubmission',
            'creator',
        ];

        $search = $this->search;
        $today = now()->toDateString();

        if ($this->activeTab === 'pending_review') {
            $query = CashierTaskDefinition::with($with);
            if ($activeJurusanId) $query->where('jurusan_id', $activeJurusanId);
            if ($search) $query->where(function($q) use ($search) {
                $q->where('task_name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
            $query->whereHas('assignments', function ($q) {
                $q->whereHas('submissions', function ($q2) {
                    $q2->where('approval_status', 'pending');
                });
            });
            $tasks = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->simplePaginate(15);

        } elseif ($this->activeTab === 'history') {
            $query = CashierTaskDefinition::with($with);
            if ($activeJurusanId) $query->where('jurusan_id', $activeJurusanId);
            if ($search) $query->where(function($q) use ($search) {
                $q->where('task_name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
            $query->where('date', '<', $today);
            $tasks = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->simplePaginate(15);

        } else {
            $query = CashierTaskDefinition::with($with);
            if ($activeJurusanId) $query->where('jurusan_id', $activeJurusanId);
            if ($search) $query->where(function($q) use ($search) {
                $q->where('task_name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
            $query->where('date', '>=', $today);
            $tasks = $query->orderBy('date', 'asc')->orderBy('created_at', 'desc')->simplePaginate(15);
        }

        $jurusans = session('active_role_name') === 'superadmin' 
            ? Jurusan::all() 
            : collect();

        return view('livewire.management.cashier-tasks', [
            'tasks' => $tasks,
            'cashiers' => $cashiers,
            'categories' => $categories,
            'jurusans' => $jurusans,
            'currentReviewingSubmissions' => $this->currentReviewingSubmissions,
        ]);
    }
}
