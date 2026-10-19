<?php

namespace App\Livewire;

use App\Models\CashierAttendance;
use App\Models\CashierSchedule;
use App\Models\CashierTaskAssignment;
use App\Models\CashTransaction;
use App\Models\DailyRecap;
use App\Models\DocumentationSchedule;
use App\Models\Jurusan;
use App\Models\ProductCategory;
use App\Models\Transaction;
use App\Services\CashierTaskService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Dashboard extends Component
{
    public $filterJurusan = '';

    public $selectedTopCategory = 'all';

    public function mount()
    {
        // Auto clock in cashier if logged in as kasir and scheduled today
        if (session('active_role_name') === 'kasir') {
            $this->ensureCashierClockIn();
        }
    }

    protected function ensureCashierClockIn(): void
    {
        $userId = auth()->id();
        $activeJurusanId = session('active_jurusan_id');
        if (! $userId || ! $activeJurusanId) {
            return;
        }

        $activeJurusan = Jurusan::find($activeJurusanId);
        $allowedJurusanIds = [$activeJurusanId];
        if ($activeJurusan && $activeJurusan->parent_id) {
            $allowedJurusanIds[] = $activeJurusan->parent_id;
        }

        $isScheduled = CashierSchedule::where('user_id', $userId)
            ->whereIn('jurusan_id', $allowedJurusanIds)
            ->where('date', now()->toDateString())
            ->exists();

        if (! $isScheduled) {
            return;
        }

        $attendance = CashierAttendance::where('user_id', $userId)
            ->where('jurusan_id', $activeJurusanId)
            ->where('date', now()->toDateString())
            ->first();

        if (! $attendance) {
            $schedule = CashierSchedule::where('user_id', $userId)
                ->whereIn('jurusan_id', $allowedJurusanIds)
                ->where('date', now()->toDateString())
                ->first();

            $currentTime = now();
            $settings = $activeJurusan ? ($activeJurusan->theme_settings ?: []) : [];
            $targetClockIn = $settings['clock_in_time'] ?? '07:00';

            $status = 'present';
            $clockInStatus = 'on_time';
            if ($targetClockIn) {
                try {
                    $targetTime = Carbon::createFromFormat('H:i', $targetClockIn);
                    $targetTime->setDate($currentTime->year, $currentTime->month, $currentTime->day);
                    if ($currentTime->gt($targetTime)) {
                        $status = 'late';
                        $clockInStatus = 'late';
                    } else {
                        $status = 'present';
                        $clockInStatus = 'on_time';
                    }
                } catch (\Exception $e) {
                    // ignore
                }
            }

            CashierAttendance::create([
                'cashier_schedule_id' => $schedule ? $schedule->id : null,
                'user_id' => $userId,
                'jurusan_id' => $activeJurusanId,
                'date' => now()->toDateString(),
                'clock_in' => $currentTime,
                'status' => $status,
                'clock_in_status' => $clockInStatus,
            ]);
        }
    }

    public function setTopCategory($categoryId)
    {
        $this->selectedTopCategory = $categoryId;
    }

    public function updatedFilterJurusan()
    {
        // No action needed, page will re-render
    }

    public function cleanupOrphanCarryForward(): void
    {
        if (! in_array(session('active_role_name'), ['pengelola_jurusan', 'superadmin'], true)) {
            abort(403);
        }

        $activeJurusanId = session('active_jurusan_id');

        if (! $activeJurusanId) {
            return;
        }

        $deleted = CashTransaction::withoutGlobalScope('active')
            ->where('jurusan_id', $activeJurusanId)
            ->whereDate('date', now()->startOfMonth()->toDateString())
            ->carryForwardOnly()
            ->delete();

        Cache::flush();

        $this->dispatch('toast', message: 'Saldo bawaan tutup buku yatim berhasil dibersihkan ('.$deleted.' catatan).');
    }

    public function render()
    {
        $today = today();
        $yesterday = today()->subDay();
        $activeJurusanId = session('active_jurusan_id') ?: ($this->filterJurusan ?: null);

        $hasHigherRole = auth()->user()
            ->roles()
            ->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])
            ->exists();

        $isCashier = session('active_role_name') === 'kasir';

        // Check if session is finished (Daily Recap exists with actual cash)
        $isSessionFinished = DailyRecap::withoutGlobalScope('active')
            ->whereDate('date', now())
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where(function ($query) use ($activeJurusanId) {
                    $query->where('jurusan_id', $activeJurusanId)
                        ->orWhereNull('jurusan_id');
                });
            })
            ->where('actual_cash', '>', 0)
            ->exists();

        $hasClockedOut = CashierAttendance::where('user_id', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where(function ($query) use ($activeJurusanId) {
                    $query->where('jurusan_id', $activeJurusanId)
                        ->orWhereNull('jurusan_id');
                });
            })
            ->whereNotNull('clock_out')
            ->exists();

        $isSessionClosed = $isSessionFinished || ($isCashier && $hasClockedOut);

        // 1. DATA KHUSUS KASIR
        if ($isCashier) {
            $userId = auth()->id();

            // Auto-assign routine tasks for cashier if scheduled
            try {
                app(CashierTaskService::class)->autoAssignRoutineTasksForCashier($userId, $activeJurusanId);
            } catch (\Exception $e) {
                // ignore
            }

            // Attendance Data Hari Ini
            $todayAttendance = CashierAttendance::where('user_id', $userId)
                ->where('jurusan_id', $activeJurusanId)
                ->whereDate('date', $today)
                ->first();

            $jurusan = Jurusan::find($activeJurusanId);
            $settings = $jurusan ? ($jurusan->theme_settings ?: []) : [];
            $targetClockOut = $settings['clock_out_time'] ?? '15:00';
            $targetClockIn = $settings['clock_in_time'] ?? '07:00';

            $targetClockOutCarbon = null;
            $isPastClosingTarget = false;
            if ($targetClockOut) {
                try {
                    $targetClockOutCarbon = Carbon::createFromFormat('H:i', $targetClockOut);
                    $targetClockOutCarbon->setDate(now()->year, now()->month, now()->day);
                    $isPastClosingTarget = now()->gte($targetClockOutCarbon);
                } catch (\Exception $e) {
                    // ignore
                }
            }

            // Tasks Hari Ini
            $todayTaskAssignments = CashierTaskAssignment::with([
                'taskDefinition',
                'submissions' => fn ($q) => $q->orderBy('submission_version', 'desc'),
            ])
                ->where('assigned_to', $userId)
                ->where(function ($query) use ($today) {
                    $query->whereHas('taskDefinition', function ($q) use ($today) {
                        $q->where('date', $today->toDateString());
                    })
                        ->orWhere(function ($q) use ($today) {
                            $q->whereHas('taskDefinition', function ($sq) {
                                $sq->where('is_routine', true);
                            })
                                ->whereDate('created_at', $today->toDateString());
                        });
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Documentation Schedules Hari Ini & Terdekat
            $myDocSchedules = DocumentationSchedule::with('activity')
                ->where('user_id', $userId)
                ->whereDate('date', '>=', now()->toDateString())
                ->orderBy('date', 'asc')
                ->limit(4)
                ->get();

            return view('livewire.dashboard', [
                'today' => $today,
                'isCashier' => true,
                'hasHigherRole' => $hasHigherRole,
                'isSessionFinished' => $isSessionFinished,
                'hasClockedOut' => $hasClockedOut,
                'isSessionClosed' => $isSessionClosed,
                'todayAttendance' => $todayAttendance,
                'targetClockOut' => $targetClockOut,
                'targetClockIn' => $targetClockIn,
                'targetClockOutCarbon' => $targetClockOutCarbon,
                'isPastClosingTarget' => $isPastClosingTarget,
                'todayTaskAssignments' => $todayTaskAssignments,
                'myDocSchedules' => $myDocSchedules,
                'jurusans' => Jurusan::all(),
                'topProducts' => collect(),
                'categories' => collect(),
                'topCategories' => collect(),
                'weeklyData' => [],
                'stats' => (object)[],
                'recentTransactions' => collect(),
                'orphanCarryForwardAmount' => 0,
            ])->layout('layouts.app', ['title' => 'Dashboard Kasir']);
        }

        $yesterday = today()->subDay();
        $activeJurusanId = session('active_jurusan_id') ?: ($this->filterJurusan ?: null);

        $todayTransactions = Transaction::forReporting()->with('product')
            ->whereDate('transacted_at', $today)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        $yesterdayTransactions = Transaction::forReporting()->with('product')
            ->whereDate('transacted_at', $yesterday)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        $todayExpenses = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->whereDate('date', $today)
            ->where('type', 'expense')
            ->where('cash_type', 'keuntungan')
            ->sum('amount');

        $yesterdayExpenses = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->whereDate('date', $yesterday)
            ->where('type', 'expense')
            ->where('cash_type', 'keuntungan')
            ->sum('amount');

        $todayRevenue = (float) $todayTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');
        $yesterdayRevenue = (float) $yesterdayTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');
        $revenueChange = $yesterdayRevenue > 0 ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 100;

        $todaySupplierHak = $todayTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn ($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
        $todayInternalRevenue = max(0, $todayRevenue - $todaySupplierHak);

        $todayProfit = max(0, $todayTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn ($tx) => $tx->unit_profit * $tx->quantity) - $todayExpenses);
        $yesterdayProfit = max(0, $yesterdayTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn ($tx) => $tx->unit_profit * $tx->quantity) - $yesterdayExpenses);
        $profitChange = $yesterdayProfit > 0 ? (($todayProfit - $yesterdayProfit) / $yesterdayProfit) * 100 : 100;

        $todayCount = $todayTransactions->count();
        $yesterdayCount = $yesterdayTransactions->count();
        $countChange = $yesterdayCount > 0 ? (($todayCount - $yesterdayCount) / $yesterdayCount) * 100 : 100;

        // General Stats (All Time)
        $allTimeBase = Transaction::forReporting()->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            });
        $allTimeProfitIncome = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->where('cash_type', 'keuntungan')
            ->where('type', 'income')
            ->sum('amount');
        $allTimeProfitExpense = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->where('cash_type', 'keuntungan')
            ->where('type', 'expense')
            ->sum('amount');

        $totalAllTimeProfit = $allTimeProfitIncome - $allTimeProfitExpense;

        $totalAllTimeRevenue = (float) $allTimeBase->sum('total_price');
        $totalAllTimeTransactions = $allTimeBase->count();

        $cashIncome = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->where('type', 'income')
            ->sum('amount');

        $cashExpense = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->where('type', 'expense')
            ->sum('amount');

        $totalAuditCash = $cashIncome - $cashExpense;

        $totalOutstandingDebt = Transaction::forReporting()->whereIn('status', ['belum_menerima_uang', 'uang_dipinjam'])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->sum('total_price');

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $monthlyIncome = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->excludeCarryForward()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('type', 'income')
            ->sum('amount');

        $monthlyExpense = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->excludeCarryForward()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('type', 'expense')
            ->sum('amount');

        $modalIncome = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->where('cash_type', 'modal')
            ->where('type', 'income')
            ->sum('amount');

        $modalExpense = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->where('cash_type', 'modal')
            ->where('type', 'expense')
            ->sum('amount');

        $totalModalBalance = $modalIncome - $modalExpense;

        $stats = (object) [
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'total_modal_balance' => $totalModalBalance,
            'today_revenue' => $todayRevenue,
            'today_internal_revenue' => $todayInternalRevenue,
            'revenue_change' => $revenueChange,
            'today_profit' => $todayProfit,
            'profit_change' => $profitChange,
            'today_transactions' => $todayCount,
            'transactions_change' => $countChange,
            'avg_transaction' => $todayCount > 0 ? $todayRevenue / $todayCount : 0,

            // New General Stats
            'total_all_time_profit' => $totalAllTimeProfit,
            'total_all_time_revenue' => $totalAllTimeRevenue,
            'total_all_time_transactions' => $totalAllTimeTransactions,
            'avg_all_time_ticket' => $totalAllTimeTransactions > 0 ? $totalAllTimeRevenue / $totalAllTimeTransactions : 0,
            'total_audit_cash' => $totalAuditCash,
            'total_outstanding_debt' => $totalOutstandingDebt,
        ];

        // Weekly Chart Data - Optimized to use only 2 queries instead of 14
        $startDate = today()->subDays(6)->startOfDay()->toDateTimeString();
        $endDate = today()->endOfDay()->toDateTimeString();

        $weeklyTransactions = Transaction::forReporting()->whereBetween('transacted_at', [$startDate, $endDate])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        $weeklyExpenses = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->whereBetween('date', [today()->subDays(6)->toDateString(), today()->toDateString()])
            ->where('type', 'expense')
            ->where('cash_type', 'keuntungan')
            ->selectRaw('date, SUM(amount) as total_amount')
            ->groupBy('date')
            ->pluck('total_amount', 'date')
            ->toArray();

        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $dateString = $date->toDateString();

            $dayTxs = $weeklyTransactions->filter(function ($tx) use ($dateString) {
                return $tx->transacted_at->format('Y-m-d') === $dateString;
            });

            $dayExpenses = (float) ($weeklyExpenses[$dateString] ?? 0);

            $weeklyData[] = [
                'day' => $date->translatedFormat('D'),
                'revenue' => (float) $dayTxs->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price'),
                'profit' => max(0, $dayTxs->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn ($tx) => $tx->unit_profit * $tx->quantity) - $dayExpenses),
            ];
        }

        $recentTransactions = Transaction::forReporting()->with('product')
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->orderByDesc('transacted_at')
            ->limit(5)
            ->get();

        $categories = ProductCategory::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where(function ($query) use ($activeJurusanId) {
                $query->where('jurusan_id', $activeJurusanId)
                    ->orWhereNull('jurusan_id');
            });
        })->get();

        $topProducts = Transaction::forReporting()->with('product')
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('transactions.jurusan_id', $activeJurusanId);
            })
            ->when($this->selectedTopCategory !== 'all', function ($q) {
                return $q->whereHas('product', function ($pq) {
                    $pq->where('category_id', $this->selectedTopCategory);
                });
            })
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Category Distribution (All Time)
        $categoryData = Transaction::forReporting()->join('products', 'transactions.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian'])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('transactions.jurusan_id', $activeJurusanId);
            })
            ->selectRaw('product_categories.name as category_name, SUM(transactions.total_price) as total_revenue')
            ->groupBy('product_categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Monthly Revenue Growth (Last 6 Months) - Optimized to use only 2 queries instead of 12
        $startOfMonthRange = today()->subMonths(5)->startOfMonth()->startOfDay()->toDateTimeString();
        $endOfMonthRange = today()->endOfDay()->toDateTimeString();

        $monthlyTransactions = Transaction::forReporting()->whereBetween('transacted_at', [$startOfMonthRange, $endOfMonthRange])
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        $monthlyExpenses = CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })
            ->whereBetween('date', [today()->subMonths(5)->startOfMonth()->toDateString(), today()->toDateString()])
            ->where('type', 'expense')
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as expense_month, SUM(amount) as total_amount")
            ->groupBy('expense_month')
            ->pluck('total_amount', 'expense_month')
            ->toArray();

        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = today()->subMonths($i);
            $yearMonthKey = $month->format('Y-m');

            $monthExpenses = (float) ($monthlyExpenses[$yearMonthKey] ?? 0);

            $monthTxs = $monthlyTransactions->filter(function ($tx) use ($month) {
                $txDate = Carbon::parse($tx->transacted_at);

                return $txDate->month === $month->month && $txDate->year === $month->year;
            });

            $monthlyData[] = [
                'month' => $month->translatedFormat('M Y'),
                'revenue' => (float) $monthTxs->sum('total_price'),
            ];
        }

        $orphanCarryForwardAmount = 0;
        if ($activeJurusanId) {
            $orphanCarryForwardAmount = (int) CashTransaction::withoutGlobalScope('active')
                ->where('jurusan_id', $activeJurusanId)
                ->whereDate('date', now()->startOfMonth()->toDateString())
                ->carryForwardOnly()
                ->sum('amount');
        }

        return view('livewire.dashboard', [
            'today' => $today,
            'isCashier' => false,
            'hasHigherRole' => $hasHigherRole,
            'todayAttendance' => null,
            'targetClockOut' => '15:00',
            'targetClockIn' => '07:00',
            'targetClockOutCarbon' => null,
            'isPastClosingTarget' => false,
            'todayTaskAssignments' => collect(),
            'myDocSchedules' => collect(),
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'topProducts' => $topProducts,
            'categories' => $categories,
            'topCategories' => $categories,
            'selectedTopCategory' => $this->selectedTopCategory,
            'weeklyData' => $weeklyData,
            'categoryData' => $categoryData,
            'monthlyData' => $monthlyData,
            'isSessionFinished' => $isSessionFinished,
            'hasClockedOut' => $hasClockedOut,
            'isSessionClosed' => $isSessionClosed,
            'orphanCarryForwardAmount' => $orphanCarryForwardAmount,
            'jurusans' => Jurusan::all(),
        ])->layout('layouts.app', ['title' => 'Dashboard Overview']);
    }

    public function emergencyReactivateSession()
    {
        $hasHigherRole = auth()->user()->roles()
            ->whereIn('roles.name', ['superadmin', 'pengelola_jurusan'])
            ->exists();

        $isCashier = session('active_role_name') === 'kasir';

        if (! $hasHigherRole && ! $isCashier) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengaktifkan mode darurat.');
        }

        $activeJurusanId = session('active_jurusan_id');

        // 1. Reset or delete DailyRecap for today so isSessionFinished returns false
        DailyRecap::withoutGlobalScope('active')
            ->whereDate('date', now())
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where(function ($query) use ($activeJurusanId) {
                    $query->where('jurusan_id', $activeJurusanId)
                        ->orWhereNull('jurusan_id');
                });
            })
            ->delete();

        // 2. Reset clock_out for cashier attendance today so cashiers can re-enter POS mode
        CashierAttendance::whereDate('date', now())
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where(function ($query) use ($activeJurusanId) {
                    $query->where('jurusan_id', $activeJurusanId)
                        ->orWhereNull('jurusan_id');
                });
            })
            ->update([
                'clock_out' => null,
            ]);

        // Catat ke security logs jika model tersedia
        try {
            \App\Models\SecurityLog::create([
                'user_id' => auth()->id(),
                'jurusan_id' => $activeJurusanId,
                'action' => 'emergency_reactivate_session',
                'description' => auth()->user()->name . ' mengaktifkan Mode Kasir Darurat / membuka kembali sesi kasir setelah penutupan.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // ignore
        }

        session()->flash('success', 'Mode kasir darurat berhasil diaktifkan. Sesi kasir terbuka kembali untuk transaksi.');

        $this->redirect(route('kasir'), navigate: true);
    }
}
