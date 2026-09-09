<?php

namespace App\Livewire\Management;

use App\Models\TefaApiKey;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class TefaApiKeyManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $name = '';
    public $isModalOpen = false;
    public $newlyGeneratedKey = null;

    protected $rules = [
        'name' => 'required|string|max:100',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'newlyGeneratedKey']);
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['name', 'newlyGeneratedKey']);
    }

    public function generateKey()
    {
        $this->validate();

        $rawKey = 'tfk_' . Str::random(56);
        $activeJurusanId = session('active_jurusan_id');

        TefaApiKey::create([
            'jurusan_id' => $activeJurusanId,
            'name'       => $this->name,
            'key'        => $rawKey,
            'is_active'  => true,
        ]);

        $this->newlyGeneratedKey = $rawKey;
        session()->flash('toast', "API Key '{$this->name}' berhasil dibuat!");
    }

    public function toggleActive($id)
    {
        $key = TefaApiKey::findOrFail($id);
        $key->update(['is_active' => ! $key->is_active]);

        $status = $key->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('toast', "API Key '{$key->name}' berhasil {$status}.");
    }

    public function deleteKey($id)
    {
        $key = TefaApiKey::findOrFail($id);
        $key->delete();

        session()->flash('toast', "API Key '{$key->name}' telah dihapus.");
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');

        $keys = TefaApiKey::with('jurusan')
            ->when($activeJurusanId, fn ($q) => $q->where(function ($sub) use ($activeJurusanId) {
                $sub->where('jurusan_id', $activeJurusanId)
                    ->orWhereNull('jurusan_id');
            }))
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.management.tefa-api-key-management', [
            'apiKeys' => $keys,
        ])->layout('layouts.app', ['title' => 'Manajemen API Key TEFA']);
    }
}
