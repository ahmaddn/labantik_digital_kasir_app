<?php

namespace App\Livewire\Management;

use App\Models\Jurusan;
use Livewire\Component;
use Livewire\WithPagination;

class JurusanManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $jurusanId;

    public $name = '';

    public $showModal = false;

    public $showDeleteModal = false;

    public $deleteId = null;

    public $parent_id = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:jurusans,name',
        'parent_id' => 'nullable|exists:jurusans,id',
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
        $jurusan = Jurusan::findOrFail($id);
        $this->jurusanId = $jurusan->id;
        $this->name = $jurusan->name;
        $this->parent_id = $jurusan->parent_id;
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->jurusanId = null;
        $this->name = '';
        $this->parent_id = null;
    }

    public function saveJurusan()
    {
        if ($this->jurusanId) {
            $this->validate([
                'name' => 'required|string|max:255|unique:jurusans,name,'.$this->jurusanId,
                'parent_id' => 'nullable|exists:jurusans,id|different:jurusanId',
            ]);
            $jurusan = Jurusan::findOrFail($this->jurusanId);
            $jurusan->update([
                'name' => $this->name,
                'parent_id' => $this->parent_id ?: null,
            ]);
        } else {
            $this->validate();
            Jurusan::create([
                'name' => $this->name,
                'parent_id' => $this->parent_id ?: null,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('toast', message: 'Data jurusan berhasil disimpan.');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteJurusan()
    {
        if ($this->deleteId) {
            Jurusan::findOrFail($this->deleteId)->delete();
            $this->dispatch('toast', message: 'Data jurusan berhasil dihapus.');
            $this->showDeleteModal = false;
            $this->deleteId = null;
        }
    }

    public function render()
    {
        if (session('active_role_name') !== 'superadmin') {
            abort(403);
        }

        $jurusans = Jurusan::with('parent')
            ->where('name', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        $parentOptions = Jurusan::whereNull('parent_id')
            ->when($this->jurusanId, function ($q) {
                return $q->where('id', '!=', $this->jurusanId);
            })
            ->get();

        return view('livewire.management.jurusan-management', [
            'jurusans' => $jurusans,
            'parentOptions' => $parentOptions,
        ])->layout('layouts.app', ['title' => 'Manajemen Jurusan']);
    }
}
