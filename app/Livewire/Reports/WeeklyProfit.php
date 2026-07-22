<?php

namespace App\Livewire\Reports;

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
                
                $description = 'Bagi Hasil Mingguan (Sistem) - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y');
                
                // Delete the cash transaction
                \App\Models\CashTransaction::where('jurusan_id', $activeJurusanId)
                    ->where('description', $description)
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
        $totalProfit = Transaction::whereBetween('transacted_at', [
            $weekStart->startOfDay()->toDateTimeString(),
            $weekEnd->endOfDay()->toDateTimeString(),
        ])
            ->where('jurusan_id', $activeJurusanId)
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->sum(DB::raw('unit_profit * quantity'));

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
                'jurusan_id' => $activeJurusanId
            ],
            $data
        );

        // Auto post expense to cash book
        $catBagiHasil = \App\Models\CashCategory::firstOrCreate(
            ['name' => 'Bagi Hasil Mingguan', 'jurusan_id' => $activeJurusanId]
        );

        $description = 'Bagi Hasil Mingguan (Sistem) - Periode ' . $weekStart->format('d/m/Y') . ' s.d ' . $weekEnd->format('d/m/Y');

        \App\Models\CashTransaction::updateOrCreate(
            [
                'jurusan_id' => $activeJurusanId,
                'description' => $description,
            ],
            [
                'date' => now()->toDateString(),
                'cash_type' => 'keuntungan',
                'cash_category_id' => $catBagiHasil->id,
                'type' => 'expense',
                'amount' => $totalProfit * 0.5,
            ]
        );

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

        $currentProfit = $weeklyData->internal_profit ?? 0;
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
            ->where('month_name', 'like', '%' . $monthName . '%')
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
