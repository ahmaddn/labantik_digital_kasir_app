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

    // UI State
    public $rightSidebarTab = 'cart';

    // Stock Management Properties
    public $showOpeningStockModal = false;
    public $showClosingStockModal = false;
    public $stockItems = []; // Array to hold stock input values [product_id => quantity]
    public $lastClosingStocks = []; // Array to hold yesterday's closing stock [product_id => quantity]
    public $showDetailsModal = false;
    public $detailReference = null;

    public function mount(): void
    {
        // Check if session for today is already finished
        $isSessionFinished = DailyRecap::whereDate('date', now())
            ->where('actual_cash', '>', 0)
            ->exists();

        if ($isSessionFinished) {
            session()->flash('error', 'Sesi kasir hari ini telah berakhir. Anda tidak dapat melakukan transaksi lagi.');
            $this->redirectRoute('dashboard', navigate: true);
            return;
        }

        $this->checkOpeningStock();
    }

    public function editOpeningStock(): void
    {
        $today = now()->toDateString();
        
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
        $this->lastClosingStocks = $lastStocks;

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
            $this->lastClosingStocks = $lastStocks;
            
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
        $this->dispatch('stock-saved', products: $this->getProductsForAlpine());
    }

    public function finishSession(): void
    {
        $today = now()->toDateString();
        $allProducts = Product::where('is_active', true)->get();
        foreach ($allProducts as $p) {
            $entry = StockEntry::where('product_id', $p->id)->where('date', $today)->first();
            $sold = Transaction::where('product_id', $p->id)
                ->whereDate('transacted_at', $today)
                ->sum('quantity');
            
            $this->stockItems[$p->id] = $entry ? ($entry->opening_stock - $sold) : 0;
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

        // Mark session as finished in DailyRecap
        DailyRecap::updateOrCreate(
            ['date' => $today],
            ['actual_cash' => 1] // Set a placeholder value to mark as finished
        );

        $this->showClosingStockModal = false;
        $this->stockItems = [];
        
        session()->flash('toast', 'Sesi kasir berhasil diselesaikan.');
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function checkout($cart, $total, $change, $buyer_name, $status, $note): void
    {
        if (empty($cart)) return;

        $cleanName = preg_replace('/[^A-Za-z0-9]/', '', $buyer_name ?: 'GUEST');
        $initials = strtoupper(substr($cleanName, 0, 2));
        $reference = 'LBK-' . now()->format('Ymd') . '-' . $initials . strtoupper(bin2hex(random_bytes(2)));

        foreach ($cart as $item) {
            Transaction::create([
                'reference' => $reference,
                'user_id' => auth()->id(),
                'product_id' => $item['id'],
                'supplier_id' => $item['supplier_id'] ?? null,
                'transacted_at' => now(),
                'buyer_name' => $buyer_name ?: null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'unit_profit' => $item['profit'],
                'total_price' => $item['price'] * $item['quantity'],
                'debt_amount' => in_array($status, ['belum_menerima_uang', 'uang_dipinjam']) ? ($item['price'] * $item['quantity']) : 0,
                'change_due' => ($status === 'belum_kembalian') ? $change : 0,
                'status' => $status,
                'note' => $note ?: ($change > 0 ? 'Kembalian: Rp' . number_format($change, 0, ',', '.') : null),
            ]);
        }

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

    #[Computed]
    public function stockComparison()
    {
        $today = now()->toDateString();
        return Product::where('is_active', true)
            ->whereHas('stockEntries', function($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->with(['stockEntries' => fn($q) => $q->where('date', $today)])
            ->get()
            ->map(function($p) use ($today) {
                $entry = $p->stockEntries->first();
                $sold = Transaction::where('product_id', $p->id)
                    ->whereDate('transacted_at', $today)
                    ->sum('quantity');
                
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'category' => $p->category->name ?? 'Uncategorized',
                    'opening' => $entry ? $entry->opening_stock : 0,
                    'sold' => $sold,
                    'expected' => ($entry ? $entry->opening_stock : 0) - $sold,
                    'actual' => $entry ? $entry->closing_stock : 0,
                ];
            });
    }

    protected function getProductsForAlpine()
    {
        $today = now()->toDateString();
        return Product::with('category')
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
                    'profit' => (int)$p->profit,
                    'supplier_id' => $p->supplier_id,
                    'category_id' => $p->category_id,
                    'category_name' => $p->category->name ?? 'Uncategorized',
                    'initial' => substr($p->name, 0, 1),
                ];
            });
    }

    public function render()
    {
        $today = now()->toDateString();
        
        $allProducts = $this->getProductsForAlpine();

        $isSessionFinished = DailyRecap::where('date', $today)
            ->where('actual_cash', '>', 0)
            ->exists();

        return view('livewire.pos.kasir', [
            'allProductsJson' => $allProducts,
            'isSessionFinished' => $isSessionFinished
        ])->layout('layouts.kasir');
    }
}
