<?php

namespace App\Livewire\Management;

use App\Models\Jurusan;
use App\Models\ProductCategory;
use Livewire\Component;
use Livewire\WithPagination;

class Category extends Component
{
    use WithPagination;

    public $name;

    public $categoryId;

    public $isEditing = false;

    public $search = '';

    public $jurusan_id;

    public $filterJurusan = '';

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
        $this->jurusan_id = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->isEditing) {
            $rules['name'] = 'required|string|max:255|unique:product_categories,name,'.$this->categoryId;
        }

        $this->validate($rules);

        $activeJurusanId = session('active_jurusan_id');
        $data = [
            'name' => $this->name,
            'jurusan_id' => $activeJurusanId ?: ($this->jurusan_id ?: null),
        ];

        if ($this->isEditing) {
            $category = ProductCategory::find($this->categoryId);
            $category->update($data);
            $this->dispatch('toast', message: 'Kategori berhasil diperbarui.');
        } else {
            ProductCategory::create($data);
            $this->dispatch('toast', message: 'Kategori baru berhasil ditambahkan.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->jurusan_id = $category->jurusan_id;
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
        $activeJurusanId = session('active_jurusan_id');
        $categories = ProductCategory::withCount('products')
            ->where('name', 'like', '%'.$this->search.'%')
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where(function ($sq) use ($activeJurusanId) {
                    $sq->whereNull('jurusan_id')->orWhere('jurusan_id', $activeJurusanId);
                });
            })
            ->when(! $activeJurusanId && $this->filterJurusan, function ($q) {
                return $q->where('jurusan_id', $this->filterJurusan);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.management.category', [
            'categories' => $categories,
            'jurusans' => Jurusan::all(),
        ])->layout('layouts.app', ['title' => 'Kategori Produk']);
    }
}
