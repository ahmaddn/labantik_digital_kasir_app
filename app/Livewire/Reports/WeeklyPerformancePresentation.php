<?php

namespace App\Livewire\Reports;

use App\Models\CashierAttendance;
use App\Models\CashierSchedule;
use App\Models\CashierTaskAssignment;
use App\Models\CashierTaskSubmission;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WeeklyPerformancePresentation extends Component
{
    public $startDate;
    public $endDate;
    public $activeSlide = 1;

    public function mount($startDate = null, $endDate = null, $slide = 1)
    {
        $this->startDate = request('startDate') ?? $startDate ?? now()->startOfWeek()->toDateString();
        $this->endDate = request('endDate') ?? $endDate ?? now()->endOfWeek()->toDateString();
        $this->activeSlide = (int) (request('slide') ?? $slide ?? 1);
    }

    public function setSlide($slide)
    {
        if ($slide >= 1 && $slide <= 4) {
            $this->activeSlide = $slide;
        }
    }

    public function nextSlide()
    {
        if ($this->activeSlide < 4) {
            $this->activeSlide++;
        }
    }

    public function prevSlide()
    {
        if ($this->activeSlide > 1) {
            $this->activeSlide--;
        }
    }

    public function render()
    {
        $weekStart = Carbon::parse($this->startDate)->startOfDay();
        $weekEnd = Carbon::parse($this->endDate)->endOfDay();
        $activeJurusanId = session('active_jurusan_id');

        $diffDays = max(1, $weekStart->diffInDays($weekEnd) + 1);
        $prevWeekStart = (clone $weekStart)->subDays($diffDays);
        $prevWeekEnd = (clone $weekStart)->subSecond();

        // 1. Executive Metrics
        $currentAgg = Transaction::forReporting()
            ->selectRaw('COALESCE(SUM(total_price), 0) as total_rev, COALESCE(SUM(unit_profit * quantity), 0) as total_profit, COUNT(DISTINCT reference) as total_tx')
            ->whereBetween('transacted_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->first();

        $totalRevenue = (float) ($currentAgg->total_rev ?? 0);
        $totalProfit = (float) ($currentAgg->total_profit ?? 0);
        $totalTransactions = (int) ($currentAgg->total_tx ?? 0);

        $prevAgg = Transaction::forReporting()
            ->selectRaw('COALESCE(SUM(total_price), 0) as total_rev, COALESCE(SUM(unit_profit * quantity), 0) as total_profit, COUNT(DISTINCT reference) as total_tx')
            ->whereBetween('transacted_at', [$prevWeekStart->format('Y-m-d 00:00:00'), $prevWeekEnd->format('Y-m-d 23:59:59')])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->first();

        $prevRevenue = (float) ($prevAgg->total_rev ?? 0);
        $prevProfit = (float) ($prevAgg->total_profit ?? 0);
        $prevTxCount = (int) ($prevAgg->total_tx ?? 0);

        $revenueGrowth = $prevRevenue > 0 ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : ($totalRevenue > 0 ? 100 : 0);
        $profitGrowth = $prevProfit > 0 ? round((($totalProfit - $prevProfit) / $prevProfit) * 100, 1) : ($totalProfit > 0 ? 100 : 0);
        $txGrowth = $prevTxCount > 0 ? round((($totalTransactions - $prevTxCount) / $prevTxCount) * 100, 1) : ($totalTransactions > 0 ? 100 : 0);

        // 2. Daily Sales
        $dailySales = [];
        $maxDailyRevenue = 1;
        $peakDay = ['day' => '-', 'revenue' => 0, 'transactions' => 0, 'date' => '-'];

        $indonesianDays = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        $currDate = clone $weekStart;
        while ($currDate->lte($weekEnd)) {
            $dayDate = clone $currDate;
            $dayStr = $dayDate->toDateString();
            $dayName = $indonesianDays[$dayDate->dayOfWeek];

            $dayAgg = Transaction::forReporting()
                ->selectRaw('COALESCE(SUM(total_price), 0) as total_rev, COALESCE(SUM(unit_profit * quantity), 0) as total_profit, COUNT(DISTINCT reference) as total_tx')
                ->whereDate('transacted_at', $dayStr)
                ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                ->first();

            $rev = (float) ($dayAgg->total_rev ?? 0);
            $txCount = (int) ($dayAgg->total_tx ?? 0);

            if ($rev > $maxDailyRevenue) {
                $maxDailyRevenue = $rev;
            }

            if ($rev > $peakDay['revenue']) {
                $peakDay = [
                    'day' => $dayName,
                    'date' => $dayDate->format('d M'),
                    'revenue' => $rev,
                    'transactions' => $txCount
                ];
            }

            $dailySales[] = [
                'day_num' => $dayDate->dayOfWeek,
                'day_name' => $dayName,
                'date' => $dayDate->format('d M Y'),
                'revenue' => $rev,
                'transactions' => $txCount,
            ];

            $currDate->addDay();
        }

        // 3. Cashiers Evaluation
        $cashierUsers = User::role('Kasir')
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->get();

        $cashierPerformanceList = $cashierUsers->map(function ($user) use ($weekStart, $weekEnd, $activeJurusanId) {
            $userSales = Transaction::forReporting()
                ->where('user_id', $user->id)
                ->whereBetween('transacted_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
                ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                ->whereIn('status', ['uang_diterima', 'belum_kembalian']);

            $totalSales = $userSales->sum('total_price');
            $totalTx = $userSales->count('reference');

            $schedules = CashierSchedule::where('user_id', $user->id)
                ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                ->get();
            $scheduledCount = $schedules->count();

            $attendances = CashierAttendance::where('user_id', $user->id)
                ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                ->get();
            $attendedCount = $attendances->count();
            $onTimeCount = $attendances->whereIn('clock_in_status', ['on_time', 'early'])->count();
            $lateCount = $attendances->where('clock_in_status', 'late')->count();

            $assignments = CashierTaskAssignment::where('assigned_to', $user->id)
                ->whereBetween('created_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
                ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                ->get();
            $totalAssignedTasks = $assignments->count();

            $submissions = CashierTaskSubmission::where('submitted_by', $user->id)
                ->whereBetween('created_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
                ->get();
            $approvedTasksCount = $submissions->where('approval_status', 'approved')->count();
            $rejectedTasksCount = $submissions->where('approval_status', 'rejected')->count();
            $unsubmittedTasksCount = max(0, $totalAssignedTasks - $submissions->count());

            $posScore = ($totalTx * 5) + (int) floor($totalSales / 10000);
            $attendanceScore = ($onTimeCount * 25) + ($lateCount * 10);
            $taskScore = ($approvedTasksCount * 30) - ($rejectedTasksCount * 10) - ($unsubmittedTasksCount * 15);
            $overallScore = max(0, $posScore + $attendanceScore + $taskScore);

            $evaluationNotes = [];
            if ($unsubmittedTasksCount > 0) $evaluationNotes[] = "$unsubmittedTasksCount tugas belum dilaporkan";
            if ($rejectedTasksCount > 0) $evaluationNotes[] = "$rejectedTasksCount tugas ditolak";
            if ($lateCount > 0) $evaluationNotes[] = "$lateCount kali terlambat piket";
            if ($scheduledCount > $attendedCount) {
                $missed = $scheduledCount - $attendedCount;
                $evaluationNotes[] = "$missed piket tidak hadir";
            }
            if (empty($evaluationNotes)) $evaluationNotes[] = "Kinerja & kepatuhan sangat baik";

            return (object) [
                'user' => $user,
                'total_sales' => $totalSales,
                'total_tx' => $totalTx,
                'overall_score' => $overallScore,
                'evaluation_notes' => implode(', ', $evaluationNotes),
            ];
        })->sortByDesc('overall_score')->values();

        $topPerformers = $cashierPerformanceList->take(3);
        $bottomPerformers = $cashierPerformanceList->filter(fn($c) => $c->overall_score < 60 || $c->total_tx === 0)->sortBy('overall_score')->values()->take(3);

        // Attendance & Tasks stats
        $totalScheduledShifts = CashierSchedule::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))->count();
        $totalAttendedShifts = CashierAttendance::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))->count();
        $shiftFulfillmentRate = $totalScheduledShifts > 0 ? round(($totalAttendedShifts / $totalScheduledShifts) * 100) : 100;

        $totalWeeklyTasksAssigned = CashierTaskAssignment::whereBetween('created_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))->count();
        $totalWeeklyTasksApproved = CashierTaskSubmission::whereBetween('created_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
            ->where('approval_status', 'approved')->count();
        $taskApprovedRate = $totalWeeklyTasksAssigned > 0 ? round(($totalWeeklyTasksApproved / $totalWeeklyTasksAssigned) * 100) : 100;

        // 4. Products Analysis
        $productSalesCurrent = Transaction::forReporting()
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_omset'))
            ->whereBetween('transacted_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->groupBy('product_id')->get()->keyBy('product_id');

        $productSalesPrev = Transaction::forReporting()
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_omset'))
            ->whereBetween('transacted_at', [$prevWeekStart->format('Y-m-d 00:00:00'), $prevWeekEnd->format('Y-m-d 23:59:59')])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->groupBy('product_id')->get()->keyBy('product_id');

        $allProducts = Product::when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('supplier_id')
                  ->orWhere(function($sq) {
                      $sq->whereNotNull('supplier_id')
                        ->whereHas('stockEntries', function($stq) {
                            $stq->where('opening_stock', '>', 0)
                               ->orWhere('closing_stock', '>', 0);
                        });
                  });
            })
            ->with(['category', 'supplier', 'stockEntries' => function($sq) {
                $sq->orderBy('date', 'desc')->limit(1);
            }])
            ->get();

        $productPerformanceCollection = $allProducts->map(function ($product) use ($productSalesCurrent, $productSalesPrev, $totalRevenue) {
            $curr = $productSalesCurrent->get($product->id);
            $prev = $productSalesPrev->get($product->id);

            $qtySold = $curr ? (int)$curr->total_qty : 0;
            $omset = $curr ? (float)$curr->total_omset : 0;
            $contrib = $totalRevenue > 0 ? round(($omset / $totalRevenue) * 100, 1) : 0;

            $prevQty = $prev ? (int)$prev->total_qty : 0;
            $qtyGrowth = $prevQty > 0 ? round((($qtySold - $prevQty) / $prevQty) * 100, 1) : ($qtySold > 0 ? 100 : 0);

            $latestEntry = $product->stockEntries->first();
            $latestStock = $latestEntry ? ($latestEntry->closing_stock ?? $latestEntry->opening_stock ?? 0) : 0;

            return (object) [
                'product' => $product,
                'is_tefa_internal' => is_null($product->supplier_id),
                'qty_sold' => $qtySold,
                'omset' => $omset,
                'contribution_pct' => $contrib,
                'stock' => $latestStock,
                'prev_qty' => $prevQty,
                'qty_growth' => $qtyGrowth,
            ];
        });

        $topSellingProducts = $productPerformanceCollection
            ->filter(fn($p) => $p->qty_sold > 0)
            ->sortByDesc(fn($p) => $p->qty_sold * 10000000 + $p->omset)
            ->values()->take(5);

        $leastSellingProducts = $productPerformanceCollection
            ->sortBy(fn($p) => $p->qty_sold * 10000000 + $p->omset)
            ->values()->take(10);

        return view('livewire.reports.weekly-performance-presentation', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'totalRevenue' => $totalRevenue,
            'totalProfit' => $totalProfit,
            'totalTransactions' => $totalTransactions,
            'revenueGrowth' => $revenueGrowth,
            'profitGrowth' => $profitGrowth,
            'txGrowth' => $txGrowth,
            'prevRevenue' => $prevRevenue,
            'prevProfit' => $prevProfit,
            'prevTxCount' => $prevTxCount,
            'dailySales' => $dailySales,
            'maxDailyRevenue' => $maxDailyRevenue,
            'peakDay' => $peakDay,
            'topPerformers' => $topPerformers,
            'bottomPerformers' => $bottomPerformers,
            'shiftFulfillmentRate' => $shiftFulfillmentRate,
            'taskApprovedRate' => $taskApprovedRate,
            'topSellingProducts' => $topSellingProducts,
            'leastSellingProducts' => $leastSellingProducts,
        ])->layout('layouts.kasir', ['title' => 'Mode Presentasi Evaluasi Mingguan']);
    }
}
