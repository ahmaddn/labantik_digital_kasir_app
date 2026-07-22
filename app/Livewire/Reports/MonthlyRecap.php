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

    public function exportExcel()
    {
        $query = Transaction::with(['product.category'])
            ->whereMonth('transacted_at', $this->selectedMonth)
            ->whereYear('transacted_at', $this->selectedYear);

        $allTransactions = $query->get();

        $activeJurusanId = session('active_jurusan_id');
        $monthlyExpenses = \App\Models\CashTransaction::where('jurusan_id', $activeJurusanId)
            ->whereYear('date', $this->selectedYear)
            ->whereMonth('date', $this->selectedMonth)
            ->where('type', 'expense')
            ->sum('amount');

        $totalRevenueAll = max(0, $allTransactions->sum('total_price') - $monthlyExpenses);
        $totalRevenueReal = max(0, $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price') - $monthlyExpenses);
        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
        $totalInternalRevenue = max(0, $totalRevenueReal - $totalSupplierHak);
        $totalProfit = max(0, $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity) - $monthlyExpenses);
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

        foreach ($dailyBreakdown as $day) {
            $dayTransactions = Transaction::whereDate('transacted_at', $day->date)->whereIn('status', ['uang_diterima', 'belum_kembalian'])->get();
            $day->total_profit = $dayTransactions->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
            
            // Load daily recap audit values
            $recapModel = \App\Models\DailyRecap::whereDate('date', $day->date)->first();
            $day->actual_cash = $recapModel ? $recapModel->actual_cash : null;
            $day->retained_change_cash = $recapModel ? $recapModel->retained_change_cash : 0;
            
            $previousRecap = \App\Models\DailyRecap::where('jurusan_id', session('active_jurusan_id'))
                ->where('date', '<', $day->date)
                ->orderBy('date', 'desc')
                ->first();
            $day->starting_change_cash = $previousRecap ? ($previousRecap->retained_change_cash ?? 0) : 0;
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

        $monthName = \Carbon\Carbon::create(null, $this->selectedMonth)->translatedFormat('F') . ' ' . $this->selectedYear;
        $filename = 'Rekap_Bulanan_' . str_replace(' ', '_', $monthName) . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MonthlyRecapExport($recap, $categoryRecap, $dailyBreakdown, $monthName), $filename);
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

        $activeJurusanId = session('active_jurusan_id');
        $monthlyExpenses = \App\Models\CashTransaction::where('jurusan_id', $activeJurusanId)
            ->whereYear('date', $this->selectedYear)
            ->whereMonth('date', $this->selectedMonth)
            ->where('type', 'expense')
            ->sum('amount');

        $totalRevenueAll = max(0, $allTransactions->sum('total_price') - $monthlyExpenses);
        $totalRevenueReal = max(0, $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum('total_price') - $monthlyExpenses);
        
        $totalSupplierHak = $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->whereNotNull('supplier_id')
            ->sum(fn($tx) => ($tx->unit_price - $tx->unit_profit) * $tx->quantity);
            
        $totalInternalRevenue = max(0, $totalRevenueReal - $totalSupplierHak);

        $totalProfit = max(0, $allTransactions->whereIn('status', ['uang_diterima', 'belum_kembalian'])->sum(fn($tx) => $tx->unit_profit * $tx->quantity) - $monthlyExpenses);
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

        // Add profit and physical cash audit data manually to breakdown
        foreach ($dailyBreakdown as $day) {
            $dayTransactions = Transaction::whereDate('transacted_at', $day->date)->whereIn('status', ['uang_diterima', 'belum_kembalian'])->get();
            $day->total_profit = $dayTransactions->sum(fn($tx) => $tx->unit_profit * $tx->quantity);
            $day->month_week = Carbon::parse($day->date)->weekOfMonth;

            // Load daily recap audit values
            $recapModel = \App\Models\DailyRecap::whereDate('date', $day->date)->first();
            $day->actual_cash = $recapModel ? $recapModel->actual_cash : null;
            $day->retained_change_cash = $recapModel ? $recapModel->retained_change_cash : 0;

            $previousRecap = \App\Models\DailyRecap::where('jurusan_id', session('active_jurusan_id'))
                ->where('date', '<', $day->date)
                ->orderBy('date', 'desc')
                ->first();
            $day->starting_change_cash = $previousRecap ? ($previousRecap->retained_change_cash ?? 0) : 0;
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

        return view('livewire.reports.monthly-recap', [
            'recap' => $recap,
            'categoryRecap' => $categoryRecap,
            'dailyBreakdown' => $dailyBreakdown
        ])->layout('layouts.app', ['title' => 'Rekap Bulanan']);
    }
}
