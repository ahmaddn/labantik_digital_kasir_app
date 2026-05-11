<?php

namespace App\Livewire;

use App\Models\ProductCategory;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;

    public $name;
    public $categoryId;
    public $isEditing = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:product_categories,name',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->name = '';
        $this->categoryId = null;
        $this->isEditing = false;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->isEditing) {
            $rules['name'] = 'required|string|max:255|unique:product_categories,name,' . $this->categoryId;
        }

        $this->validate($rules);

        if ($this->isEditing) {
            $category = ProductCategory::find($this->categoryId);
            $category->update([
                'name' => $this->name,
            ]);
            $this->dispatch('toast', message: 'Kategori berhasil diperbarui.');
        } else {
            ProductCategory::create([
                'name' => $this->name,
            ]);
            $this->dispatch('toast', message: 'Kategori baru berhasil ditambahkan.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        $category = ProductCategory::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            $this->dispatch('toast', message: 'Tidak dapat menghapus kategori yang masih memiliki produk.', type: 'error');
            return;
        }

        $category->delete();
        $this->dispatch('toast', message: 'Kategori berhasil dihapus.');
    }

    public function render()
    {
        $categories = ProductCategory::withCount('products')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.category-manager', [
            'categories' => $categories
        ])->layout('layouts.app', ['title' => 'Kategori Produk']);
    }
}
