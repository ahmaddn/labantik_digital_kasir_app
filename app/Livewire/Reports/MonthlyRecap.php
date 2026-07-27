<?php

namespace App\Livewire\Reports;

use App\Models\Transaction;
use App\Models\DailyRecap;
use App\Models\CashTransaction;
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

    private function getRecapData($activeJurusanId)
    {
        $monthlyExpenses = CashTransaction::where('jurusan_id', $activeJurusanId)
            ->whereYear('date', $this->selectedYear)
            ->whereMonth('date', $this->selectedMonth)
            ->where('type', 'expense')
            ->sum('amount');

        // 1. Calculate monthly aggregates
        $aggregates = Transaction::whereMonth('transacted_at', $this->selectedMonth)
            ->whereYear('transacted_at', $this->selectedYear)
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
                COUNT(DISTINCT DATE(transacted_at)) as days_count
            ")
            ->first();

        if (!$aggregates || !$aggregates->total_transactions) {
            return null;
        }

        $totalRevenueAll = max(0, ($aggregates->total_revenue_all ?? 0) - $monthlyExpenses);
        $totalRevenueReal = max(0, ($aggregates->total_revenue_real ?? 0) - $monthlyExpenses);
        $totalSupplierHak = $aggregates->total_supplier_hak ?? 0;
        $totalInternalRevenue = max(0, $totalRevenueReal - $totalSupplierHak);
        $totalProfit = max(0, ($aggregates->total_profit ?? 0) - $monthlyExpenses);
        $totalModal = $aggregates->total_modal ?? 0;
        $daysCount = $aggregates->days_count ?? 1;

        $recap = (object) [
            'total_revenue_all' => $totalRevenueAll,
            'total_revenue_real' => $totalRevenueReal,
            'total_internal_revenue' => $totalInternalRevenue,
            'total_profit' => $totalProfit,
            'total_modal' => $totalModal,
            'total_transactions' => $aggregates->total_transactions ?? 0,
            'days_count' => $daysCount ?: 1
        ];

        // 2. Fetch daily breakdown with profit calculated in DB
        $dailyBreakdown = Transaction::selectRaw('
                DATE(transacted_at) as date,
                COUNT(*) as total_transactions,
                SUM(total_price) as total_revenue_all,
                SUM(CASE WHEN status IN ("uang_diterima", "belum_kembalian") THEN total_price ELSE 0 END) as total_revenue_real,
                SUM(CASE WHEN status IN ("uang_diterima", "belum_kembalian") THEN unit_profit * quantity ELSE 0 END) as total_profit
            ')
            ->whereMonth('transacted_at', $this->selectedMonth)
            ->whereYear('transacted_at', $this->selectedYear)
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // 3. Load daily recaps and calculate starting change cash in-memory
        $dates = $dailyBreakdown->pluck('date')->sort()->values();
        $firstDate = $dates->first();
        
        $initialPrev = null;
        if ($firstDate) {
            $initialPrev = DailyRecap::where('jurusan_id', $activeJurusanId)
                ->where('date', '<', $firstDate)
                ->orderBy('date', 'desc')
                ->first();
        }

        $recapsMap = [];
        $recapsWithPrevious = [];
        if ($firstDate) {
            $lastDate = $dates->last();
            $allRecaps = DailyRecap::where('jurusan_id', $activeJurusanId)
                ->whereBetween('date', [$firstDate, $lastDate])
                ->orderBy('date')
                ->get();

            foreach ($allRecaps as $r) {
                $recapsMap[Carbon::parse($r->date)->toDateString()] = $r;
            }

            $prevChangeCash = $initialPrev ? ($initialPrev->retained_change_cash ?? 0) : 0;
            foreach ($allRecaps as $r) {
                $recapsWithPrevious[Carbon::parse($r->date)->toDateString()] = $prevChangeCash;
                $prevChangeCash = $r->retained_change_cash ?? 0;
            }
        }

        foreach ($dailyBreakdown as $day) {
            $dateStr = Carbon::parse($day->date)->toDateString();
            $day->month_week = Carbon::parse($day->date)->weekOfMonth;

            // Load daily recap audit values from memory map
            $recapModel = $recapsMap[$dateStr] ?? null;
            $day->actual_cash = $recapModel ? $recapModel->actual_cash : null;
            $day->retained_change_cash = $recapModel ? $recapModel->retained_change_cash : 0;
            $day->starting_change_cash = $recapsWithPrevious[$dateStr] ?? 0;
        }

        // 4. Category breakdown directly from DB
        $categoryRecap = Transaction::whereMonth('transacted_at', $this->selectedMonth)
            ->whereYear('transacted_at', $this->selectedYear)
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
            'dailyBreakdown' => $dailyBreakdown
        ];
    }

    public function exportExcel()
    {
        $activeJurusanId = session('active_jurusan_id');
        $data = $this->getRecapData($activeJurusanId);

        if (!$data) {
            return;
        }

        $monthName = Carbon::create(null, $this->selectedMonth)->translatedFormat('F') . ' ' . $this->selectedYear;
        $filename = 'Rekap_Bulanan_' . str_replace(' ', '_', $monthName) . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MonthlyRecapExport(
            $data['recap'], 
            $data['categoryRecap'], 
            $data['dailyBreakdown'], 
            $monthName
        ), $filename);
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $data = $this->getRecapData($activeJurusanId);

        if (!$data) {
            return view('livewire.reports.monthly-recap', [
                'recap' => null,
                'categoryRecap' => [],
                'dailyBreakdown' => []
            ])->layout('layouts.app', ['title' => 'Rekap Bulanan']);
        }

        return view('livewire.reports.monthly-recap', [
            'recap' => $data['recap'],
            'categoryRecap' => $data['categoryRecap'],
            'dailyBreakdown' => $data['dailyBreakdown']
        ])->layout('layouts.app', ['title' => 'Rekap Bulanan']);
    }
}
