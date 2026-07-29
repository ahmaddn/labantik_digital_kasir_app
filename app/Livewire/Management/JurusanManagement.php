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
        if (session('active_role_name') === 'pengelola' && is_null($jurusan->parent_id)) {
            $this->dispatch('toast', message: 'Akses Ditolak: Pengelola hanya dapat mengedit Sub-Unit.');
            return;
        }
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
        if (session('active_role_name') === 'pengelola' && empty($this->parent_id)) {
            $this->addError('parent_id', 'Pengelola wajib memilih Unit Induk (Parent TEFA).');
            return;
        }

        if ($this->jurusanId) {
            $this->validate([
                'name' => 'required|string|max:255|unique:jurusans,name,'.$this->jurusanId,
                'parent_id' => 'nullable|exists:jurusans,id|different:jurusanId',
            ]);
            $jurusan = Jurusan::findOrFail($this->jurusanId);
            if (session('active_role_name') === 'pengelola' && is_null($jurusan->parent_id)) {
                abort(403, 'Akses Ditolak: Pengelola tidak dapat mengedit Jurusan Induk.');
            }
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
        $jurusan = Jurusan::findOrFail($id);
        if (session('active_role_name') === 'pengelola' && is_null($jurusan->parent_id)) {
            $this->dispatch('toast', message: 'Akses Ditolak: Pengelola hanya dapat menghapus Sub-Unit.');
            return;
        }
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteJurusan()
    {
        if ($this->deleteId) {
            $jurusan = Jurusan::findOrFail($this->deleteId);
            if (session('active_role_name') === 'pengelola' && is_null($jurusan->parent_id)) {
                abort(403, 'Akses Ditolak: Pengelola tidak dapat menghapus Jurusan Induk.');
            }
            $jurusan->delete();
            $this->dispatch('toast', message: 'Data jurusan berhasil dihapus.');
            $this->showDeleteModal = false;
            $this->deleteId = null;
        }
    }

    public function render()
    {
        if (session('active_role_name') !== 'superadmin' && session('active_role_name') !== 'pengelola') {
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
