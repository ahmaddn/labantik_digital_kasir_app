<?php

namespace App\Livewire\Management;

use App\Models\Product as ProductModel;
use App\Models\ProductCategory;
use App\Models\StockEntry;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Product extends Component
{
    use WithPagination;
    
    #[\Livewire\Attributes\Url]
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
    public ?int $editingId = null;

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
    public ?int $deleteId = null;
    public string $filterCategory = '';
    public string $search = '';
    public array $selectedProducts = [];
    public bool $selectAll = false;


    public function mount(): void
    {
        $this->categories = ProductCategory::all();
        $this->suppliers = Supplier::all();
        $this->stock_date = today()->toDateString();
    }

    public function updatedFilterCategory(): void
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
            $this->selectedProducts = ProductModel::when($this->filterCategory, function($q) {
                    return $q->where('category_id', $this->filterCategory);
                })
                ->when($this->search, function($q) {
                    return $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->pluck('id')
                ->map(fn($id) => (string)$id)
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

    public function bulkDelete(): void
    {
        if (empty($this->selectedProducts)) return;
        
        ProductModel::whereIn('id', $this->selectedProducts)->delete();
        $this->dispatch('toast', message: count($this->selectedProducts) . ' produk berhasil dihapus.');
        $this->resetSelection();
    }

    public function bulkToggleStatus(): void
    {
        if (empty($this->selectedProducts)) return;

        $products = ProductModel::whereIn('id', $this->selectedProducts)->get();
        
        // If all are active, make them inactive. Otherwise, make them all active.
        $allActive = $products->every(fn($p) => $p->is_active);
        $newStatus = !$allActive;

        ProductModel::whereIn('id', $this->selectedProducts)->update(['is_active' => $newStatus]);
        
        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        $this->dispatch('toast', message: count($this->selectedProducts) . " produk berhasil $statusText.");
        $this->resetSelection();
    }


    public function updatedPrice(): void
    {
        // When price changes, we prioritize keeping modal_price and updating profit
        if (is_numeric($this->price) && is_numeric($this->modal_price)) {
            $this->profit_per_unit = (int)$this->price - (int)$this->modal_price;
        }
        $this->updateLabel();
    }

    public function updatedProfitPerUnit(): void
    {
        if (is_numeric($this->price) && is_numeric($this->profit_per_unit)) {
            $this->modal_price = (int)$this->price - (int)$this->profit_per_unit;
        }
    }

    public function updatedModalPrice(): void
    {
        if (is_numeric($this->price) && is_numeric($this->modal_price)) {
            $this->profit_per_unit = (int)$this->price - (int)$this->modal_price;
        }
    }

    public function updatedName(): void
    {
        $this->updateLabel();
    }

    protected function calculateModal(): void
    {
        // This is now handled in updated hooks for better control, 
        // but kept for saveProduct fallback if needed
        if (is_numeric($this->price) && is_numeric($this->profit_per_unit)) {
            $this->modal_price = (int)$this->price - (int)$this->profit_per_unit;
        }
    }

    protected function updateLabel(): void
    {
        if ($this->name && is_numeric($this->price)) {
            $this->label = $this->name . ' - Rp' . number_format((int)$this->price, 0, ',', '.');
        }
    }

    public function saveProduct(): void
    {
        $this->validate([
            'name'            => 'required|string|max:100',
            'price'           => 'required|numeric|min:1',
            'profit_per_unit' => 'required|numeric|min:0',
            'category_id'     => 'required|exists:product_categories,id',
            'supplier_id'     => 'nullable|exists:suppliers,id',
        ]);

        $this->calculateModal();
        $this->updateLabel();

        $data = [
            'name'        => $this->name,
            'label'       => $this->label,
            'price'       => (int)$this->price,
            'modal_price' => $this->modal_price,
            'category_id' => (int)$this->category_id,
            'supplier_id' => $this->supplier_id ? (int)$this->supplier_id : null,
            'is_active'   => (bool)$this->is_active,
        ];

        if ($this->editingId) {
            $product = ProductModel::find($this->editingId);
            if ($product) {
                $product->update($data);
                $this->dispatch('toast', message: 'Produk berhasil diperbarui.');
            }
        } else {
            ProductModel::create($data);
            $this->dispatch('toast', message: 'Produk berhasil ditambahkan.');
        }

        $this->reset(['name', 'label', 'price', 'profit_per_unit', 'modal_price', 'category_id', 'supplier_id', 'is_active', 'editingId']);
        $this->is_active = true;
    }

    public function editProduct(int $id): void
    {
        $product = ProductModel::findOrFail($id);
        $this->editingId       = $id;
        $this->name            = $product->name;
        $this->label           = $product->label;
        $this->price           = $product->price;
        $this->profit_per_unit = $product->price - $product->modal_price;
        $this->modal_price     = $product->modal_price;
        $this->category_id     = $product->category_id;
        $this->supplier_id     = $product->supplier_id;
        $this->is_active       = (bool)$product->is_active;
    }

    public function cancelEdit(): void
    {
        $this->reset(['name', 'label', 'price', 'profit_per_unit', 'modal_price', 'category_id', 'editingId']);
        $this->is_active = true;
    }

    public function confirmDelete(int $id): void
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

    public function saveStock(): void
    {
        $this->validate([
            'stock_product_id' => 'required|exists:products,id',
            'opening_stock'    => 'required|integer|min:0',
            'stock_date'       => 'required|date',
        ]);

        StockEntry::updateOrCreate(
            [
                'product_id' => $this->stock_product_id,
                'date'       => $this->stock_date,
            ],
            [
                'opening_stock' => $this->opening_stock,
                'closing_stock' => $this->opening_stock,
            ]
        );

        $this->dispatch('toast', message: 'Stok awal berhasil disimpan.');
        $this->reset(['stock_product_id', 'opening_stock']);
        $this->stock_date = today()->toDateString();
    }

    public function render()
    {
        $query = ProductModel::with(['category', 'supplier'])->orderBy('name');
        
        if ($this->highlight && !$this->search) {
            $query->where('id', $this->highlight);
        } elseif ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('label', 'like', '%' . $this->search . '%')
                  ->orWhereHas('supplier', function($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return view('livewire.management.product', [
            'products' => $query->paginate(10)
        ])->layout('layouts.app', ['title' => 'Katalog Produk']);
    }
}
