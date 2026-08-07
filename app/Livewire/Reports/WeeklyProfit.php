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
        $activeRole = session('active_role_name');
        if ($activeRole === 'kasir') {
            $isScheduled = \App\Models\CashierSchedule::where('user_id', auth()->id())->exists();
            if ($isScheduled) {
                abort(403, 'Akses ditolak. Kasir terjadwal tidak diperkenankan mengakses menu Bagi Hasil Mingguan.');
            }
        }

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

        $totalShortage = 0;
        $totalSurplus = 0;
        foreach ($dailyRecaps as $recap) {
            // Lewati recap yang uang fisiknya belum diinput (<= 1) agar seluruh
            // omzet hari itu tidak ikut terhitung sebagai kekurangan kas
            if ((float) $recap->actual_cash <= 1) {
                continue;
            }

            $totalRevenueReal = (float) ($recap->total_revenue_real ?? 0);
            $diff = ((float) $recap->actual_cash - (float) ($recap->retained_change_cash ?? 0)) - $totalRevenueReal;
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
            'kas_amount' => $totalProfit * 0.40, // 40%
            'shared_amount' => $totalProfit * 0.60, // 60% (Najmy 30% + Labantik 30%)
        ];

        WeeklyProfitShare::updateOrCreate(
            [
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
                'jurusan_id' => $activeJurusanId,
            ],
            $data
        );

        // Delete old postings for this period first to prevent duplicates on regeneration
        $descriptionPattern = 'Bagi Hasil Mingguan%Periode '.$weekStart->format('d/m/Y').' s.d '.$weekEnd->format('d/m/Y').'%';
        CashTransaction::where('jurusan_id', $activeJurusanId)
            ->where('description', 'like', $descriptionPattern)
            ->delete();

        // 1. Fetch transactions for category grouping
        $transactions = Transaction::with(['product.category', 'product.supplier'])
            ->whereBetween('transacted_at', [
                $weekStart->startOfDay()->toDateTimeString(),
                $weekEnd->endOfDay()->toDateTimeString(),
            ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->get();

        $grouped = $transactions->groupBy(fn($tx) => ($tx->product->supplier_id ?? $tx->supplier_id) ? 'supplier_' . ($tx->product->supplier_id ?? $tx->supplier_id) : 'category_' . ($tx->product->category_id ?? 'other'));

        $najmyUser = \App\Models\User::where('name', 'like', '%Najmy%')->first();
        $najmyName = $najmyUser ? $najmyUser->name : 'Najmy';

        $scaleFactor = ($systemProfit > 0) ? ($totalProfit / $systemProfit) : 1;

        foreach ($grouped as $key => $txs) {
            $firstTx = $txs->first();
            if (str_starts_with($key, 'supplier_')) {
                $supplierName = $firstTx->product->supplier->name ?? 'Supplier';
                $categoryNameClean = trim($supplierName);
                $cashCategoryName = 'Penjualan '.$categoryNameClean;
            } else {
                $categoryName = $firstTx->product->category->name ?? 'Lainnya';
                $categoryNameClean = trim($categoryName);

                $categoryNameLower = strtolower($categoryNameClean);
                if ($categoryNameLower === 'makanan' || $categoryNameLower === 'minuman' || $categoryNameLower === 'makanan & minuman' || $categoryNameLower === 'makanan dan minuman' || $categoryNameLower === 'snack') {
                    $cashCategoryName = 'Jurusan Snack & Minuman';
                } else {
                    $cashCategoryName = 'Penjualan '.$categoryNameClean;
                }
            }

            $catPenjualan = CashCategory::firstOrCreate(
                ['name' => $cashCategoryName, 'jurusan_id' => $activeJurusanId]
            );

            $groupProfit = $txs->sum(fn ($tx) => $tx->unit_profit * $tx->quantity);
            $adjustedGroupProfit = $groupProfit * $scaleFactor;

            // 1. Post Najmy's portion (30%)
            $najmyShare = $adjustedGroupProfit * 0.30;
            if ($najmyShare > 0) {
                CashTransaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'date' => now()->toDateString(),
                    'cash_type' => 'keuntungan',
                    'cash_category_id' => $catPenjualan->id,
                    'type' => 'expense',
                    'amount' => $najmyShare,
                    'description' => 'Bagi Hasil Mingguan dengan ' . $najmyName . ' (30% - Kategori: ' . $categoryNameClean . ') - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y'),
                    'reference' => 'WD-PROFIT-NAJMY-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                ]);
            }

            // 2. Post Labantik's portion (30%)
            $labantikShare = $adjustedGroupProfit * 0.30;
            if ($labantikShare > 0) {
                CashTransaction::create([
                    'jurusan_id' => $activeJurusanId,
                    'date' => now()->toDateString(),
                    'cash_type' => 'keuntungan',
                    'cash_category_id' => $catPenjualan->id,
                    'type' => 'expense',
                    'amount' => $labantikShare,
                    'description' => 'Bagi Hasil Mingguan Labantik (30% - Kategori: ' . $categoryNameClean . ') - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y'),
                    'reference' => 'WD-PROFIT-LABANTIK-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
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

        $totalShortage = 0;
        $totalSurplus = 0;
        foreach ($dailyRecaps as $recap) {
            // Lewati recap yang uang fisiknya belum diinput (<= 1) agar seluruh
            // omzet hari itu tidak ikut terhitung sebagai kekurangan kas
            if ((float) $recap->actual_cash <= 1) {
                continue;
            }

            $totalRevenueReal = (float) ($recap->total_revenue_real ?? 0);
            $diff = ((float) $recap->actual_cash - (float) ($recap->retained_change_cash ?? 0)) - $totalRevenueReal;
            if ($diff < 0) {
                $totalShortage += abs($diff);
            } else {
                $totalSurplus += $diff;
            }
        }

        $currentProfit = $systemProfit - $totalShortage + $totalSurplus;
        $totalRevenue = $weeklyData->total_revenue ?? 0;
        $supplierHak = $weeklyData->supplier_hak ?? 0;

        $najmyUser = \App\Models\User::where('name', 'like', '%Najmy%')->first();
        $najmyName = $najmyUser ? $najmyUser->name : 'Najmy';

        $adminContributions = collect([
            (object) [
                'user' => (object) ['name' => $najmyName],
                'portion_name' => 'Bagi Hasil Najmy',
                'percentage' => '30%',
                'user_profit' => $currentProfit * 0.30,
            ],
            (object) [
                'user' => (object) ['name' => 'Labantik'],
                'portion_name' => 'Bagi Hasil Labantik (Kasir)',
                'percentage' => '30%',
                'user_profit' => $currentProfit * 0.30,
            ]
        ]);

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
