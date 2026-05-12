<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\ProductCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class KasirMode extends Component
{
    use WithPagination;

    public $categories = [];
    public $cart = [];
    public $search = '';
    public $selectedCategory = null;
    public $total = 0;
    public $payment_amount = 0;
    public $change = 0;
    public $buyer_name = '';
    public $status = 'uang_diterima';
    public $note = '';
    
    // Modal filtering
    public $modalSearch = '';
    public $modalCategory = null;
    public $rightSidebarTab = 'cart'; // 'cart' or 'history'

    // Stock Management Properties
    public $showOpeningStockModal = false;
    public $showClosingStockModal = false;
    public $stockItems = []; // Array to hold stock input values [product_id => quantity]
    public $allProducts = []; // For the stock modals

    // Edit Transaction Properties
    public $showEditModal = false;
    public $editingTransaction = null;
    public $editQty = 0;
    public $editStatus = '';
    public $editNote = '';
    public $editBuyer = '';
    public $editChangeDue = 0;


    public function mount(): void
    {
        $this->categories = ProductCategory::all();
        $this->checkOpeningStock();
    }

    public function editOpeningStock(): void
    {
        $this->modalSearch = '';
        $this->modalCategory = null;
        $this->allProducts = Product::where('is_active', true)->orderBy('name')->get();
        $today = now()->toDateString();
        
        foreach ($this->allProducts as $p) {
            $entry = \App\Models\StockEntry::where('product_id', $p->id)->where('date', $today)->first();
            $this->stockItems[$p->id] = $entry ? $entry->opening_stock : 0;
        }
        
        $this->showOpeningStockModal = true;
    }

    protected function checkOpeningStock(): void
    {
        $today = now()->toDateString();
        $exists = \App\Models\StockEntry::where('date', $today)->exists();

        if (!$exists) {
            $this->modalSearch = '';
            $this->modalCategory = null;
            $this->allProducts = Product::where('is_active', true)->orderBy('name')->get();
            foreach ($this->allProducts as $p) {
                $this->stockItems[$p->id] = 0;
            }
            $this->showOpeningStockModal = true;
        }
    }

    public function saveOpeningStock(): void
    {
        $today = now()->toDateString();
        foreach ($this->stockItems as $productId => $qty) {
            $entry = \App\Models\StockEntry::updateOrCreate(
                ['product_id' => $productId, 'date' => $today],
                ['opening_stock' => $qty ?? 0]
            );

            // Recalculate closing stock based on sales today
            $totalSold = Transaction::where('product_id', $productId)
                ->whereDate('transacted_at', $today)
                ->sum('quantity');
            
            $entry->update([
                'closing_stock' => $entry->opening_stock - $totalSold
            ]);
        }
        $this->showOpeningStockModal = false;
        $this->stockItems = [];
        $this->dispatch('toast', message: 'Stok awal berhasil diperbarui.');
        $this->dispatch('stock-saved');
    }

    public function finishSession(): void
    {
        $this->modalSearch = '';
        $this->modalCategory = null;
        $this->allProducts = Product::where('is_active', true)->orderBy('name')->get();
        $today = now()->toDateString();
        
        foreach ($this->allProducts as $p) {
            $entry = \App\Models\StockEntry::where('product_id', $p->id)->where('date', $today)->first();
            $this->stockItems[$p->id] = $entry ? $entry->closing_stock : 0;
        }
        
        $this->showClosingStockModal = true;
    }

    public function saveClosingStock(): void
    {
        $today = now()->toDateString();
        foreach ($this->stockItems as $productId => $qty) {
            \App\Models\StockEntry::updateOrCreate(
                ['product_id' => $productId, 'date' => $today],
                ['closing_stock' => $qty ?? 0]
            );
        }
        $this->showClosingStockModal = false;
        $this->stockItems = [];
        $this->dispatch('toast', message: 'Sesi kasir berhasil diselesaikan.');
        $this->dispatch('session-finished');
    }


    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function selectCategory($id): void
    {
        $this->selectedCategory = $id;
        $this->resetPage();
    }

    public function updatedPaymentAmount(): void
    {
        $this->calculateChange();
    }

    public function addToCart($productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'modal_price' => $product->modal_price,
                'profit' => $product->profit,
                'supplier_id' => $product->supplier_id,
                'quantity' => 1,
            ];
        }
        $this->calculateTotal();
    }

    public function removeFromCart($productId): void
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
            } else {
                unset($this->cart[$productId]);
            }
        }
        $this->calculateTotal();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->total = 0;
        $this->payment_amount = 0;
        $this->change = 0;
    }

    protected function calculateTotal(): void
    {
        $this->total = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $this->calculateChange();
    }

    protected function calculateChange(): void
    {
        if ($this->payment_amount > 0) {
            $this->change = $this->payment_amount - $this->total;
        } else {
            $this->change = 0;
        }
    }

    public function checkout(): void
    {
        if (empty($this->cart)) return;

        foreach ($this->cart as $item) {
            Transaction::create([
                'product_id' => $item['id'],
                'supplier_id' => $item['supplier_id'] ?? null,
                'transacted_at' => now(),
                'buyer_name' => $this->buyer_name ?: null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'unit_profit' => $item['profit'],
                'total_price' => $item['price'] * $item['quantity'],
                'debt_amount' => in_array($this->status, ['belum_menerima_uang', 'uang_dipinjam']) ? ($item['price'] * $item['quantity']) : 0,
                'change_due' => ($this->status === 'belum_kembalian') ? $this->change : 0,
                'status' => $this->status,
                'note' => $this->note ?: ($this->change > 0 ? 'Kembalian: Rp' . number_format($this->change, 0, ',', '.') : null),
            ]);
        }

        $this->clearCart();
        $this->buyer_name = '';
        $this->status = 'uang_diterima';
        $this->note = '';
        $this->dispatch('toast', message: 'Transaksi berhasil disimpan!');
        $this->dispatch('transaction-complete');
    }

    public function setRightSidebarTab(string $tab): void
    {
        $this->rightSidebarTab = $tab;
    }

    public function editTransaction($id): void
    {
        $this->editingTransaction = Transaction::find($id);
        if (!$this->editingTransaction) return;

        $this->editQty = $this->editingTransaction->quantity;
        $this->editStatus = $this->editingTransaction->status;
        $this->editNote = $this->editingTransaction->note ?? '';
        $this->editBuyer = $this->editingTransaction->buyer_name ?? '';
        $this->editChangeDue = $this->editingTransaction->change_due ?? 0;
        $this->showEditModal = true;
    }

    public function updateTransaction(): void
    {
        if (!$this->editingTransaction) return;

        $totalPrice = $this->editingTransaction->unit_price * $this->editQty;
        
        $debt = in_array($this->editStatus, ['belum_menerima_uang', 'uang_dipinjam']) ? $totalPrice : 0;
        $changeDue = ($this->editStatus === 'belum_kembalian') ? $this->editChangeDue : 0;

        $this->editingTransaction->update([
            'quantity' => $this->editQty,
            'total_price' => $totalPrice,
            'status' => $this->editStatus,
            'note' => $this->editNote,
            'buyer_name' => $this->editBuyer ?: null,
            'debt_amount' => $debt,
            'change_due' => $changeDue
        ]);

        $this->showEditModal = false;
        $this->dispatch('toast', message: 'Transaksi berhasil diperbarui!');
    }

    // Computed Property for recent transactions
    #[Computed]
    public function recentTransactions()
    {
        return Transaction::with('product')
            ->latest('transacted_at')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $query = Product::where('is_active', true);
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.kasir-mode', [
            'products' => $query->orderBy('name')->paginate(20)
        ])->layout('layouts.kasir');
    }
}
