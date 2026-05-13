<?php

namespace App\Livewire\Pos;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\ProductCategory;
use App\Models\StockEntry;
use App\Models\DailyRecap;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Kasir extends Component
{
    use WithPagination;

    public $cart = [];
    public $total = 0;
    public $payment_amount = 0;
    public $change = 0;
    public $buyer_name = '';
    public $status = 'uang_diterima';
    public $note = '';
    
    // UI State (Moved to Alpine but kept here for initial sync if needed)
    public $rightSidebarTab = 'cart'; // 'cart' or 'history'

    // Stock Management Properties
    public $showOpeningStockModal = false;
    public $showClosingStockModal = false;
    public $stockItems = []; // Array to hold stock input values [product_id => quantity]
    public $showDetailsModal = false;
    public $detailReference = null;

    public function mount(): void
    {
        $this->checkOpeningStock();
    }

    public function editOpeningStock(): void
    {
        $today = now()->toDateString();
        
        // Cari data terakhir dari hari sebelumnya untuk fallback jika belum ada data hari ini
        $lastSessionDate = StockEntry::where('date', '<', $today)
            ->orderBy('date', 'desc')
            ->value('date');
        
        $lastStocks = [];
        if ($lastSessionDate) {
            $lastStocks = StockEntry::where('date', $lastSessionDate)
                ->pluck('closing_stock', 'product_id')
                ->toArray();
        }

        $allProducts = Product::where('is_active', true)->get();
        foreach ($allProducts as $p) {
            $entry = StockEntry::where('product_id', $p->id)->where('date', $today)->first();
            if ($entry) {
                $this->stockItems[$p->id] = $entry->opening_stock;
            } else {
                $this->stockItems[$p->id] = $lastStocks[$p->id] ?? 0;
            }
        }
        
        $this->showOpeningStockModal = true;
    }

    protected function checkOpeningStock(): void
    {
        $today = now()->toDateString();
        $exists = StockEntry::where('date', $today)->exists();

        if (!$exists) {
            // Cari data terakhir dari hari sebelumnya
            $lastSessionDate = StockEntry::where('date', '<', $today)
                ->orderBy('date', 'desc')
                ->value('date');

            $lastStocks = [];
            if ($lastSessionDate) {
                $lastStocks = StockEntry::where('date', $lastSessionDate)
                    ->pluck('closing_stock', 'product_id')
                    ->toArray();
            }

            $allProducts = Product::where('is_active', true)->get();
            foreach ($allProducts as $p) {
                $this->stockItems[$p->id] = $lastStocks[$p->id] ?? 0;
            }
            $this->showOpeningStockModal = true;
        }
    }

    public function saveOpeningStock(): void
    {
        $today = now()->toDateString();
        foreach ($this->stockItems as $productId => $qty) {
            $entry = StockEntry::updateOrCreate(
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
        $today = now()->toDateString();
        $allProducts = Product::where('is_active', true)->get();
        foreach ($allProducts as $p) {
            $entry = StockEntry::where('product_id', $p->id)->where('date', $today)->first();
            $this->stockItems[$p->id] = $entry ? $entry->closing_stock : 0;
        }
        
        $this->showClosingStockModal = true;
    }

    public function saveClosingStock(): void
    {
        $today = now()->toDateString();
        foreach ($this->stockItems as $productId => $qty) {
            StockEntry::updateOrCreate(
                ['product_id' => $productId, 'date' => $today],
                ['closing_stock' => $qty ?? 0]
            );
        }
        $this->showClosingStockModal = false;
        $this->stockItems = [];
        $this->dispatch('toast', message: 'Sesi kasir berhasil diselesaikan.');
        $this->dispatch('session-finished');
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

        $name = preg_replace('/[^A-Za-z0-9]/', '', $this->buyer_name ?: 'GUEST');
        $initials = strtoupper(substr($name, 0, 2));
        $reference = 'LBK-' . now()->format('Ymd') . '-' . $initials . strtoupper(bin2hex(random_bytes(2)));

        foreach ($this->cart as $item) {
            Transaction::create([
                'reference' => $reference,
                'user_id' => auth()->id(),
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

    public function viewDetails($reference): void
    {
        $this->detailReference = $reference;
        $this->showDetailsModal = true;
    }

    public function getDetailItemsProperty()
    {
        if (!$this->detailReference) return collect();
        return Transaction::with('product')->where('reference', $this->detailReference)->get();
    }

    #[Computed]
    public function categories()
    {
        return ProductCategory::orderBy('name')->get();
    }

    #[Computed]
    public function recentTransactions()
    {
        return Transaction::query()
            ->whereDate('transacted_at', today())
            ->selectRaw('reference, buyer_name, status, transacted_at, SUM(total_price) as total_amount, SUM(quantity) as total_qty')
            ->groupBy('reference', 'buyer_name', 'status', 'transacted_at')
            ->latest('transacted_at')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $today = now()->toDateString();
        
        // Get ALL products for JS filtering
        $allProducts = Product::with('category')
            ->where('is_active', true)
            ->whereHas('stockEntries', function($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->orderBy('name')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (int)$p->price,
                    'category_id' => $p->category_id,
                    'category_name' => $p->category->name ?? 'Uncategorized',
                    'initial' => substr($p->name, 0, 1),
                ];
            });

        $isSessionFinished = DailyRecap::where('date', $today)
            ->where('actual_cash', '>', 0)
            ->exists();

        return view('livewire.pos.kasir', [
            'allProductsJson' => $allProducts,
            'isSessionFinished' => $isSessionFinished
        ])->layout('layouts.kasir');
    }
}
