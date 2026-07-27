<?php

namespace App\Livewire\Reports;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Transaction;
use App\Models\WeeklyProfitShare;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class WeeklyProfit extends Component
{
    use WithPagination;

    public $startDate;

    public $endDate;

    public $viewMode = 'weekly';

    public $showDeleteModal = false;

    public $reportToDeleteId = null;

    public $selectedMonth = null;

    public $currentYear;

    public function mount()
    {
        $this->startDate = now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->endDate = now()->startOfWeek(Carbon::MONDAY)->addDays(4)->toDateString();
        $this->currentYear = now()->year;
        $this->selectedMonth = now()->month;
    }

    public function confirmDelete($id)
    {
        $this->reportToDeleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->reportToDeleteId) {
            $report = WeeklyProfitShare::find($this->reportToDeleteId);
            if ($report) {
                $activeJurusanId = session('active_jurusan_id');
                $weekStart = Carbon::parse($report->week_start);
                $weekEnd = Carbon::parse($report->week_end);

                // Delete all cash transactions matching this period
                $descriptionPattern = 'Bagi Hasil Mingguan%Periode '.$weekStart->format('d/m/Y').' s.d '.$weekEnd->format('d/m/Y').'%';
                CashTransaction::where('jurusan_id', $activeJurusanId)
                    ->where('description', 'like', $descriptionPattern)
                    ->delete();

                $report->delete();
            }
            $this->reportToDeleteId = null;
            $this->showDeleteModal = false;
            $this->dispatch('toast', message: 'Laporan berhasil dihapus.');
        }
    }

    public function generateReport()
    {
        $activeJurusanId = session('active_jurusan_id');

        $weekStart = Carbon::parse($this->startDate);
        $weekEnd = Carbon::parse($this->endDate);

        $weekNumber = $weekEnd->weekOfMonth;
        $monthName = $weekEnd->translatedFormat('F Y');

        // Calculate total profit (Including shop's share from supplier products)
        $systemProfit = Transaction::whereBetween('transacted_at', [
            $weekStart->startOfDay()->toDateTimeString(),
            $weekEnd->endOfDay()->toDateTimeString(),
        ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->sum(DB::raw('unit_profit * quantity'));

        // Calculate total shortage from daily recaps
        $dailyRecaps = \App\Models\DailyRecap::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->where('jurusan_id', $activeJurusanId)
            ->get();

        // Fetch daily revenues for the week in one query
        $dailyRevenues = Transaction::whereBetween('transacted_at', [
            $weekStart->startOfDay()->toDateTimeString(),
            $weekEnd->endOfDay()->toDateTimeString(),
        ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->selectRaw('DATE(transacted_at) as date, SUM(total_price) as total_revenue')
            ->groupBy('date')
            ->pluck('total_revenue', 'date')
            ->toArray();

        $totalShortage = 0;
        $totalSurplus = 0;
        foreach ($dailyRecaps as $recap) {
            $totalRevenueReal = $dailyRevenues[$recap->date] ?? 0;
            $diff = ((float) $recap->actual_cash - (float) ($recap->retained_change_cash ?? 0)) - (float) $totalRevenueReal;
            if ($diff < 0) {
                $totalShortage += abs($diff);
            } else {
                $totalSurplus += $diff;
            }
        }

        $totalProfit = $systemProfit - $totalShortage + $totalSurplus;

        if ($totalProfit <= 0) {
            $this->dispatch('toast', message: 'Tidak ada keuntungan pada periode ini.', type: 'error');

            return;
        }

        $data = [
            'jurusan_id' => $activeJurusanId,
            'month_name' => $monthName,
            'week_number' => $weekNumber,
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'total_profit' => $totalProfit,
            'kas_amount' => $totalProfit * 0.5,
            'shared_amount' => $totalProfit * 0.5,
        ];

        WeeklyProfitShare::updateOrCreate(
            [
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
                'jurusan_id' => $activeJurusanId,
            ],
            $data
        );

        // Fetch contributors for this week grouped by user and product category/supplier
        $adminContributionsRaw = Transaction::join('products', 'transactions.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->select(
                'transactions.user_id',
                'products.category_id',
                'product_categories.name as category_name',
                'products.supplier_id',
                'suppliers.name as supplier_name',
                DB::raw('SUM(transactions.unit_profit * transactions.quantity) as user_profit')
            )
            ->whereBetween('transactions.transacted_at', [
                $weekStart->startOfDay()->toDateTimeString(),
                $weekEnd->endOfDay()->toDateTimeString(),
            ])
            ->where('transactions.jurusan_id', $activeJurusanId)
            ->whereIn('transactions.status', ['uang_diterima', 'belum_kembalian'])
            ->groupBy('transactions.user_id', 'products.category_id', 'product_categories.name', 'products.supplier_id', 'suppliers.name')
            ->get();

        $adminContributions = [];
        foreach ($adminContributionsRaw as $contrib) {
            $contribUser = \App\Models\User::find($contrib->user_id);
            $categoryNameClean = trim($contrib->supplier_id ? $contrib->supplier_name : $contrib->category_name);
            
            $adminContributions[] = (object) [
                'user' => $contribUser,
                'user_profit' => $contrib->user_profit,
                'category_name' => $categoryNameClean,
                'supplier_id' => $contrib->supplier_id,
            ];
        }

        // Scale factor for contributions
        $scaleFactor = ($systemProfit > 0) ? ($totalProfit / $systemProfit) : 1;

        // Delete old postings for this period first to prevent duplicates on regeneration
        $descriptionPattern = 'Bagi Hasil Mingguan%Periode '.$weekStart->format('d/m/Y').' s.d '.$weekEnd->format('d/m/Y').'%';
        CashTransaction::where('jurusan_id', $activeJurusanId)
            ->where('description', 'like', $descriptionPattern)
            ->delete();

        foreach ($adminContributions as $contrib) {
            $userName = $contrib->user ? $contrib->user->name : 'Unknown User';
            $userShare = ($contrib->user_profit * $scaleFactor) * 0.5;

            if ($userShare > 0) {
                $categoryNameClean = trim($contrib->category_name);
                $categoryNameLower = strtolower($categoryNameClean);
                if ($contrib->supplier_id) {
                    $cashCategoryName = 'Penjualan '.$categoryNameClean;
                } else {
                    if ($categoryNameLower === 'makanan' || $categoryNameLower === 'minuman' || $categoryNameLower === 'makanan & minuman' || $categoryNameLower === 'makanan dan minuman' || $categoryNameLower === 'snack') {
                        $cashCategoryName = 'Jurusan Snack & Minuman';
                    } else {
                        $cashCategoryName = 'Penjualan '.$categoryNameClean;
                    }
                }

                $catPenjualan = CashCategory::firstOrCreate(
                    ['name' => $cashCategoryName, 'jurusan_id' => $activeJurusanId]
                );

                $userDescription = 'Bagi Hasil Mingguan dengan '.$userName.' (Kategori: '.$categoryNameClean.') - Periode '.$weekStart->format('d/m/Y').' s.d '.$weekEnd->format('d/m/Y');

                CashTransaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'date' => now()->toDateString(),
                    'cash_type' => 'keuntungan',
                    'cash_category_id' => $catPenjualan->id,
                    'type' => 'expense',
                    'amount' => $userShare,
                    'description' => $userDescription,
                    'reference' => 'WD-PROFIT-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(2))),
                ]);
            }
        }

        $this->dispatch('toast', message: 'Laporan bagi hasil berhasil dibuat dan diposting ke Buku Kas!');
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');
        $reports = WeeklyProfitShare::where('jurusan_id', $activeJurusanId)->orderByDesc('week_end')->paginate(10);

        $weekStart = Carbon::parse($this->startDate);
        $weekEnd = Carbon::parse($this->endDate);

        $weeklyData = Transaction::whereBetween('transacted_at', [
            $weekStart->startOfDay()->toDateTimeString(),
            $weekEnd->endOfDay()->toDateTimeString(),
        ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->selectRaw('
                SUM(total_price) as total_revenue,
                SUM(CASE WHEN supplier_id IS NOT NULL THEN (unit_price - unit_profit) * quantity ELSE 0 END) as supplier_hak,
                SUM(unit_profit * quantity) as internal_profit
            ')
            ->first();

        $systemProfit = $weeklyData->internal_profit ?? 0;

        // Calculate total shortage from daily recaps for the current week
        $dailyRecaps = \App\Models\DailyRecap::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->where('jurusan_id', $activeJurusanId)
            ->get();

        // Fetch daily revenues for the week in one query
        $dailyRevenues = Transaction::whereBetween('transacted_at', [
            $weekStart->startOfDay()->toDateTimeString(),
            $weekEnd->endOfDay()->toDateTimeString(),
        ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->selectRaw('DATE(transacted_at) as date, SUM(total_price) as total_revenue')
            ->groupBy('date')
            ->pluck('total_revenue', 'date')
            ->toArray();

        $totalShortage = 0;
        $totalSurplus = 0;
        foreach ($dailyRecaps as $recap) {
            $totalRevenueReal = $dailyRevenues[$recap->date] ?? 0;
            $diff = ((float) $recap->actual_cash - (float) ($recap->retained_change_cash ?? 0)) - (float) $totalRevenueReal;
            if ($diff < 0) {
                $totalShortage += abs($diff);
            } else {
                $totalSurplus += $diff;
            }
        }

        $currentProfit = $systemProfit - $totalShortage + $totalSurplus;
        $totalRevenue = $weeklyData->total_revenue ?? 0;
        $supplierHak = $weeklyData->supplier_hak ?? 0;

        $adminContributions = Transaction::with('user')
            ->select('user_id', DB::raw('SUM(unit_profit * quantity) as user_profit'))
            ->whereBetween('transacted_at', [
                $weekStart->startOfDay()->toDateTimeString(),
                $weekEnd->endOfDay()->toDateTimeString(),
            ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->groupBy('user_id')
            ->get();

        $scaleFactor = ($systemProfit > 0) ? ($currentProfit / $systemProfit) : 1;
        foreach ($adminContributions as $contrib) {
            $contrib->user_profit = $contrib->user_profit * $scaleFactor;
        }

        // Monthly Summary Logic - Filter by month and current year
        $monthName = Carbon::createFromDate($this->currentYear, $this->selectedMonth, 1)->translatedFormat('F Y');
        $monthlyReports = WeeklyProfitShare::select(
            'month_name',
            DB::raw('SUM(total_profit) as total_profit'),
            DB::raw('SUM(kas_amount) as total_kas'),
            DB::raw('SUM(shared_amount) as total_shared'),
            DB::raw('COUNT(*) as weeks_count'),
            DB::raw('MIN(created_at) as created_at')
        )
            ->where('month_name', 'like', '%'.$monthName.'%')
            ->where('jurusan_id', $activeJurusanId)
            ->groupBy('month_name')
            ->orderByDesc(DB::raw('MAX(week_end)'))
            ->get();

        // Yearly Summary Logic - Current year only
        $yearlyData = WeeklyProfitShare::select(
            DB::raw('SUM(total_profit) as total_profit'),
            DB::raw('SUM(kas_amount) as total_kas'),
            DB::raw('SUM(shared_amount) as total_shared'),
            DB::raw('COUNT(DISTINCT week_number) as total_weeks'),
            DB::raw('COUNT(DISTINCT month_name) as total_months')
        )
            ->whereYear('week_end', $this->currentYear)
            ->where('jurusan_id', $activeJurusanId)
            ->first();

        // Get list of available months with data for filter
        $availableMonths = WeeklyProfitShare::selectRaw('DISTINCT MONTH(week_end) as month, YEAR(week_end) as year')
            ->whereYear('week_end', $this->currentYear)
            ->where('jurusan_id', $activeJurusanId)
            ->orderBy('month', 'desc')
            ->get();

        // Get all monthly data for yearly breakdown
        $allMonthlyData = WeeklyProfitShare::select(
            'month_name',
            DB::raw('SUM(total_profit) as total_profit'),
            DB::raw('SUM(kas_amount) as total_kas'),
            DB::raw('SUM(shared_amount) as total_shared')
        )
            ->whereYear('week_end', $this->currentYear)
            ->where('jurusan_id', $activeJurusanId)
            ->groupBy('month_name')
            ->orderByDesc(DB::raw('MAX(week_end)'))
            ->get();

        return view('livewire.reports.weekly-profit', [
            'reports' => $reports,
            'monthlyReports' => $monthlyReports,
            'yearlyData' => $yearlyData,
            'allMonthlyData' => $allMonthlyData,
            'availableMonths' => $availableMonths,
            'currentWeek' => [
                'start' => $weekStart,
                'end' => $weekEnd,
                'profit' => $currentProfit,
                'total_revenue' => $totalRevenue,
                'supplier_hak' => $supplierHak,
                'adminContributions' => $adminContributions,
            ],
            'canProcess' => in_array(now()->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]),
        ])->layout('layouts.app', ['title' => 'Bagi Hasil Mingguan']);
    }
}
