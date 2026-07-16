<?php

namespace App\Livewire\Management;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class RoleManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $roleId;

    public $name = '';

    public $label = '';

    public $showModal = false;

    public $showDeleteModal = false;

    public $deleteId = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
        'label' => 'required|string|max:255',
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
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->label = $role->label;
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->roleId = null;
        $this->name = '';
        $this->label = '';
    }

    public function saveRole()
    {
        if ($this->roleId) {
            $this->validate([
                'name' => 'required|string|max:255|unique:roles,name,'.$this->roleId,
                'label' => 'required|string|max:255',
            ]);
            $role = Role::findOrFail($this->roleId);
            $role->update([
                'name' => $this->name,
                'label' => $this->label,
            ]);
        } else {
            $this->validate();
            Role::create([
                'name' => $this->name,
                'label' => $this->label,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('toast', message: 'Data role berhasil disimpan.');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRole()
    {
        if ($this->deleteId) {
            Role::findOrFail($this->deleteId)->delete();
            $this->dispatch('toast', message: 'Data role berhasil dihapus.');
            $this->showDeleteModal = false;
            $this->deleteId = null;
        }
    }

    public function render()
    {
        if (session('active_role_name') !== 'superadmin') {
            abort(403);
        }

        $roles = Role::where('label', 'like', '%'.$this->search.'%')
            ->orWhere('name', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.management.role-management', [
            'roles' => $roles,
        ])->layout('layouts.app', ['title' => 'Manajemen Role']);
    }
}
