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
            ->where('cash_type', 'keuntungan')
            ->sum('amount');

        $yesterdayExpenses = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
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
        $allTimeBase = Transaction::whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            });
        $allTimeProfitIncome = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->where('cash_type', 'keuntungan')
            ->where('type', 'income')
            ->sum('amount');
        $allTimeProfitExpense = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->where('cash_type', 'keuntungan')
            ->where('type', 'expense')
            ->sum('amount');

        $totalAllTimeProfit = $allTimeProfitIncome - $allTimeProfitExpense;
 
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

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $monthlyIncome = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('type', 'income')
            ->sum('amount');

        $monthlyExpense = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('type', 'expense')
            ->sum('amount');

        $modalIncome = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->where('cash_type', 'modal')
            ->where('type', 'income')
            ->sum('amount');

        $modalExpense = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
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

        $weeklyTransactions = Transaction::whereBetween('transacted_at', [$startDate, $endDate])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        $weeklyExpenses = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereBetween('date', [today()->subDays(6)->toDateString(), today()->toDateString()])
            ->where('type', 'expense')
            ->selectRaw('date, SUM(amount) as total_amount')
            ->groupBy('date')
            ->pluck('total_amount', 'date')
            ->toArray();

        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $dateString = $date->toDateString();
            
            $dayTxs = $weeklyTransactions->filter(function ($tx) use ($dateString) {
                return substr($tx->transacted_at, 0, 10) === $dateString;
            });

            $dayExpenses = (float) ($weeklyExpenses[$dateString] ?? 0);

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

        // Monthly Revenue Growth (Last 6 Months) - Optimized to use only 2 queries instead of 12
        $startOfMonthRange = today()->subMonths(5)->startOfMonth()->startOfDay()->toDateTimeString();
        $endOfMonthRange = today()->endOfDay()->toDateTimeString();

        $monthlyTransactions = Transaction::whereBetween('transacted_at', [$startOfMonthRange, $endOfMonthRange])
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        $monthlyExpenses = \App\Models\CashTransaction::when($activeJurusanId, function ($q) use ($activeJurusanId) {
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
                $txDate = \Carbon\Carbon::parse($tx->transacted_at);
                return $txDate->month === $month->month && $txDate->year === $month->year;
            });

            $monthlyData[] = [
                'month' => $month->translatedFormat('M Y'),
                'revenue' => max(0, $monthTxs->sum('total_price') - $monthExpenses),
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
