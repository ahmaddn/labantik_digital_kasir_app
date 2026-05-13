<?php

namespace App\Livewire\Reports;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MonthlyRecap extends Component
{
    public $selectedMonth;
    public $selectedYear;
    public $availableYears = [];

    public function mount($month = null, $year = null)
    {
        $this->selectedMonth = $month ?? now()->month;
        $this->selectedYear = $year ?? now()->year;
        
        $this->availableYears = Transaction::selectRaw('YEAR(transacted_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($this->availableYears)) {
            $this->availableYears = [now()->year];
        }
    }

    public function render()
    {
        $query = Transaction::with(['product.category'])
            ->whereMonth('transacted_at', $this->selectedMonth)
            ->whereYear('transacted_at', $this->selectedYear);

        $allTransactions = $query->get();

        if ($allTransactions->isEmpty()) {
            return view('livewire.reports.monthly-recap', [
                'recap' => null,
                'categoryRecap' => [],
                'dailyBreakdown' => []
            ])->layout('layouts.app', ['title' => 'Rekap Bulanan']);
        }

        $totalRevenueAll = $allTransactions->sum('total_price');
        $totalRevenueReal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');
        
        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
            
        $totalInternalRevenue = $totalRevenueReal - $totalSupplierHak;

        $totalProfit = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
        $totalModal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
        $daysCount = $allTransactions->pluck('transacted_at')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->unique()->count();

        $recap = (object) [
            'total_revenue_all' => $totalRevenueAll,
            'total_revenue_real' => $totalRevenueReal,
            'total_internal_revenue' => $totalInternalRevenue,
            'total_profit' => $totalProfit,
            'total_modal' => $totalModal,
            'total_transactions' => $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->count(),
            'days_count' => $daysCount ?: 1
        ];

        $dailyBreakdown = Transaction::selectRaw('
                DATE(transacted_at) as date,
                COUNT(*) as total_transactions,
                SUM(total_price) as total_revenue_all,
                SUM(CASE WHEN status IN ("uang_diterima", "belum_kembalian") THEN total_price ELSE 0 END) as total_revenue_real
            ')
            ->whereMonth('transacted_at', $this->selectedMonth)
            ->whereYear('transacted_at', $this->selectedYear)
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Add profit manually to breakdown
        foreach ($dailyBreakdown as $day) {
            $dayTransactions = Transaction::whereDate('transacted_at', $day->date)->whereIn('status', ['uang_diterima', 'belum_kembalian'])->get();
            $day->total_profit = $dayTransactions->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
            $day->month_week = Carbon::parse($day->date)->weekOfMonth;
        }

        $categoryRecap = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->groupBy(fn($tx) => $tx->product->category->name ?? 'Tanpa Kategori')
            ->map(function($group) {
                return (object) [
                    'revenue' => $group->sum('total_price'),
                    'profit' => $group->sum(fn($tx) => $tx->unit_profit * $tx->quantity),
                    'modal' => $group->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity),
                    'qty' => $group->sum('quantity'),
                ];
            })->sortByDesc('revenue');

        return view('livewire.reports.monthly-recap', [
            'recap' => $recap,
            'categoryRecap' => $categoryRecap,
            'dailyBreakdown' => $dailyBreakdown
        ])->layout('layouts.app', ['title' => 'Rekap Bulanan']);
    }
}
