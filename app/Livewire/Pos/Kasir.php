<?php

namespace App\Livewire\Pos;

use App\Models\DailyRecap;
use App\Models\Jurusan;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Transaction;
use App\Models\CashierSchedule;
use App\Models\CashierAttendance;
use App\Models\CashierTask;
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

    // Attendance & Tasks UI State
    public $showOpeningAttendanceModal = false;
    public $openingCash = '';
    
    // Closing attendance fields (added to closing modal)
    public $closingCashInput = '';
    public $closingReportText = '';

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
        
        // 1. Validate if user is scheduled for today (mandatory for cashier role)
        if (session('active_role_name') === 'kasir') {
            $isScheduled = CashierSchedule::where('user_id', auth()->id())
                ->where('jurusan_id', $activeJurusanId)
                ->where('date', now()->toDateString())
                ->exists();

            if (!$isScheduled) {
                session()->flash('error', 'Anda tidak memiliki jadwal jaga kasir hari ini.');
                $this->redirectRoute('dashboard', navigate: true);
                return;
            }

            // 2. Check if already clocked in for today
            $attendance = CashierAttendance::where('user_id', auth()->id())
                ->where('jurusan_id', $activeJurusanId)
                ->where('date', now()->toDateString())
                ->first();

            if (!$attendance) {
                // Must clock in first
                $this->showOpeningAttendanceModal = true;
            }
        }

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

        if (! $this->showRecoveryModal && !$this->showOpeningAttendanceModal) {
            $this->checkOpeningStock($posSessionService);
        }
    }

    public function saveOpeningAttendance(PosSessionService $posSessionService): void
    {
        $this->validate([
            'openingCash' => 'required|numeric|min:0',
        ]);

        $activeJurusanId = session('active_jurusan_id');
        $schedule = CashierSchedule::where('user_id', auth()->id())
            ->where('jurusan_id', $activeJurusanId)
            ->where('date', now()->toDateString())
            ->first();

        CashierAttendance::create([
            'cashier_schedule_id' => $schedule ? $schedule->id : null,
            'user_id' => auth()->id(),
            'jurusan_id' => $activeJurusanId,
            'date' => now()->toDateString(),
            'clock_in' => now(),
            'opening_cash' => (float)$this->openingCash,
            'status' => 'present',
        ]);

        $this->showOpeningAttendanceModal = false;
        $this->dispatch('toast', message: 'Absen buka berhasil dicatat.');

        // Proceed to opening stock check
        $this->checkOpeningStock($posSessionService);
    }

    public function toggleTask($taskId): void
    {
        $task = CashierTask::where('assigned_to', auth()->id())
            ->where('id', $taskId)
            ->first();

        if ($task) {
            $wasCompleted = $task->is_completed;
            $task->update([
                'is_completed' => !$wasCompleted,
                'completed_at' => !$wasCompleted ? now() : null,
            ]);

            $user = auth()->user();
            if ($user) {
                if (!$wasCompleted) {
                    $user->increment('pending_points', 10);
                    $user->increment('streak', 1);
                } else {
                    $user->decrement('pending_points', 10);
                    $user->decrement('streak', 1);
                    if ($user->pending_points < 0) {
                        $user->pending_points = 0;
                    }
                    if ($user->streak < 0) {
                        $user->streak = 0;
                    }
                    $user->save();
                }
            }

            $this->dispatch('toast', message: 'Status tugas diperbarui.');
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
        $this->validate([
            'closingCashInput' => 'required|numeric|min:0',
            'closingReportText' => 'required|string',
        ]);

        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        $validProductIds = StockEntry::where('date', $today)
            ->where('opening_stock', '>', 0)
            ->pluck('product_id')
            ->toArray();

        $filteredStockItems = array_intersect_key($this->stockItems, array_flip($validProductIds));

        // Save stock
        $posSessionService->saveClosingStock($filteredStockItems, $today, $activeJurusanId);

        // Record Closing Attendance
        $attendance = CashierAttendance::where('user_id', auth()->id())
            ->where('jurusan_id', $activeJurusanId)
            ->where('date', now()->toDateString())
            ->first();

        if ($attendance) {
            $attendance->update([
                'clock_out' => now(),
                'closing_cash' => (float)$this->closingCashInput,
                'closing_report' => $this->closingReportText,
            ]);
        }

        // Post closing cash to daily recap
        DailyRecap::updateOrCreate(
            ['date' => $today, 'jurusan_id' => $activeJurusanId],
            ['actual_cash' => (float)$this->closingCashInput]
        );

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

    public function saveQuickExpense($amount, $categoryId, $description): void
    {
        $activeJurusanId = session('active_jurusan_id');
        
        \App\Models\CashTransaction::create([
            'jurusan_id' => $activeJurusanId,
            'date' => $this->transactionDate ?: now()->toDateString(),
            'cash_type' => 'modal',
            'cash_category_id' => $categoryId,
            'type' => 'expense',
            'amount' => (float) $amount,
            'description' => trim($description) . ' (Sistem - Pengeluaran Cepat)',
        ]);

        // Invalidate cache
        \Illuminate\Support\Facades\Cache::forget('cash_balances_' . ($activeJurusanId ?: 'global'));

        $this->dispatch('toast', message: 'Pengeluaran berhasil dicatat!');
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

        $categories = \App\Models\CashCategory::where('jurusan_id', $activeJurusanId)->get();

        // Get daily tasks for the logged in cashier
        $dailyTasks = CashierTask::where('assigned_to', auth()->id())
            ->where('date', now()->toDateString())
            ->get();

        return view('livewire.pos.kasir', [
            'allProductsJson' => $allProducts,
            'isSessionFinished' => $isSessionFinished,
            'categories' => $categories,
            'dailyTasks' => $dailyTasks,
        ])->layout('layouts.kasir');
    }
}
