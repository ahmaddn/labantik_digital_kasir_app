<?php

namespace App\Livewire;

use App\Models\DailyRecap;
use App\Models\Jurusan;
use App\Models\Transaction;
use Livewire\Component;

class Dashboard extends Component
{
    public $filterJurusan = '';

    public function updatedFilterJurusan()
    {
        // No action needed, page will re-render
    }

    public function render()
    {
        $today = today();
        $yesterday = today()->subDay();
        $activeJurusanId = session('active_jurusan_id') ?: ($this->filterJurusan ?: null);

        $todayTransactions = Transaction::with('product')
            ->whereDate('transacted_at', $today)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        $yesterdayTransactions = Transaction::with('product')
            ->whereDate('transacted_at', $yesterday)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        $todayExpenses = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereDate('date', $today)
            ->where('type', 'expense')
            ->sum('amount');

        $yesterdayExpenses = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereDate('date', $yesterday)
            ->where('type', 'expense')
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
        $allTimeBase = Transaction::whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            });
        $allTimeExpenses = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->where('type', 'expense')
            ->sum('amount');

        $totalAllTimeProfit = max(0, (float) $allTimeBase->sum(\DB::raw('unit_profit * quantity')) - $allTimeExpenses);
        $totalAllTimeRevenue = (float) $allTimeBase->sum('total_price');
        $totalAllTimeTransactions = $allTimeBase->count();

        $cashIncome = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->where('type', 'income')
            ->sum('amount');
            
        $cashExpense = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->where('type', 'expense')
            ->sum('amount');
            
        $totalAuditCash = $cashIncome - $cashExpense;

        $totalOutstandingDebt = Transaction::whereIn('status', ['belum_menerima_uang', 'uang_dipinjam'])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->sum('total_price');

        $stats = (object) [
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

        // Weekly Chart Data
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $dayTxs = Transaction::whereDate('transacted_at', $date)
                ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                    return $q->where('jurusan_id', $activeJurusanId);
                })
                ->get();

            $dayExpenses = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                    return $q->where('jurusan_id', $activeJurusanId);
                })
                ->whereDate('date', $date)
                ->where('type', 'expense')
                ->sum('amount');

            $weeklyData[] = [
                'day' => $date->translatedFormat('D'),
                'revenue' => max(0, $dayTxs->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price') - $dayExpenses),
                'profit' => max(0, $dayTxs->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn ($tx) => $tx->unit_profit * $tx->quantity) - $dayExpenses),
            ];
        }

        $recentTransactions = Transaction::with('product')
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->orderByDesc('transacted_at')
            ->limit(5)
            ->get();

        $topProducts = Transaction::with('product')
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Category Distribution (All Time)
        $categoryData = Transaction::join('products', 'transactions.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian'])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('transactions.jurusan_id', $activeJurusanId);
            })
            ->selectRaw('product_categories.name as category_name, SUM(transactions.total_price) as total_revenue')
            ->groupBy('product_categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Monthly Revenue Growth (Last 6 Months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = today()->subMonths($i);
            $monthExpenses = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                    return $q->where('jurusan_id', $activeJurusanId);
                })
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->where('type', 'expense')
                ->sum('amount');

            $monthlyData[] = [
                'month' => $month->translatedFormat('M Y'),
                'revenue' => max(0, Transaction::whereMonth('transacted_at', $month->month)
                    ->whereYear('transacted_at', $month->year)
                    ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                    ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                        return $q->where('jurusan_id', $activeJurusanId);
                    })
                    ->sum('total_price') - $monthExpenses),
            ];
        }

        // Check if session is finished (Daily Recap exists with actual cash)
        $isSessionFinished = DailyRecap::whereDate('date', now())
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->where('actual_cash', '>', 0)
            ->exists();

        return view('livewire.dashboard', [
            'today' => $today,
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'topProducts' => $topProducts,
            'weeklyData' => $weeklyData,
            'categoryData' => $categoryData,
            'monthlyData' => $monthlyData,
            'isSessionFinished' => $isSessionFinished,
            'jurusans' => Jurusan::all(),
        ])->layout('layouts.app', ['title' => 'Dashboard Overview']);
    }

    public function emergencyReactivateSession()
    {
        $activeJurusanId = session('active_jurusan_id');
        DailyRecap::whereDate('date', now())
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->delete();
        $this->redirect(route('dashboard'), navigate: true);
    }
}
