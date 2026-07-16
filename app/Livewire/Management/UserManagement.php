<?php

namespace App\Livewire\Management;

use App\Models\Jurusan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';

    // Form State
    public $userId;

    public $name = '';

    public $email = '';

    public $password = '';

    public $assignedAccesses = []; // Array of ['role_id' => ..., 'role_label' => ..., 'jurusan_id' => ..., 'jurusan_name' => ...]

    // Temp inputs for adding access
    public $selectedRoleId = '';

    public $selectedJurusanId = '';

    // UI state
    public $showModal = false;

    public $showDeleteModal = false;

    public $deleteId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;

        // Load accesses
        $accesses = $user->getAvailableAccesses();
        foreach ($accesses as $acc) {
            $this->assignedAccesses[] = [
                'role_id' => $acc->role_id,
                'role_label' => $acc->role_label,
                'jurusan_id' => $acc->jurusan_id,
                'jurusan_name' => $acc->jurusan_name ?? 'Global',
            ];
        }

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->assignedAccesses = [];
        $this->selectedRoleId = '';
        $this->selectedJurusanId = '';
    }

    public function addAccess()
    {
        $this->validate([
            'selectedRoleId' => 'required',
        ]);

        $role = Role::find($this->selectedRoleId);
        $jurusan = $this->selectedJurusanId ? Jurusan::find($this->selectedJurusanId) : null;

        if (! $role) {
            return;
        }

        // Prevent duplicate accesses
        foreach ($this->assignedAccesses as $acc) {
            if ($acc['role_id'] === $role->id && $acc['jurusan_id'] === ($jurusan ? $jurusan->id : null)) {
                $this->dispatch('toast', message: 'Hak akses ini sudah ditambahkan.');

                return;
            }
        }

        $this->assignedAccesses[] = [
            'role_id' => $role->id,
            'role_label' => $role->label,
            'jurusan_id' => $jurusan ? $jurusan->id : null,
            'jurusan_name' => $jurusan ? $jurusan->name : 'Global',
        ];

        // Reset selections
        $this->selectedRoleId = '';
        $this->selectedJurusanId = '';
    }

    public function removeAccess($index)
    {
        unset($this->assignedAccesses[$index]);
        $this->assignedAccesses = array_values($this->assignedAccesses);
    }

    public function saveUser()
    {
        $this->validate();

        if (! $this->userId) {
            $this->validate([
                'email' => 'unique:users,email',
                'password' => 'required|min:6',
            ]);
        } else {
            $this->validate([
                'email' => 'unique:users,email,'.$this->userId,
                'password' => 'nullable|min:6',
            ]);
        }

        DB::transaction(function () {
            if ($this->userId) {
                $user = User::findOrFail($this->userId);
                $user->name = $this->name;
                $user->email = $this->email;
                if ($this->password) {
                    $user->password = Hash::make($this->password);
                }
                $user->save();
            } else {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                ]);
            }

            // Sync accesses in pivot table
            DB::table('role_user')->where('user_id', $user->id)->delete();

            foreach ($this->assignedAccesses as $acc) {
                DB::table('role_user')->insert([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'role_id' => $acc['role_id'],
                    'jurusan_id' => $acc['jurusan_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        $this->showModal = false;
        $this->dispatch('toast', message: 'Data user berhasil disimpan.');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if ($this->deleteId) {
            // Prevent deleting self
            if ($this->deleteId === auth()->id()) {
                $this->dispatch('toast', message: 'Anda tidak dapat menghapus akun Anda sendiri.');
                $this->showDeleteModal = false;

                return;
            }

            User::findOrFail($this->deleteId)->delete();
            $this->dispatch('toast', message: 'Akun user berhasil dihapus.');
            $this->showDeleteModal = false;
            $this->deleteId = null;
        }
    }

    public function render()
    {
        // Only superadmin can access this
        if (session('active_role_name') !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        $users = User::where(function ($q) {
            $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%');
        })
            ->latest()
            ->paginate(10);

        $roles = Role::orderBy('label')->get();
        $jurusans = Jurusan::orderBy('name')->get();

        return view('livewire.management.user-management', [
            'users' => $users,
            'roles' => $roles,
            'jurusans' => $jurusans,
        ])->layout('layouts.app', ['title' => 'Manajemen User']);
    }
}
