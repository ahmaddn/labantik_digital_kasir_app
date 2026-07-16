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

    protected $rules = [
        'name' => 'required|string|max:255|unique:jurusans,name',
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
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->jurusanId = null;
        $this->name = '';
    }

    public function saveJurusan()
    {
        if ($this->jurusanId) {
            $this->validate([
                'name' => 'required|string|max:255|unique:jurusans,name,'.$this->jurusanId,
            ]);
            $jurusan = Jurusan::findOrFail($this->jurusanId);
            $jurusan->update(['name' => $this->name]);
        } else {
            $this->validate();
            Jurusan::create(['name' => $this->name]);
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

        $jurusans = Jurusan::where('name', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.management.jurusan-management', [
            'jurusans' => $jurusans,
        ])->layout('layouts.app', ['title' => 'Manajemen Jurusan']);
    }
}
