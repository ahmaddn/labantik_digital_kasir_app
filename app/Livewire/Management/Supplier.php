<?php

namespace App\Livewire\Management;

use App\Models\Supplier as SupplierModel;
use Livewire\Component;
use Livewire\WithPagination;

class Supplier extends Component
{
    use WithPagination;
    
    #[Livewire\Attributes\Url]
    public $highlight = '';

    public string $name = '';
    public string $contact = '';
    public string $address = '';
    public string $note = '';
    public ?int $editingId = null;
    public string $search = '';

    public function saveSupplier(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'contact' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $data = [
            'name' => $this->name,
            'contact' => $this->contact,
            'address' => $this->address,
            'note' => $this->note,
        ];

        if ($this->editingId) {
            SupplierModel::find($this->editingId)?->update($data);
            $this->dispatch('toast', message: 'Supplier berhasil diperbarui.');
        } else {
            SupplierModel::create($data);
            $this->dispatch('toast', message: 'Supplier berhasil ditambahkan.');
        }

        $this->reset(['name', 'contact', 'address', 'note', 'editingId']);
    }

    public function editSupplier(int $id): void
    {
        $supplier = SupplierModel::findOrFail($id);
        $this->editingId = $id;
        $this->name = $supplier->name;
        $this->contact = $supplier->contact;
        $this->address = $supplier->address;
        $this->note = $supplier->note;
    }

    public function cancelEdit(): void
    {
        $this->reset(['name', 'contact', 'address', 'note', 'editingId']);
    }

    public function deleteSupplier(int $id): void
    {
        SupplierModel::destroy($id);
        $this->dispatch('toast', message: 'Supplier berhasil dihapus.');
    }

    public function render()
    {
        $query = SupplierModel::query();
        
        if ($this->highlight && !$this->search) {
            $query->where('id', $this->highlight);
        } elseif ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.management.supplier', [
            'suppliers' => $query->orderBy('name')->paginate(10)
        ])->layout('layouts.app', ['title' => 'Manajemen Supplier']);
    }
}
