<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\WeeklyProfitShare;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WeeklyProfitView extends Component
{
    use WithPagination;

    public $selectedDate;
    public $viewMode = 'weekly'; 
    public $showDeleteModal = false;
    public $reportToDeleteId = null;
    
    public function mount()
    {
        $this->selectedDate = now()->toDateString();
    }

    public function confirmDelete($id)
    {
        $this->reportToDeleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->reportToDeleteId) {
            WeeklyProfitShare::destroy($this->reportToDeleteId);
            $this->reportToDeleteId = null;
            $this->showDeleteModal = false;
            $this->dispatch('toast', message: 'Laporan berhasil dihapus.');
        }
    }

    public function generateReport()
    {
        // Only allow generation on Friday, Saturday, Sunday
        if (!in_array(now()->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY])) {
            $this->dispatch('toast', message: 'Laporan hanya bisa diproses pada hari Jumat, Sabtu, atau Minggu.', type: 'error');
            return;
        }

        $date = Carbon::parse($this->selectedDate);
        
        // Monday to Friday cycle
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $date->copy()->startOfWeek(Carbon::MONDAY)->addDays(4); // Friday
        
        $weekNumber = $weekEnd->weekOfMonth;
        $monthName = $weekEnd->translatedFormat('F Y');

        // Calculate total profit (Including shop's share from supplier products)
        $totalProfit = Transaction::whereBetween('transacted_at', [
                $weekStart->startOfDay()->toDateTimeString(), 
                $weekEnd->endOfDay()->toDateTimeString()
            ])
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->sum(DB::raw('unit_profit * quantity'));

        if ($totalProfit <= 0) {
            $this->dispatch('toast', message: 'Tidak ada keuntungan pada periode ini.', type: 'error');
            return;
        }

        $data = [
            'month_name' => $monthName,
            'week_number' => $weekNumber,
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'total_profit' => $totalProfit,
            'kas_amount' => $totalProfit * 0.5,
            'shared_amount' => $totalProfit * 0.5,
        ];

        WeeklyProfitShare::updateOrCreate(
            ['week_start' => $weekStart->toDateString(), 'week_end' => $weekEnd->toDateString()],
            $data
        );

        $this->dispatch('toast', message: 'Laporan bagi hasil berhasil dibuat!');
    }

    public function render()
    {
        $reports = WeeklyProfitShare::orderByDesc('week_end')->paginate(10);
        
        $date = Carbon::parse($this->selectedDate);
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(4); // Friday

        $weeklyData = Transaction::whereBetween('transacted_at', [
                $weekStart->startOfDay()->toDateTimeString(), 
                $weekEnd->endOfDay()->toDateTimeString()
            ])
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
                $weekEnd->endOfDay()->toDateTimeString()
            ])
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->groupBy('user_id')
            ->get();

        // Monthly Summary Logic
        $monthlyReports = WeeklyProfitShare::select('month_name', 
                DB::raw('SUM(total_profit) as total_profit'),
                DB::raw('SUM(kas_amount) as total_kas'),
                DB::raw('SUM(shared_amount) as total_shared'),
                DB::raw('COUNT(*) as weeks_count')
            )
            ->groupBy('month_name')
            ->orderByDesc(DB::raw('MAX(week_end)'))
            ->get();

        return view('livewire.weekly-profit-view', [
            'reports' => $reports,
            'monthlyReports' => $monthlyReports,
            'currentWeek' => [
                'start' => $weekStart,
                'end' => $weekEnd,
                'profit' => $currentProfit,
                'total_revenue' => $totalRevenue,
                'supplier_hak' => $supplierHak,
                'adminContributions' => $adminContributions
            ],
            'canProcess' => in_array(now()->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY])
        ])->layout('layouts.app', ['title' => 'Bagi Hasil Mingguan']);
    }
}
