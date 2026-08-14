<?php

namespace App\Livewire\Pos;

use App\Models\CashCategory;
use App\Models\CashierAttendance;
use App\Models\CashierSchedule;
use App\Models\CashierTaskDefinition;
use App\Models\CashierTaskAssignment;
use App\Models\CashTransaction;
use App\Models\DailyRecap;
use App\Models\Jurusan;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Transaction;
use App\Services\PosQueryService;
use App\Services\PosSessionService;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Kasir extends Component
{
    use WithPagination;

    public $transactionDate;

    // For new task notifications polling
    public $lastTaskCheckAt;

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

    // Closing report properties
    public bool $showClosingReportModal = false;

    public string $closingReportText = '';

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

        // initialize task polling timestamp
        $this->lastTaskCheckAt = now();

        $activeJurusanId = session('active_jurusan_id');

        // 1. Validate if user is scheduled for today (mandatory for cashier role, unless they have a higher role)
        if (session('active_role_name') === 'kasir') {
            $hasHigherRole = auth()->user()->roles()
                ->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])
                ->exists();

            if (! $hasHigherRole) {
                $activeJurusan = Jurusan::find($activeJurusanId);
                $allowedJurusanIds = [$activeJurusanId];
                if ($activeJurusan && $activeJurusan->parent_id) {
                    $allowedJurusanIds[] = $activeJurusan->parent_id;
                }

                $isScheduled = CashierSchedule::where('user_id', auth()->id())
                    ->whereIn('jurusan_id', $allowedJurusanIds)
                    ->where('date', now()->toDateString())
                    ->exists();

                if (! $isScheduled) {
                    session()->flash('error', 'Anda tidak memiliki jadwal jaga kasir hari ini.');
                    $this->redirectRoute('dashboard', navigate: true);

                    return;
                }

                // 2. Check if already clocked in/out for today
                $attendance = CashierAttendance::where('user_id', auth()->id())
                    ->where('jurusan_id', $activeJurusanId)
                    ->where('date', now()->toDateString())
                    ->first();

                if ($attendance && $attendance->clock_out) {
                    session()->flash('error', 'Anda sudah melakukan clock-out untuk hari ini.');
                    $this->redirectRoute('dashboard', navigate: true);

                    return;
                }

                if (! $attendance) {
                    // Auto clock-in: first POS entry of the day counts as clock in
                    $schedule = CashierSchedule::where('user_id', auth()->id())
                        ->whereIn('jurusan_id', $allowedJurusanIds)
                        ->where('date', now()->toDateString())
                        ->first();

                    $currentTime = now();
                    $isLate = false;
                    $deducted = 0;

                    $jurusan = Jurusan::find($activeJurusanId);
                    $settings = $jurusan ? ($jurusan->theme_settings ?: []) : [];
                    $targetClockIn = $settings['clock_in_time'] ?? '07:00';
                    $penalty = (int) ($settings['late_clock_in_penalty'] ?? 0);

                    if ($targetClockIn) {
                        try {
                            $targetTime = \Carbon\Carbon::createFromFormat('H:i', $targetClockIn);
                            $targetTime->setDate($currentTime->year, $currentTime->month, $currentTime->day);
                            if ($currentTime->gt($targetTime)) {
                                $isLate = true;
                                if ($penalty > 0) {
                                    $deducted = $penalty;
                                    auth()->user()->decrement('points', $penalty);
                                    if (auth()->user()->points < 0) {
                                        auth()->user()->update(['points' => 0]);
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // ignore format error
                        }
                    }

                    CashierAttendance::create([
                        'cashier_schedule_id' => $schedule ? $schedule->id : null,
                        'user_id' => auth()->id(),
                        'jurusan_id' => $activeJurusanId,
                        'date' => now()->toDateString(),
                        'clock_in' => $currentTime,
                        'status' => $isLate ? 'late' : 'present',
                    ]);

                    if ($isLate && $deducted > 0) {
                        $this->dispatch('toast', message: 'Clock in otomatis tercatat. Anda TERLAMBAT! Poin berkurang ' . $deducted, type: 'warning');
                    } else {
                        $this->dispatch('toast', message: 'Clock in otomatis tercatat. Selamat bertugas!');
                    }
                }
            }
        }

        // Check if session for today is already finished
        $isTodayFinished = DailyRecap::whereDate('date', now())
            ->where('jurusan_id', $activeJurusanId)
            ->where('actual_cash', '>', 0)
            ->exists();

        if ($isTodayFinished) {
            $hasHigherRole = auth()->user()->roles()
                ->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])
                ->exists();

            if (! $hasHigherRole) {
                // If they haven't clocked out yet, allow them to continue
                $attendance = CashierAttendance::where('user_id', auth()->id())
                    ->where('jurusan_id', $activeJurusanId)
                    ->where('date', now()->toDateString())
                    ->first();

                if ($attendance && ! $attendance->clock_out) {
                    // Do not block or redirect, allow them to check stock and do transactions
                } else {
                    session()->flash('error', 'Sesi kasir hari ini telah berakhir. Anda tidak dapat melakukan transaksi lagi.');
                    $this->redirectRoute('dashboard', navigate: true);

                    return;
                }
            } else {
                // Higher-role cashier: once the session is finished, the cashier mode
                // is locked too — block re-entry (can only be reopened via emergency reactivate)
                session()->flash('error', 'Sesi kasir hari ini telah diselesaikan. Mode kasir terkunci.');
                $this->redirectRoute('dashboard', navigate: true);

                return;
            }
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
        $allProducts = $this->getActiveProducts();

        $posSessionService->fixUnfinishedSession($this->unfinishedSessionDate, $allProducts, $activeJurusanId);

        $this->showRecoveryModal = false;
        $this->unfinishedSessionDate = null;

        $this->dispatch('toast', message: 'Sesi sebelumnya berhasil diselesaikan secara otomatis!', type: 'success');

        // Check opening stock for today now that the previous session is fixed
        $this->checkOpeningStock($posSessionService);

        $this->refreshProducts();
    }

    // Polling method to detect newly assigned tasks and notify cashier
    public function checkNewTasks(): void
    {
        $userId = auth()->id();
        if (! $userId) {
            return;
        }

        $activeJurusanId = session('active_jurusan_id');

        // Get new task assignments (not submissions)
        $newAssignments = CashierTaskAssignment::with('taskDefinition')
            ->where('assigned_to', $userId)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
            ->where('created_at', '>', $this->lastTaskCheckAt)
            ->get();

        if ($newAssignments->isNotEmpty()) {
            $first = $newAssignments->first();
            $this->dispatch('toast', message: 'Tugas baru: "' . $first->taskDefinition->task_name . '"', type: 'success');
            // Also dispatch a custom event carrying CTA url
            $this->dispatch('new-task',
                message: 'Terdapat tugas baru: "' . $first->taskDefinition->task_name . '" — buka halaman tugas Anda.',
                cta_url: route('my-tasks')
            );
            // update last check
            $this->lastTaskCheckAt = now();
        } else {
            // update last check to now if no new tasks to avoid re-querying older range
            $this->lastTaskCheckAt = now();
        }
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
        $activeJurusanId = session('active_jurusan_id');
        $allProducts = Product::where('is_active', true)
            ->whereHas('stockEntries', function ($q) use ($today) {
                $q->where('date', $today)->where('opening_stock', '>', 0);
            })
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
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

    public function saveClosingStockAndNext(PosSessionService $posSessionService): void
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        $validProductIds = StockEntry::where('date', $today)
            ->where('opening_stock', '>', 0)
            ->whereHas('product', function ($q) use ($activeJurusanId) {
                if ($activeJurusanId) {
                    $q->where('jurusan_id', $activeJurusanId);
                }
            })
            ->pluck('product_id')
            ->toArray();

        $filteredStockItems = array_intersect_key($this->stockItems, array_flip($validProductIds));

        $hasHigherRole = auth()->user()->roles()
            ->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])
            ->exists();

        // Save stock
        $posSessionService->saveClosingStock($filteredStockItems, $today, $activeJurusanId);

        $hasHigherRole = auth()->user()->roles()
            ->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])
            ->exists();

        if ($hasHigherRole) {
            // Higher-role cashier: no closing report needed, finish immediately
            $this->completeSession(null);

            return;
        }

        $this->showClosingStockModal = false;
        $this->showClosingReportModal = true;
    }

    public function submitClosingReport(): void
    {
        $this->validate([
            'closingReportText' => 'required|string|min:5',
        ]);

        $this->completeSession($this->closingReportText);
    }

    protected function completeSession(?string $report): void
    {
        $today = $this->transactionDate ?: now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');

        // Record Closing Attendance
        $attendance = CashierAttendance::where('user_id', auth()->id())
            ->where('jurusan_id', $activeJurusanId)
            ->where('date', now()->toDateString())
            ->first();

        $currentTime = now();
        $isEarlyClockOut = false;
        $isBonusClockOut = false;
        $deductedClockOut = 0;
        $bonusPoints = 10;

        $jurusan = Jurusan::find($activeJurusanId);
        $settings = $jurusan ? ($jurusan->theme_settings ?: []) : [];
        $targetClockOut = $settings['clock_out_time'] ?? '15:00';
        $penaltyClockOut = (int) ($settings['late_clock_out_penalty'] ?? 0);

        if ($targetClockOut) {
            try {
                $targetTime = \Carbon\Carbon::createFromFormat('H:i', $targetClockOut);
                $targetTime->setDate($currentTime->year, $currentTime->month, $currentTime->day);
                if ($currentTime->lt($targetTime)) {
                    $isEarlyClockOut = true;
                    if ($penaltyClockOut > 0) {
                        $deductedClockOut = $penaltyClockOut;
                        auth()->user()->decrement('points', $penaltyClockOut);
                        if (auth()->user()->points < 0) {
                            auth()->user()->update(['points' => 0]);
                        }
                    }
                } else {
                    $isBonusClockOut = true;
                    auth()->user()->increment('points', $bonusPoints);
                    auth()->user()->increment('streak', 1);
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        $closingCash = 1; // Default to 1 to lock the session (actual_cash > 0)

        if ($attendance) {
            $attendance->update([
                'clock_out' => $currentTime,
                'closing_cash' => $closingCash,
                'closing_report' => $report,
                'points_at_closing' => (int) (auth()->user()->points + auth()->user()->pending_points),
            ]);
        }

        // Always make sure DailyRecap is locked for today
        $existingRecap = DailyRecap::withoutGlobalScope('active')
            ->where('date', $today)
            ->where('jurusan_id', $activeJurusanId)
            ->first();

        $newActualCash = $closingCash;
        if ($existingRecap) {
            if ($existingRecap->actual_cash < 0) {
                $newActualCash = abs($existingRecap->actual_cash);
            } elseif ($existingRecap->actual_cash > 1) {
                $newActualCash = $existingRecap->actual_cash;
            }
        }

        DailyRecap::upsertForSession($today, $activeJurusanId, [
            'actual_cash' => $newActualCash,
        ]);

        $this->showClosingStockModal = false;
        $this->showClosingReportModal = false;
        $this->stockItems = [];

        if ($isEarlyClockOut && $deductedClockOut > 0) {
            session()->flash('toast', 'Sesi kasir berhasil diselesaikan. Anda clock-out TERLALU CEPAT! Poin berkurang ' . $deductedClockOut);
        } elseif ($isBonusClockOut) {
            session()->flash('toast', 'Sesi kasir berhasil diselesaikan. Selamat, Anda mendapat bonus +' . $bonusPoints . ' poin & +1 streak!');
        } else {
            session()->flash('toast', 'Sesi kasir berhasil diselesaikan.');
        }
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function checkout(PosSessionService $posSessionService, $cart, $total, $change, $buyer_name, $status, $note, $transactionDate = null): void
    {
        if (empty($cart)) {
            return;
        }

        $activeJurusanId = session('active_jurusan_id');

        // Validate that all products in the cart belong to the active TEFA
        if ($activeJurusanId) {
            $productIds = collect($cart)->pluck('id')->toArray();
            $invalidProductsExist = Product::whereIn('id', $productIds)
                ->where('jurusan_id', '!=', $activeJurusanId)
                ->exists();

            if ($invalidProductsExist) {
                $this->dispatch('toast', message: 'Gagal Checkout: Terdapat produk dari unit usaha/TEFA lain di keranjang belanja Anda!', type: 'error');
                return;
            }
        }

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
            $this->dispatch(
                'toast',
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

        CashTransaction::create([
            'jurusan_id' => $activeJurusanId,
            'date' => $this->transactionDate ?: now()->toDateString(),
            'cash_type' => 'modal',
            'cash_category_id' => $categoryId,
            'type' => 'expense',
            'amount' => (float) $amount,
            'description' => trim($description) . ' (Sistem - Pengeluaran Cepat)',
        ]);

        // Invalidate cache
        Cache::forget('cash_balances_' . ($activeJurusanId ?: 'global'));

        $this->dispatch('toast', message: 'Pengeluaran berhasil dicatat!');
    }

    public function render(PosQueryService $posQueryService)
    {
        $today = now()->toDateString();
        $activeJurusanId = session('active_jurusan_id');
        $userId = auth()->id();

        // Auto-assign routine tasks to kasir jika mereka terjadwal hari ini
        $service = app(\App\Services\CashierTaskService::class);
        $service->autoAssignRoutineTasksForCashier($userId, $activeJurusanId);

        $allProducts = $this->getProductsForAlpine($posQueryService);

        $isSessionFinished = false;
        if (session('active_role_name') === 'kasir') {
            $attendance = CashierAttendance::where('user_id', auth()->id())
                ->where('jurusan_id', $activeJurusanId)
                ->where('date', $today)
                ->first();
            $isSessionFinished = $attendance && $attendance->clock_out;
        } else {
            $isSessionFinished = DailyRecap::where('date', $today)
                ->where('jurusan_id', $activeJurusanId)
                ->where('actual_cash', '>', 0)
                ->exists();
        }

        $categories = CashCategory::where('jurusan_id', $activeJurusanId)->get();

        // Get daily tasks for the logged in cashier (from task assignments)
        $dailyAssignments = CashierTaskAssignment::with(['taskDefinition', 'submissions'])
            ->where('assigned_to', auth()->id())
            ->where(function ($query) use ($today) {
                $query->whereHas('taskDefinition', function ($q) use ($today) {
                    $q->where('date', $today);
                })
                ->orWhere(function ($q) use ($today) {
                    $q->whereHas('taskDefinition', function ($sq) {
                        $sq->where('is_routine', true);
                    })
                    ->whereDate('created_at', $today);
                });
            })
            ->whereDoesntHave('submissions', function ($q) {
                $q->where('approval_status', 'approved');
            })
            ->whereHas('submissions', function ($q) {
                $q->where('approval_status', 'rejected');
            }, '<', 3)
            ->get();

        // Compute computed_deadline for routine tasks
        foreach ($dailyAssignments as $assignment) {
            $taskDef = $assignment->taskDefinition;
            if ($taskDef->is_routine && ! $taskDef->deadline_at) {
                $attendance = CashierAttendance::where('user_id', auth()->id())
                    ->where('date', $assignment->created_at->toDateString())
                    ->orderBy('clock_in', 'asc')
                    ->first();

                if ($attendance && $attendance->clock_in) {
                    $taskDef->computed_deadline = \Carbon\Carbon::parse($attendance->clock_in)->addHours(8);
                } else {
                    $taskDef->computed_deadline = null;
                }
            }
        }

        return view('livewire.pos.kasir', [
            'allProductsJson' => $allProducts,
            'isSessionFinished' => $isSessionFinished,
            'categories' => $categories,
            'dailyTasks' => $dailyAssignments,
        ])->layout('layouts.kasir');
    }
}
