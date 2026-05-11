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

        $todayRevenue = $todayTransactions->where('status', 'uang_diterima')->sum('total_price');
        $yesterdayRevenue = $yesterdayTransactions->where('status', 'uang_diterima')->sum('total_price');
        $revenueChange = $yesterdayRevenue > 0 ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 100;

        $todayProfit = $todayTransactions->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
        $yesterdayProfit = $yesterdayTransactions->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
        $profitChange = $yesterdayProfit > 0 ? (($todayProfit - $yesterdayProfit) / $yesterdayProfit) * 100 : 100;

        $todayCount = $todayTransactions->count();
        $yesterdayCount = $yesterdayTransactions->count();
        $countChange = $yesterdayCount > 0 ? (($todayCount - $yesterdayCount) / $yesterdayCount) * 100 : 100;

        $stats = (object) [
            'today_revenue' => $todayRevenue,
            'revenue_change' => $revenueChange,
            'today_profit' => $todayProfit,
            'profit_change' => $profitChange,
            'today_transactions' => $todayCount,
            'transactions_change' => $countChange,
            'avg_transaction' => $todayCount > 0 ? $todayRevenue / $todayCount : 0
        ];

        // Weekly Chart Data
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $dayTxs = Transaction::whereDate('transacted_at', $date)->get();
            $weeklyData[] = [
                'day' => $date->translatedFormat('D'),
                'revenue' => $dayTxs->where('status', 'uang_diterima')->sum('total_price'),
                'profit' => $dayTxs->sum(fn($tx) => $tx->unit_profit * $tx->quantity)
            ];
        }

        $recentTransactions = Transaction::with('product')
            ->orderByDesc('transacted_at')
            ->limit(8)
            ->get();

        $topProducts = Transaction::with('product')
            ->whereDate('transacted_at', $today)
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('livewire.dashboard', [
            'today' => $today,
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'topProducts' => $topProducts,
            'weeklyData' => $weeklyData
        ])->layout('layouts.app', ['title' => 'Dashboard Overview']);
    }
}
