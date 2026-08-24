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

    public bool $update_history = false;

    // Restock Assistant properties
    public $restockQty = '';
    public $totalModalCost = '';
    public string $activeTab = 'products'; // 'products' | 'grouping'

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
        try {
            $service->bulkDelete($this->selectedProducts);
            $this->dispatch('toast', message: $count.' produk berhasil dihapus.');
            $this->resetSelection();
            $this->showBulkDeleteModal = false;
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
            $this->showBulkDeleteModal = false;
        }
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

    public function updatedRestockQty()
    {
        $this->calculateFromRestock();
    }

    public function updatedTotalModalCost()
    {
        $this->calculateFromRestock();
    }

    protected function calculateFromRestock()
    {
        if (is_numeric($this->restockQty) && $this->restockQty > 0 && is_numeric($this->totalModalCost) && $this->totalModalCost >= 0) {
            $this->modal_price = round((float) $this->totalModalCost / (int) $this->restockQty);
            if (is_numeric($this->price)) {
                $this->profit_per_unit = (int) $this->price - (int) $this->modal_price;
            }
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
            'restockQty' => 'nullable|numeric|min:1',
            'totalModalCost' => 'nullable|numeric|min:0',
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

        $product = $service->saveProduct($this->editingId, $data, $this->update_history);

        // Save stock if Restock Qty is provided
        if (is_numeric($this->restockQty) && $this->restockQty > 0) {
            $today = today()->toDateString();
            $service->saveStock($product->id, $today, (int) $this->restockQty);
        }

        $msg = $this->editingId ? 'Produk berhasil diperbarui.' : 'Produk berhasil ditambahkan.';
        $this->dispatch('toast', message: $msg);

        $this->reset(['name', 'label', 'price', 'profit_per_unit', 'modal_price', 'category_id', 'supplier_id', 'is_active', 'editingId', 'jurusan_id', 'restockQty', 'totalModalCost', 'update_history']);
        $this->is_active = true;
        $this->showFormModal = false;
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'label', 'price', 'profit_per_unit', 'modal_price', 'category_id', 'supplier_id', 'editingId', 'jurusan_id', 'restockQty', 'totalModalCost', 'update_history']);
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
        $this->restockQty = '';
        $this->totalModalCost = '';
        $this->update_history = false;
        $this->showFormModal = true;
    }

    public function cancelEdit(): void
    {
        $this->reset(['name', 'label', 'price', 'profit_per_unit', 'modal_price', 'category_id', 'editingId', 'jurusan_id', 'restockQty', 'totalModalCost', 'update_history']);
        $this->is_active = true;
        $this->showFormModal = false;
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteProduct(ProductService $service): void
    {
        if ($this->deleteId) {
            try {
                $service->deleteProduct($this->deleteId);
                $this->dispatch('toast', message: 'Produk berhasil dihapus.');
            } catch (\Exception $e) {
                $this->dispatch('toast', message: $e->getMessage(), type: 'error');
            }
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

    public function getCashGroupedProducts()
    {
        $activeJurusanId = session('active_jurusan_id');
        $products = ProductModel::with(['category', 'supplier'])
            ->where('is_active', true)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('label', 'like', '%'.$this->search.'%')
                        ->orWhereHas('supplier', function ($subq) {
                            $subq->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->get();

        $grouped = [];

        foreach ($products as $product) {
            if ($product->supplier_id) {
                $categoryName = 'Penjualan ' . trim($product->supplier->name ?? 'Supplier');
            } else {
                $categoryNameClean = trim($product->category->name ?? 'Lainnya');
                $categoryNameLower = strtolower($categoryNameClean);
                if (in_array($categoryNameLower, ['makanan', 'minuman', 'makanan & minuman', 'makanan dan minuman', 'snack'])) {
                    $activeJurusan = Jurusan::find($product->jurusan_id);
                    $activeJurusanNameLower = $activeJurusan ? strtolower($activeJurusan->name) : '';
                    $categoryName = str_contains($activeJurusanNameLower, 'doku') ? 'Kas Doku' : 'Jurusan Snack & Minuman';
                } elseif (in_array($categoryNameLower, ['umum', 'lainnya', 'lain-lain'])) {
                    $categoryName = 'Keuntungan Jurusan';
                } else {
                    $categoryName = 'Penjualan ' . $categoryNameClean;
                }
            }

            $grouped[$categoryName][] = $product;
        }

        ksort($grouped);

        return $grouped;
    }

    public $selectedModifierGroupId = '';
    public bool $showBulkModifierModal = false;

    public function applyModifierGroupMassive()
    {
        $this->validate([
            'selectedModifierGroupId' => 'required|exists:modifier_groups,id',
        ]);

        if (empty($this->selectedProducts)) {
            $this->dispatch('toast', message: 'Silakan pilih produk terlebih dahulu.', type: 'danger');
            return;
        }

        $count = 0;
        foreach ($this->selectedProducts as $productId) {
            if ($productId) {
                $product = ProductModel::find($productId);
                if ($product) {
                    // Sync without detaching to append, or sync to replace depending on needs.
                    // SyncWithoutDetaching is better to preserve other groups.
                    $product->modifierGroups()->syncWithoutDetaching([$this->selectedModifierGroupId]);
                    $count++;
                }
            }
        }

        $this->selectedProducts = [];
        $this->selectAll = false;
        $this->showBulkModifierModal = false;
        $this->selectedModifierGroupId = '';

        $this->dispatch('toast', message: "Berhasil menerapkan kelompok topping ke {$count} produk.");
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $query = ProductModel::with(['category', 'supplier', 'jurusan', 'modifierGroups'])
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

        $modifierGroups = \App\Models\ModifierGroup::when($activeJurusanId, function($q) use ($activeJurusanId) {
            $q->where('jurusan_id', $activeJurusanId);
        })->get();

        return view('livewire.management.product', [
            'products' => $query->paginate(10),
            'cashGroupedProducts' => $this->activeTab === 'grouping' ? $this->getCashGroupedProducts() : [],
            'modifierGroupsList' => $modifierGroups,
        ])->layout('layouts.app', ['title' => 'Katalog Produk']);
    }
}
