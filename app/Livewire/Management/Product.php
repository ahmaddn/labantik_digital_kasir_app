<?php

namespace App\Livewire\Management;

use App\Models\Jurusan;
use App\Models\Product as ProductModel;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Services\ProductService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Product extends Component
{
    use WithPagination;

    #[Url]
    public $highlight = '';

    // Form produk
    public string $name = '';

    public string $label = '';

    public $price = '';

    public $profit_per_unit = '';

    public $modal_price = 0; // Displayed in UI

    public $category_id = '';

    public $supplier_id = '';

    public bool $is_active = true;

    public ?string $editingId = null;

    // Stok harian
    public $stock_product_id = '';

    public int $opening_stock = 0;

    public string $stock_date = '';

    // State
    public $categories = [];

    public $suppliers = [];

    public string $successMessage = '';

    public string $tab = 'products'; // 'products' | 'stock'

    public bool $showDeleteModal = false;

    public bool $showBulkDeleteModal = false;

    public bool $showFormModal = false;

    public ?string $deleteId = null;

    public string $filterCategory = '';

    public string $filterJurusan = '';

    public string $search = '';

    public array $selectedProducts = [];

    public bool $selectAll = false;

    public $jurusans = [];

    public $jurusan_id = '';

    public function mount(): void
    {
        $activeJurusanId = session('active_jurusan_id');
        $this->categories = ProductCategory::where(function ($q) use ($activeJurusanId) {
            $q->whereNull('jurusan_id');
            if ($activeJurusanId) {
                $q->orWhere('jurusan_id', $activeJurusanId);
            }
        })->get();
        $this->suppliers = Supplier::all();
        $this->jurusans = Jurusan::all();
        $this->stock_date = today()->toDateString();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedFilterJurusan(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedProducts = ProductModel::when($this->filterCategory, function ($q) {
                return $q->where('category_id', $this->filterCategory);
            })
                ->when($this->search, function ($q) {
                    return $q->where('name', 'like', '%'.$this->search.'%');
                })
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function resetSelection(): void
    {
        $this->selectedProducts = [];
        $this->selectAll = false;
    }

    public function confirmBulkDelete(): void
    {
        if (empty($this->selectedProducts)) {
            return;
        }
        $this->showBulkDeleteModal = true;
    }

    public function cancelBulkDelete(): void
    {
        $this->showBulkDeleteModal = false;
    }

    public function bulkDelete(ProductService $service): void
    {
        if (empty($this->selectedProducts)) {
            return;
        }

        $count = count($this->selectedProducts);
        $service->bulkDelete($this->selectedProducts);

        $this->dispatch('toast', message: $count.' produk berhasil dihapus.');
        $this->resetSelection();
        $this->showBulkDeleteModal = false;
    }

    public function bulkToggleStatus(ProductService $service): void
    {
        if (empty($this->selectedProducts)) {
            return;
        }

        $count = count($this->selectedProducts);
        $newStatus = $service->bulkToggleStatus($this->selectedProducts);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        $this->dispatch('toast', message: $count." produk berhasil $statusText.");
        $this->resetSelection();
    }

    public function updatedPrice(): void
    {
        if (is_numeric($this->price) && is_numeric($this->modal_price)) {
            $this->profit_per_unit = (int) $this->price - (int) $this->modal_price;
        }
        $this->updateLabel();
    }

    public function updatedProfitPerUnit(): void
    {
        if (is_numeric($this->price) && is_numeric($this->profit_per_unit)) {
            $this->modal_price = (int) $this->price - (int) $this->profit_per_unit;
        }
    }

    public function updatedModalPrice(): void
    {
        if (is_numeric($this->price) && is_numeric($this->modal_price)) {
            $this->profit_per_unit = (int) $this->price - (int) $this->modal_price;
        }
    }

    public function updatedName(): void
    {
        $this->updateLabel();
    }

    protected function calculateModal(): void
    {
        if (is_numeric($this->price) && is_numeric($this->profit_per_unit)) {
            $this->modal_price = (int) $this->price - (int) $this->profit_per_unit;
        }
    }

    protected function updateLabel(): void
    {
        if ($this->name && is_numeric($this->price)) {
            $this->label = $this->name.' - Rp'.number_format((int) $this->price, 0, ',', '.');
        }
    }

    public function saveProduct(ProductService $service): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:1',
            'profit_per_unit' => 'required|numeric|min:0',
            'category_id' => 'required|exists:product_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'jurusan_id' => 'nullable|exists:jurusans,id',
        ]);

        $this->calculateModal();
        $this->updateLabel();

        $activeJurusanId = session('active_jurusan_id');
        $data = [
            'jurusan_id' => $activeJurusanId ?: ($this->jurusan_id ?: null),
            'name' => $this->name,
            'label' => $this->label,
            'price' => (int) $this->price,
            'modal_price' => $this->modal_price,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id ?: null,
            'is_active' => (bool) $this->is_active,
        ];

        $service->saveProduct($this->editingId, $data);

        $msg = $this->editingId ? 'Produk berhasil diperbarui.' : 'Produk berhasil ditambahkan.';
        $this->dispatch('toast', message: $msg);

        $this->reset(['name', 'label', 'price', 'profit_per_unit', 'modal_price', 'category_id', 'supplier_id', 'is_active', 'editingId', 'jurusan_id']);
        $this->is_active = true;
        $this->showFormModal = false;
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'label', 'price', 'profit_per_unit', 'modal_price', 'category_id', 'supplier_id', 'editingId', 'jurusan_id']);
        $this->is_active = true;
        $this->showFormModal = true;
    }

    public function editProduct(string $id): void
    {
        $product = ProductModel::findOrFail($id);
        $this->editingId = $id;
        $this->name = $product->name;
        $this->label = $product->label;
        $this->price = $product->price;
        $this->profit_per_unit = $product->price - $product->modal_price;
        $this->modal_price = $product->modal_price;
        $this->category_id = $product->category_id;
        $this->supplier_id = $product->supplier_id;
        $this->jurusan_id = $product->jurusan_id;
        $this->is_active = (bool) $product->is_active;
        $this->showFormModal = true;
    }

    public function cancelEdit(): void
    {
        $this->reset(['name', 'label', 'price', 'profit_per_unit', 'modal_price', 'category_id', 'editingId', 'jurusan_id']);
        $this->is_active = true;
        $this->showFormModal = false;
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteProduct(): void
    {
        if ($this->deleteId) {
            ProductModel::destroy($this->deleteId);
            $this->dispatch('toast', message: 'Produk berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function saveStock(ProductService $service): void
    {
        $this->validate([
            'stock_product_id' => 'required|exists:products,id',
            'opening_stock' => 'required|integer|min:0',
            'stock_date' => 'required|date',
        ]);

        $service->saveStock($this->stock_product_id, $this->stock_date, $this->opening_stock);

        $this->dispatch('toast', message: 'Stok awal berhasil disimpan.');
        $this->reset(['stock_product_id', 'opening_stock']);
        $this->stock_date = today()->toDateString();
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $query = ProductModel::with(['category', 'supplier', 'jurusan'])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->when(! $activeJurusanId && $this->filterJurusan, function ($q) {
                return $q->where('jurusan_id', $this->filterJurusan);
            })
            ->orderBy('name');

        if ($this->highlight && ! $this->search) {
            $query->where('id', $this->highlight);
        } elseif ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('label', 'like', '%'.$this->search.'%')
                    ->orWhereHas('supplier', function ($sq) {
                        $sq->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        return view('livewire.management.product', [
            'products' => $query->paginate(10),
        ])->layout('layouts.app', ['title' => 'Katalog Produk']);
    }
}
