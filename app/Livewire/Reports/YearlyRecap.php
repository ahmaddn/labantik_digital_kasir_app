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

        $activeJurusanId = session('active_jurusan_id');
        $yearlyExpenses = \App\Models\CashTransaction::where('jurusan_id', $activeJurusanId)
            ->whereYear('date', $this->selectedYear)
            ->where('type', 'expense')
            ->sum('amount');

        $totalRevenueAll = max(0, $allTransactions->sum('total_price') - $yearlyExpenses);
        $totalRevenueReal = max(0, $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price') - $yearlyExpenses);
        
        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
            
        $totalInternalRevenue = max(0, $totalRevenueReal - $totalSupplierHak);

        $totalProfit = max(0, $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity) - $yearlyExpenses);
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
                $recaps = \App\Models\DailyRecap::whereYear('date', $this->selectedYear)
                    ->whereMonth('date', $m)
                    ->orderBy('date')
                    ->get();

                $totalActual = 0;
                $totalRetained = 0;
                $totalStarting = 0;
                $auditedDays = 0;
                $hasData = false;

                foreach ($recaps as $r) {
                    if ($r->actual_cash > 0) {
                        $totalActual += $r->actual_cash;
                        $totalRetained += $r->retained_change_cash ?? 0;
                        
                        $prev = \App\Models\DailyRecap::where('jurusan_id', session('active_jurusan_id'))
                            ->where('date', '<', $r->date)
                            ->orderBy('date', 'desc')
                            ->first();
                        $totalStarting += $prev ? ($prev->retained_change_cash ?? 0) : 0;
                        $auditedDays++;
                        $hasData = true;
                    }
                }

                $monthExpenses = \App\Models\CashTransaction::where('jurusan_id', $activeJurusanId)
                    ->whereYear('date', $this->selectedYear)
                    ->whereMonth('date', $m)
                    ->where('type', 'expense')
                    ->sum('amount');

                $monthlyBreakdown[] = (object) [
                    'month' => $m,
                    'total_transactions' => $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->count(),
                    'total_revenue_real' => max(0, $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price') - $monthExpenses),
                    'total_profit' => max(0, $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity) - $monthExpenses),
                    'actual_cash' => $hasData ? $totalActual : null,
                    'retained_change_cash' => $totalRetained,
                    'starting_change_cash' => $totalStarting,
                    'audited_days' => $auditedDays,
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

        $activeJurusanId = session('active_jurusan_id');
        $yearlyExpenses = \App\Models\CashTransaction::where('jurusan_id', $activeJurusanId)
            ->whereYear('date', $this->selectedYear)
            ->where('type', 'expense')
            ->sum('amount');

        $totalRevenueAll = max(0, $allTransactions->sum('total_price') - $yearlyExpenses);
        $totalRevenueReal = max(0, $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price') - $yearlyExpenses);
        
        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
            
        $totalInternalRevenue = max(0, $totalRevenueReal - $totalSupplierHak);

        $totalProfit = max(0, $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity) - $yearlyExpenses);
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
                $recaps = \App\Models\DailyRecap::whereYear('date', $this->selectedYear)
                    ->whereMonth('date', $m)
                    ->orderBy('date')
                    ->get();

                $totalActual = 0;
                $totalRetained = 0;
                $totalStarting = 0;
                $auditedDays = 0;
                $hasData = false;

                foreach ($recaps as $r) {
                    if ($r->actual_cash > 0) {
                        $totalActual += $r->actual_cash;
                        $totalRetained += $r->retained_change_cash ?? 0;
                        
                        $prev = \App\Models\DailyRecap::where('jurusan_id', session('active_jurusan_id'))
                            ->where('date', '<', $r->date)
                            ->orderBy('date', 'desc')
                            ->first();
                        $totalStarting += $prev ? ($prev->retained_change_cash ?? 0) : 0;
                        $auditedDays++;
                        $hasData = true;
                    }
                }

                $monthExpenses = \App\Models\CashTransaction::where('jurusan_id', $activeJurusanId)
                    ->whereYear('date', $this->selectedYear)
                    ->whereMonth('date', $m)
                    ->where('type', 'expense')
                    ->sum('amount');

                $monthlyBreakdown[] = (object) [
                    'month' => $m,
                    'total_transactions' => $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->count(),
                    'total_revenue_real' => max(0, $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price') - $monthExpenses),
                    'total_profit' => max(0, $monthTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity) - $monthExpenses),
                    'actual_cash' => $hasData ? $totalActual : null,
                    'retained_change_cash' => $totalRetained,
                    'starting_change_cash' => $totalStarting,
                    'audited_days' => $auditedDays,
                ];
            }
        }

        $categoryRecap = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->groupBy(fn($tx) => $tx->product->category_id ?? 'null')
            ->map(function($group) {
                $first = $group->first();
                return (object) [
                    'id' => $first->product->category_id ?? 'null',
                    'name' => $first->product->category->name ?? 'Tanpa Kategori',
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
