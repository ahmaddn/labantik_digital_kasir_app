<?php

namespace App\Livewire\Reports;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class YearlyRecap extends Component
{
    public $selectedYear;
    public $availableYears = [];

    public function mount($year = null)
    {
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

    public function exportExcel()
    {
        $query = Transaction::with(['product.category'])
            ->whereYear('transacted_at', $this->selectedYear);

        $allTransactions = $query->get();

        $totalRevenueAll = $allTransactions->sum('total_price');
        $totalRevenueReal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');
        
        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
            
        $totalInternalRevenue = $totalRevenueReal - $totalSupplierHak;

        $totalProfit = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
        $totalModal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
        $monthsCount = $allTransactions->pluck('transacted_at')->map(fn($date) => Carbon::parse($date)->format('Y-m'))->unique()->count();

        $recap = (object) [
            'total_revenue_all' => $totalRevenueAll,
            'total_revenue_real' => $totalRevenueReal,
            'total_internal_revenue' => $totalInternalRevenue,
            'total_profit' => $totalProfit,
            'total_modal' => $totalModal,
            'total_transactions' => $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->count(),
            'months_count' => $monthsCount ?: 1
        ];

        $monthlyBreakdown = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthTransactions = $allTransactions->filter(fn($tx) => Carbon::parse($tx->transacted_at)->month == $m);
            if ($monthTransactions->isNotEmpty()) {
                $recapData = \App\Models\DailyRecap::whereYear('date', $this->selectedYear)
                    ->whereMonth('date', $m)
                    ->selectRaw('SUM(actual_cash) as total_actual, SUM(retained_change_cash) as total_retained, COUNT(CASE WHEN actual_cash > 0 THEN 1 END) as audited_days')
                    ->first();

                $monthlyBreakdown[] = (object) [
                    'month' => $m,
                    'total_transactions' => $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->count(),
                    'total_revenue_real' => $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price'),
                    'total_profit' => $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity),
                    'actual_cash' => $recapData ? $recapData->total_actual : null,
                    'retained_change_cash' => $recapData ? $recapData->total_retained : 0,
                    'audited_days' => $recapData ? ($recapData->audited_days ?? 0) : 0,
                ];
            }
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

        $filename = 'Rekap_Tahunan_' . $this->selectedYear . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\YearlyRecapExport($recap, $categoryRecap, $monthlyBreakdown, $this->selectedYear), $filename);
    }

    public function render()
    {
        $query = Transaction::with(['product.category'])
            ->whereYear('transacted_at', $this->selectedYear);

        $allTransactions = $query->get();

        if ($allTransactions->isEmpty()) {
            return view('livewire.reports.yearly-recap', [
                'recap' => null,
                'monthlyBreakdown' => []
            ])->layout('layouts.app', ['title' => 'Rekap Tahunan']);
        }

        $totalRevenueAll = $allTransactions->sum('total_price');
        $totalRevenueReal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price');
        
        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
            
        $totalInternalRevenue = $totalRevenueReal - $totalSupplierHak;

        $totalProfit = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
        $totalModal = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
        $monthsCount = $allTransactions->pluck('transacted_at')->map(fn($date) => Carbon::parse($date)->format('Y-m'))->unique()->count();

        $recap = (object) [
            'total_revenue_all' => $totalRevenueAll,
            'total_revenue_real' => $totalRevenueReal,
            'total_internal_revenue' => $totalInternalRevenue,
            'total_profit' => $totalProfit,
            'total_modal' => $totalModal,
            'total_transactions' => $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->count(),
            'months_count' => $monthsCount ?: 1
        ];

        $monthlyBreakdown = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthTransactions = $allTransactions->filter(fn($tx) => Carbon::parse($tx->transacted_at)->month == $m);
            if ($monthTransactions->isNotEmpty()) {
                $recapData = \App\Models\DailyRecap::whereYear('date', $this->selectedYear)
                    ->whereMonth('date', $m)
                    ->selectRaw('SUM(actual_cash) as total_actual, SUM(retained_change_cash) as total_retained, COUNT(CASE WHEN actual_cash > 0 THEN 1 END) as audited_days')
                    ->first();

                $monthlyBreakdown[] = (object) [
                    'month' => $m,
                    'total_transactions' => $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->count(),
                    'total_revenue_real' => $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price'),
                    'total_profit' => $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity),
                    'actual_cash' => $recapData ? $recapData->total_actual : null,
                    'retained_change_cash' => $recapData ? $recapData->total_retained : 0,
                    'audited_days' => $recapData ? ($recapData->audited_days ?? 0) : 0,
                ];
            }
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

        return view('livewire.reports.yearly-recap', [
            'recap' => $recap,
            'categoryRecap' => $categoryRecap,
            'monthlyBreakdown' => $monthlyBreakdown
        ])->layout('layouts.app', ['title' => 'Rekap Tahunan']);
    }
}
