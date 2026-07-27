<?php

namespace App\Livewire\Reports;

use App\Models\Transaction;
use App\Models\DailyRecap;
use App\Models\CashTransaction;
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

    private function getRecapData($activeJurusanId)
    {
        $yearlyExpenses = CashTransaction::where('jurusan_id', $activeJurusanId)
            ->whereYear('date', $this->selectedYear)
            ->where('type', 'expense')
            ->sum('amount');

        // 1. Calculate yearly aggregates directly from DB
        $aggregates = Transaction::whereYear('transacted_at', $this->selectedYear)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->selectRaw("
                SUM(total_price) as total_revenue_all,
                SUM(CASE WHEN status IN ('uang_diterima', 'belum_kembalian') THEN total_price ELSE 0 END) as total_revenue_real,
                SUM(CASE WHEN status IN ('uang_diterima', 'belum_kembalian') AND supplier_id IS NOT NULL THEN (unit_price - unit_profit) * quantity ELSE 0 END) as total_supplier_hak,
                SUM(CASE WHEN status IN ('uang_diterima', 'belum_kembalian') THEN unit_profit * quantity ELSE 0 END) as total_profit,
                SUM(CASE WHEN status IN ('uang_diterima', 'belum_kembalian') THEN (unit_price - unit_profit) * quantity ELSE 0 END) as total_modal,
                COUNT(CASE WHEN status IN ('uang_diterima', 'belum_kembalian') THEN 1 END) as total_transactions,
                COUNT(DISTINCT DATE_FORMAT(transacted_at, '%Y-%m')) as months_count
            ")
            ->first();

        if (!$aggregates || !$aggregates->total_transactions) {
            return null;
        }

        $totalRevenueAll = max(0, ($aggregates->total_revenue_all ?? 0) - $yearlyExpenses);
        $totalRevenueReal = max(0, ($aggregates->total_revenue_real ?? 0) - $yearlyExpenses);
        $totalSupplierHak = $aggregates->total_supplier_hak ?? 0;
        $totalInternalRevenue = max(0, $totalRevenueReal - $totalSupplierHak);
        $totalProfit = max(0, ($aggregates->total_profit ?? 0) - $yearlyExpenses);
        $totalModal = $aggregates->total_modal ?? 0;
        $monthsCount = $aggregates->months_count ?? 1;

        $recap = (object) [
            'total_revenue_all' => $totalRevenueAll,
            'total_revenue_real' => $totalRevenueReal,
            'total_internal_revenue' => $totalInternalRevenue,
            'total_profit' => $totalProfit,
            'total_modal' => $totalModal,
            'total_transactions' => $aggregates->total_transactions ?? 0,
            'months_count' => $monthsCount ?: 1
        ];

        // 2. Fetch monthly stats
        $monthlyTxStats = Transaction::whereYear('transacted_at', $this->selectedYear)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->selectRaw("
                MONTH(transacted_at) as month_num,
                COUNT(CASE WHEN status IN ('uang_diterima', 'belum_kembalian') THEN 1 END) as total_transactions,
                SUM(CASE WHEN status IN ('uang_diterima', 'belum_kembalian') THEN total_price ELSE 0 END) as total_revenue_real,
                SUM(CASE WHEN status IN ('uang_diterima', 'belum_kembalian') THEN unit_profit * quantity ELSE 0 END) as total_profit
            ")
            ->groupBy('month_num')
            ->get()
            ->keyBy('month_num');

        // 3. Cache and optimize CashTransaction expenses by month
        $yearlyExpensesByMonth = CashTransaction::where('jurusan_id', $activeJurusanId)
            ->whereYear('date', $this->selectedYear)
            ->where('type', 'expense')
            ->selectRaw('MONTH(date) as month_num, SUM(amount) as total_amount')
            ->groupBy('month_num')
            ->pluck('total_amount', 'month_num')
            ->toArray();

        // 4. Load all DailyRecaps for the selected year once
        $recaps = DailyRecap::whereYear('date', $this->selectedYear)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->orderBy('date')
            ->get();

        // Fetch initial previous recap before the first recap of this year starts
        $firstRecapDate = $recaps->first()?->date;
        $initialPrev = null;
        if ($firstRecapDate) {
            $initialPrev = DailyRecap::where('jurusan_id', $activeJurusanId)
                ->where('date', '<', $firstRecapDate)
                ->orderBy('date', 'desc')
                ->first();
        }

        // Build sliding window starting change cash in memory
        $recapsWithStarting = [];
        $prevChangeCash = $initialPrev ? ($initialPrev->retained_change_cash ?? 0) : 0;
        foreach ($recaps as $r) {
            $recapsWithStarting[$r->id] = $prevChangeCash;
            $prevChangeCash = $r->retained_change_cash ?? 0;
        }

        $monthlyBreakdown = [];
        for ($m = 1; $m <= 12; $m++) {
            $txStat = $monthlyTxStats->get($m);
            if ($txStat && $txStat->total_transactions > 0) {
                // Filter recaps for this month in-memory
                $monthRecaps = $recaps->filter(fn($r) => Carbon::parse($r->date)->month == $m);

                $totalActual = 0;
                $totalRetained = 0;
                $totalStarting = 0;
                $auditedDays = 0;
                $hasData = false;

                foreach ($monthRecaps as $r) {
                    if ($r->actual_cash > 0) {
                        $totalActual += $r->actual_cash;
                        $totalRetained += $r->retained_change_cash ?? 0;
                        $totalStarting += $recapsWithStarting[$r->id] ?? 0;
                        $auditedDays++;
                        $hasData = true;
                    }
                }

                $monthExpenses = $yearlyExpensesByMonth[$m] ?? 0;

                $monthlyBreakdown[] = (object) [
                    'month' => $m,
                    'total_transactions' => $txStat->total_transactions,
                    'total_revenue_real' => max(0, $txStat->total_revenue_real - $monthExpenses),
                    'total_profit' => max(0, $txStat->total_profit - $monthExpenses),
                    'actual_cash' => $hasData ? $totalActual : null,
                    'retained_change_cash' => $totalRetained,
                    'starting_change_cash' => $totalStarting,
                    'audited_days' => $auditedDays,
                ];
            }
        }

        // 5. Category breakdown
        $categoryRecap = Transaction::whereYear('transacted_at', $this->selectedYear)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->join('products', 'transactions.product_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->selectRaw("
                product_categories.id as id,
                COALESCE(product_categories.name, 'Tanpa Kategori') as name,
                SUM(transactions.total_price) as revenue,
                SUM(transactions.unit_profit * transactions.quantity) as profit,
                SUM((transactions.unit_price - transactions.unit_profit) * transactions.quantity) as modal,
                SUM(transactions.quantity) as qty
            ")
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderByDesc('revenue')
            ->get();

        return [
            'recap' => $recap,
            'categoryRecap' => $categoryRecap,
            'monthlyBreakdown' => $monthlyBreakdown
        ];
    }

    public function exportExcel()
    {
        $activeJurusanId = session('active_jurusan_id');
        $data = $this->getRecapData($activeJurusanId);

        if (!$data) {
            return;
        }

        $filename = 'Rekap_Tahunan_' . $this->selectedYear . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\YearlyRecapExport(
            $data['recap'], 
            $data['categoryRecap'], 
            $data['monthlyBreakdown'], 
            $this->selectedYear
        ), $filename);
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $data = $this->getRecapData($activeJurusanId);

        if (!$data) {
            return view('livewire.reports.yearly-recap', [
                'recap' => null,
                'monthlyBreakdown' => []
            ])->layout('layouts.app', ['title' => 'Rekap Tahunan']);
        }

        return view('livewire.reports.yearly-recap', [
            'recap' => $data['recap'],
            'categoryRecap' => $data['categoryRecap'],
            'monthlyBreakdown' => $data['monthlyBreakdown']
        ])->layout('layouts.app', ['title' => 'Rekap Tahunan']);
    }
}
