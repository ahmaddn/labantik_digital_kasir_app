<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $today = today();
        $yesterday = today()->subDay();

        $todayTransactions = Transaction::with('product')->whereDate('transacted_at', $today)->get();
        $yesterdayTransactions = Transaction::with('product')->whereDate('transacted_at', $yesterday)->get();

        $todayRevenue = $todayTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');
        $yesterdayRevenue = $yesterdayTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');
        $revenueChange = $yesterdayRevenue > 0 ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 100;

        $todaySupplierHak = $todayTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
        $todayInternalRevenue = $todayRevenue - $todaySupplierHak;

        $todayProfit = $todayTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
        $yesterdayProfit = $yesterdayTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
        $profitChange = $yesterdayProfit > 0 ? (($todayProfit - $yesterdayProfit) / $yesterdayProfit) * 100 : 100;

        $todayCount = $todayTransactions->count();
        $yesterdayCount = $yesterdayTransactions->count();
        $countChange = $yesterdayCount > 0 ? (($todayCount - $yesterdayCount) / $yesterdayCount) * 100 : 100;

        // General Stats (All Time)
        $allTimeBase = Transaction::whereIn('status', ['uang_diterima', 'belum_kembalian']);
        $totalAllTimeProfit = $allTimeBase->sum(\DB::raw('unit_profit * quantity'));
        $totalAllTimeRevenue = $allTimeBase->sum('total_price');
        $totalAllTimeTransactions = $allTimeBase->count();
        
        $totalAuditCash = \App\Models\DailyRecap::sum('actual_cash');
        $totalOutstandingDebt = Transaction::whereIn('status', ['belum_menerima_uang', 'uang_dipinjam'])->sum('total_price');

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
            $dayTxs = Transaction::whereDate('transacted_at', $date)->get();
            $weeklyData[] = [
                'day' => $date->translatedFormat('D'),
                'revenue' => $dayTxs->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price'),
                'profit' => $dayTxs->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity)
            ];
        }

        $recentTransactions = Transaction::with('product')
            ->orderByDesc('transacted_at')
            ->limit(5)
            ->get();

        $topProducts = Transaction::with('product')
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Category Distribution (All Time)
        $categoryData = Transaction::join('products', 'transactions.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian'])
            ->selectRaw('product_categories.name as category_name, SUM(transactions.total_price) as total_revenue')
            ->groupBy('product_categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Monthly Revenue Growth (Last 6 Months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = today()->subMonths($i);
            $monthlyData[] = [
                'month' => $month->translatedFormat('M Y'),
                'revenue' => Transaction::whereMonth('transacted_at', $month->month)
                    ->whereYear('transacted_at', $month->year)
                    ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                    ->sum('total_price')
            ];
        }

        // Check if session is finished (Daily Recap exists with actual cash)
        $isSessionFinished = \App\Models\DailyRecap::where('date', $today->toDateString())
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
            'isSessionFinished' => $isSessionFinished
        ])->layout('layouts.app', ['title' => 'Dashboard Overview']);
    }
}
