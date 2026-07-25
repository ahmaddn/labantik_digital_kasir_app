<?php

namespace App\Livewire\Pos;

use App\Models\DailyRecap;
use App\Models\Jurusan;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Transaction;
use App\Services\PosQueryService;
use App\Services\PosSessionService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Kasir extends Component
{
    use WithPagination;

    public $transactionDate;

    public $products = [];

    // UI State
    public $rightSidebarTab = 'cart';

    // Stock Management Properties
    public $showOpeningStockModal = false;

    public $showClosingStockModal = false;

    public $stockItems = []; // Array to hold stock input values [product_id => quantity]

    public $lastClosingStocks = []; // Array to hold yesterday's closing stock [product_id => quantity]

    public $showDetailsModal = false;

    public $detailReference = null;

    public $showRecoveryModal = false;

    public $unfinishedSessionDate = null;

    public function refreshProducts(): void
    {
        $this->products = $this->getProductsForAlpine(app(PosQueryService::class))->toArray();
    }

    public function updatedTransactionDate(): void
    {
        $this->refreshProducts();
    }

    public function mount(PosSessionService $posSessionService): void
    {
        $this->transactionDate = now()->toDateString();
        $this->refreshProducts();

        $activeJurusanId = session('active_jurusan_id');
        // Check if session for today is already finished
        $isTodayFinished = DailyRecap::whereDate('date', now())
            ->where('jurusan_id', $activeJurusanId)
            ->where('actual_cash', '>', 0)
            ->exists();

        if ($isTodayFinished) {
            session()->flash('error', 'Sesi kasir hari ini telah berakhir. Anda tidak dapat melakukan transaksi lagi.');
            $this->redirectRoute('dashboard', navigate: true);

            return;
        }

        // Detect if there's an unfinished session from a previous day
        $this->detectUnfinishedSession($posSessionService);

        if (! $this->showRecoveryModal) {
            $this->checkOpeningStock($posSessionService);
        }
    }

    protected function getActiveProducts()
    {
        $activeJurusanId = session('active_jurusan_id');

        return Product::where('is_active', true)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();
    }

    protected function detectUnfinishedSession(PosSessionService $posSessionService): void
    {
        $today = now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        $unfinishedDate = $posSessionService->detectUnfinishedSession($today, $activeJurusanId);

        if ($unfinishedDate) {
            $this->unfinishedSessionDate = $unfinishedDate;
            $this->showRecoveryModal = true;
        }
    }

    public function fixUnfinishedSession(PosSessionService $posSessionService): void
    {
        if (! $this->unfinishedSessionDate) {
            return;
        }

        $activeJurusanId = session('active_jurusan_id');
        $posSessionService->fixUnfinishedSession($this->unfinishedSessionDate, $this->getActiveProducts(), $activeJurusanId);

        $this->showRecoveryModal = false;
        $this->unfinishedSessionDate = null;
        $this->dispatch('toast', message: 'Sesi sebelumnya berhasil dipulihkan & ditutup.');

        // Now proceed to today's opening stock
        $this->checkOpeningStock($posSessionService);
    }

    public function editOpeningStock(PosSessionService $posSessionService): void
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        $lastStocks = $posSessionService->getLastSessionStocks($today, $activeJurusanId);
        $this->lastClosingStocks = $lastStocks;

        $allProducts = $this->getActiveProducts();

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

    protected function checkOpeningStock(PosSessionService $posSessionService): void
    {
        $today = now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        $exists = StockEntry::where('date', $today)
            ->whereHas('product', function ($q) use ($activeJurusanId) {
                if ($activeJurusanId) {
                    $q->where('jurusan_id', $activeJurusanId);
                }
            })
            ->exists();

        if (! $exists) {
            $lastStocks = $posSessionService->getLastSessionStocks($today, $activeJurusanId);
            $this->lastClosingStocks = $lastStocks;

            $allProducts = $this->getActiveProducts();

            foreach ($allProducts as $p) {
                $this->stockItems[$p->id] = $lastStocks[$p->id] ?? 0;
            }
            $this->showOpeningStockModal = true;
        }
    }

    public function saveOpeningStock(PosSessionService $posSessionService): void
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $posSessionService->saveOpeningStock($this->stockItems, $today);

        $this->showOpeningStockModal = false;
        $this->stockItems = [];
        session()->flash('toast', 'Stok awal berhasil diperbarui.');
        $this->redirectRoute('kasir', navigate: true);
    }

    public function finishSession(): void
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $allProducts = Product::where('is_active', true)
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->get();

        $this->stockItems = [];
        foreach ($allProducts as $p) {
            $entry = StockEntry::where('product_id', $p->id)->where('date', $today)->first();
            $sold = Transaction::where('product_id', $p->id)
                ->whereDate('transacted_at', $today)
                ->sum('quantity');

            $this->stockItems[$p->id] = $entry ? ($entry->opening_stock - $sold) : 0;
        }

        $this->showClosingStockModal = true;
    }

    public function saveClosingStock(PosSessionService $posSessionService): void
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        $validProductIds = StockEntry::where('date', $today)
            ->where('opening_stock', '>', 0)
            ->pluck('product_id')
            ->toArray();

        $filteredStockItems = array_intersect_key($this->stockItems, array_flip($validProductIds));

        $posSessionService->saveClosingStock($filteredStockItems, $today, $activeJurusanId);

        $this->showClosingStockModal = false;
        $this->stockItems = [];

        session()->flash('toast', 'Sesi kasir berhasil diselesaikan.');
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function checkout(PosSessionService $posSessionService, $cart, $total, $change, $buyer_name, $status, $note, $transactionDate = null): void
    {
        if (empty($cart)) {
            return;
        }

        $activeJurusanId = session('active_jurusan_id');
        $themeSettings = session('active_jurusan_theme') ?? [];
        if (empty($themeSettings) && $activeJurusanId) {
            $jurusan = Jurusan::find($activeJurusanId);
            $themeSettings = $jurusan ? ($jurusan->theme_settings ?? []) : [];
        }
        $prefix = $themeSettings['doc_prefix_transaction'] ?? 'LBK';

        $reference = $posSessionService->checkout(
            $cart,
            $change,
            $buyer_name,
            $status,
            $note,
            $transactionDate,
            auth()->id(),
            $activeJurusanId,
            $prefix
        );

        $this->dispatch('transaction-completed', reference: $reference);
        $this->dispatch('toast', message: 'Transaksi berhasil disimpan!');

        // Check if total transaction of today reaches a multiple of 50,000
        $today = $transactionDate ?: now()->toDateString();
        $totalToday = Transaction::whereDate('transacted_at', $today)
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->sum('total_price');

        $checkoutAmount = (float) $total;
        $oldTotal = $totalToday - $checkoutAmount;

        $oldMultiple = floor($oldTotal / 50000);
        $newMultiple = floor($totalToday / 50000);

        if ($newMultiple > $oldMultiple && $newMultiple > 0) {
            $reachedAmount = $newMultiple * 50000;
            $this->dispatch('toast', 
                message: 'Total transaksi hari ini telah mencapai Rp' . number_format($reachedAmount, 0, ',', '.') . '. Segera cek uang tunai di laci!', 
                type: 'warning'
            );
        }

        $this->refreshProducts();
    }

    public function viewDetails(string $reference): void
    {
        $this->detailReference = $reference;
        $this->showDetailsModal = true;
    }

    public function getDetailItemsProperty()
    {
        if (! $this->detailReference) {
            return collect();
        }

        return Transaction::with('product')->where('reference', $this->detailReference)->get();
    }

    #[Computed]
    public function categories()
    {
        $posQueryService = app(PosQueryService::class);
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        return $posQueryService->getCategories($today, $activeJurusanId);
    }

    #[Computed]
    public function stockComparison()
    {
        $posQueryService = app(PosQueryService::class);
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        return $posQueryService->getStockComparison($today, $activeJurusanId);
    }

    #[Computed]
    public function recentTransactions()
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        return Transaction::query()
            ->whereDate('transacted_at', $today)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->selectRaw('reference, buyer_name, status, transacted_at, SUM(total_price) as total_amount, SUM(quantity) as total_qty')
            ->groupBy('reference', 'buyer_name', 'status', 'transacted_at')
            ->latest('transacted_at')
            ->limit(10)
            ->get();
    }



    protected function getProductsForAlpine(PosQueryService $posQueryService)
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        return $posQueryService->getProductsForAlpine($today, $activeJurusanId);
    }

    public function render(PosQueryService $posQueryService)
    {
        $today = now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');
        $allProducts = $this->getProductsForAlpine($posQueryService);

        $isSessionFinished = DailyRecap::where('date', $today)
            ->where('jurusan_id', $activeJurusanId)
            ->where('actual_cash', '>', 0)
            ->exists();

        return view('livewire.pos.kasir', [
            'allProductsJson' => $allProducts,
            'isSessionFinished' => $isSessionFinished,
        ])->layout('layouts.kasir');
    }
}
