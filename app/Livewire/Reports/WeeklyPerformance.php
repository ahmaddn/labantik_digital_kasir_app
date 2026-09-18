<?php

namespace App\Livewire\Reports;

use App\Models\CashierAttendance;
use App\Models\CashierNote;
use App\Models\CashierSchedule;
use App\Models\CashierTaskAssignment;
use App\Models\CashierTaskSubmission;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WeeklyPerformance extends Component
{
    public $startDate;
    public $endDate;
    public $currentStep = 1; // 1: Ringkasan & Tren, 2: Kinerja Kasir & Disiplin, 3: Analisis Produk & Stok, 4: Audit Harian & Catatan

    // Modal state for viewing detailed cashier audit
    public $selectedCashierId = null;
    public $showCashierDetailModal = false;

    public function mount($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?? now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->endDate = $endDate ?? now()->startOfWeek(Carbon::MONDAY)->addDays(4)->toDateString();
    }

    public function setPresetRange($preset)
    {
        if ($preset === 'this_week') {
            $this->startDate = now()->startOfWeek(Carbon::MONDAY)->toDateString();
            $this->endDate = now()->startOfWeek(Carbon::MONDAY)->addDays(4)->toDateString();
        } elseif ($preset === 'last_week') {
            $this->startDate = now()->subWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
            $this->endDate = now()->subWeek()->startOfWeek(Carbon::MONDAY)->addDays(4)->toDateString();
        } elseif ($preset === 'this_month') {
            $this->startDate = now()->startOfMonth()->toDateString();
            $this->endDate = now()->endOfMonth()->toDateString();
        }
    }

    public function setStep($step)
    {
        if ($step >= 1 && $step <= 4) {
            $this->currentStep = $step;
        }
    }

    public function nextStep()
    {
        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function viewCashierDetail($userId)
    {
        $this->selectedCashierId = $userId;
        $this->showCashierDetailModal = true;
    }

    public function closeCashierDetailModal()
    {
        $this->showCashierDetailModal = false;
        $this->selectedCashierId = null;
    }

    public function render()
    {
        $activeJurusanId = session('active_jurusan_id');

        $weekStart = Carbon::parse($this->startDate)->startOfDay();
        $weekEnd = Carbon::parse($this->endDate)->endOfDay();

        // Fallback if start is after end
        if ($weekStart->gt($weekEnd)) {
            $temp = clone $weekStart;
            $weekStart = clone $weekEnd;
            $weekEnd = $temp;
        }

        $diffDays = max(1, $weekStart->diffInDays($weekEnd) + 1);
        $prevWeekStart = (clone $weekStart)->subDays($diffDays);
        $prevWeekEnd = (clone $weekStart)->subSecond();

        // --- 1. OVERALL PERIOD SALES & REVENUE AGGREGATE (OPTIMIZED SINGLE QUERY) ---
        $currentAgg = Transaction::forReporting()
            ->selectRaw('COALESCE(SUM(total_price), 0) as total_rev, COALESCE(SUM(unit_profit * quantity), 0) as total_profit, COUNT(DISTINCT reference) as total_tx, COALESCE(SUM(quantity), 0) as total_items')
            ->whereBetween('transacted_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->first();

        $totalRevenue = (float) ($currentAgg->total_rev ?? 0);
        $totalProfit = (float) ($currentAgg->total_profit ?? 0);
        $totalTransactions = (int) ($currentAgg->total_tx ?? 0);
        $totalItemsSold = (int) ($currentAgg->total_items ?? 0);
        $avgBasketSize = $totalTransactions > 0 ? round($totalRevenue / $totalTransactions) : 0;

        // Previous Period Metrics for Comparison (OPTIMIZED SINGLE QUERY)
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

        // --- 2. DAILY BREAKDOWN & DAILY SHIFT AUDIT ---
        $dailySales = [];
        $dailyShiftAudits = [];
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

            // Sales on this day (Aggregated)
            $dayAgg = Transaction::forReporting()
                ->selectRaw('COALESCE(SUM(total_price), 0) as total_rev, COALESCE(SUM(unit_profit * quantity), 0) as total_profit, COUNT(DISTINCT reference) as total_tx')
                ->whereDate('transacted_at', $dayStr)
                ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                ->first();

            $rev = (float) ($dayAgg->total_rev ?? 0);
            $txCount = (int) ($dayAgg->total_tx ?? 0);
            $profit = (float) ($dayAgg->total_profit ?? 0);

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
                'profit' => $profit,
                'transactions' => $txCount,
            ];

            // Factual Shift & Task Audit on this specific day
            $schedulesOnDay = CashierSchedule::where('date', $dayStr)
                ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                ->with('user')
                ->get();

            $cashierAuditsForDay = [];
            foreach ($schedulesOnDay as $sched) {
                $cUser = $sched->user;
                if (!$cUser) continue;

                // Attendance on this day
                $att = CashierAttendance::where('user_id', $cUser->id)
                    ->where('date', $dayStr)
                    ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                    ->first();

                // Tasks assigned on this day (both routine and custom tasks created on/for this date)
                $assignmentsOnDay = CashierTaskAssignment::where('assigned_to', $cUser->id)
                    ->where(function($q) use ($dayStr) {
                        $q->whereDate('created_at', $dayStr)
                          ->orWhereHas('taskDefinition', fn($sq) => $sq->whereDate('date', $dayStr));
                    })
                    ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                    ->with(['taskDefinition', 'latestSubmission'])
                    ->get();

                // Cashier sales on this day
                $cSalesOnDay = Transaction::forReporting()
                    ->where('user_id', $cUser->id)
                    ->whereDate('transacted_at', $dayStr)
                    ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                    ->whereIn('status', ['uang_diterima', 'belum_kembalian']);

                $cRev = $cSalesOnDay->sum('total_price');
                $cTx = $cSalesOnDay->count('reference');

                $taskDetails = $assignmentsOnDay->map(function($asg) {
                    $def = $asg->taskDefinition;
                    $sub = $asg->latestSubmission;
                    $status = 'Belum Dikerjakan';
                    $badgeClass = 'bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:border-rose-800';

                    if ($sub) {
                        if ($sub->approval_status === 'approved') {
                            $status = 'Disetujui';
                            $badgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-800';
                        } elseif ($sub->approval_status === 'rejected') {
                            $status = 'Ditolak: ' . ($sub->rejection_note ?? 'Perlu perbaikan');
                            $badgeClass = 'bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:border-rose-800';
                        } else {
                            $status = 'Menunggu Review';
                            $badgeClass = 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800';
                        }
                    }

                    $rawTaskName = $def->task_name ?? 'Tugas Piket';
                    $hasRoutinePrefix = str_starts_with(strtolower($rawTaskName), '[rutin]');
                    $prefix = ($def && $def->is_routine && !$hasRoutinePrefix) ? '[Rutin] ' : '';

                    return [
                        'task_name' => $prefix . $rawTaskName,
                        'is_routine' => $def->is_routine ?? false,
                        'priority' => $def->priority ?? 'medium',
                        'status' => $status,
                        'badge_class' => $badgeClass,
                        'rejection_note' => $sub->rejection_note ?? null,
                    ];
                });

                $uncompletedTaskCount = $assignmentsOnDay->filter(function($asg) {
                    return !$asg->latestSubmission || $asg->latestSubmission->approval_status === 'rejected';
                })->count();

                $cashierAuditsForDay[] = [
                    'user' => $cUser,
                    'attended' => $att ? true : false,
                    'clock_in' => $att && $att->clock_in ? Carbon::parse($att->clock_in)->format('H:i') : null,
                    'clock_in_status' => $att->clock_in_status ?? 'absen_kosong',
                    'sales_omset' => $cRev,
                    'sales_tx' => $cTx,
                    'assigned_task_count' => $assignmentsOnDay->count(),
                    'uncompleted_task_count' => $uncompletedTaskCount,
                    'task_details' => $taskDetails,
                ];
            }

            if (!empty($cashierAuditsForDay)) {
                $totalCashiersScheduled = count($cashierAuditsForDay);
                $totalCashiersAttended = collect($cashierAuditsForDay)->where('attended', true)->count();
                $totalAssignedTasksDay = collect($cashierAuditsForDay)->sum('assigned_task_count');
                $totalUncompletedTasksDay = collect($cashierAuditsForDay)->sum('uncompleted_task_count');
                $totalApprovedTasksDay = max(0, $totalAssignedTasksDay - $totalUncompletedTasksDay);
                $taskRateDay = $totalAssignedTasksDay > 0 ? round(($totalApprovedTasksDay / $totalAssignedTasksDay) * 100) : 100;

                $dailyShiftAudits[] = [
                    'day_name' => $dayName,
                    'date' => $dayDate->format('d M Y'),
                    'day_revenue' => $rev,
                    'day_profit' => $profit,
                    'day_tx' => $txCount,
                    'scheduled_count' => $totalCashiersScheduled,
                    'attended_count' => $totalCashiersAttended,
                    'task_approved_count' => $totalApprovedTasksDay,
                    'task_assigned_count' => $totalAssignedTasksDay,
                    'task_rate' => $taskRateDay,
                    'cashiers' => $cashierAuditsForDay,
                ];
            }

            $currDate->addDay();
        }

        // --- 3. CASHIER SHIFT & PERFORMANCE AUDIT ---
        // Filter: Hanya kasir yang memiliki jadwal piket (atau absensi/transaksi) pada periode ini
        $scheduledUserIds = CashierSchedule::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->pluck('user_id')
            ->unique();

        $attendedUserIds = CashierAttendance::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->pluck('user_id')
            ->unique();

        $activeCashierIds = $scheduledUserIds->merge($attendedUserIds)->unique()->filter()->values();

        $cashierUsers = User::whereIn('id', $activeCashierIds)
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('roles.name', ['superadmin', 'admin', 'pengelola_jurusan', 'pengelola']);
            })
            ->when($activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->whereHas('roles', function ($sq) use ($activeJurusanId) {
                    $sq->where('role_user.jurusan_id', $activeJurusanId);
                });
            })
            ->get();

        $cashierPerformanceList = $cashierUsers->map(function ($user) use ($weekStart, $weekEnd, $activeJurusanId) {
            // POS Sales
            $userSales = Transaction::forReporting()
                ->where('user_id', $user->id)
                ->whereBetween('transacted_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
                ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
                ->whereIn('status', ['uang_diterima', 'belum_kembalian']);

            $totalSales = $userSales->sum('total_price');
            $totalTx = $userSales->count('reference');
            $totalProfitGen = $userSales->sum(DB::raw('unit_profit * quantity'));

            // Schedules & Attendance
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

            // Task Assignments & Submissions
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
            $pendingTasksCount = $submissions->where('approval_status', 'pending')->count();
            $unsubmittedTasksCount = max(0, $totalAssignedTasks - $submissions->count());

            // Comprehensive Score Index Calculation
            $posScore = ($totalTx * 5) + (int) floor($totalSales / 10000);
            $attendanceScore = ($onTimeCount * 25) + ($lateCount * 10);
            $taskScore = ($approvedTasksCount * 30) - ($rejectedTasksCount * 10) - ($unsubmittedTasksCount * 15);

            $overallScore = max(0, $posScore + $attendanceScore + $taskScore);

            // Determine status label & theme badge
            $statusBadge = 'Perlu Evaluasi';
            $badgeColor = 'danger'; // danger, warning, success, gold
            $evaluationNotes = [];

            if ($unsubmittedTasksCount > 0) {
                $evaluationNotes[] = "$unsubmittedTasksCount tugas belum dilaporkan";
            }
            if ($rejectedTasksCount > 0) {
                $evaluationNotes[] = "$rejectedTasksCount tugas ditolak";
            }
            if ($lateCount > 0) {
                $evaluationNotes[] = "$lateCount kali terlambat piket";
            }
            if ($scheduledCount > $attendedCount) {
                $missed = $scheduledCount - $attendedCount;
                $evaluationNotes[] = "$missed piket tidak hadir";
            }
            if ($totalTx === 0 && $scheduledCount > 0) {
                $evaluationNotes[] = "0 transaksi saat piket";
            }

            if (empty($evaluationNotes)) {
                $evaluationNotes[] = "Kinerja & kepatuhan sempurna";
            }

            if ($overallScore >= 150 || ($totalTx >= 15 && $approvedTasksCount >= 2 && empty($evaluationNotes[0] ?? ''))) {
                $statusBadge = 'Sangat Baik / Bintang';
                $badgeColor = 'gold';
            } elseif ($overallScore >= 80 || $totalTx >= 8) {
                $statusBadge = 'Baik & Produktif';
                $badgeColor = 'success';
            } elseif ($overallScore >= 40 || $attendedCount > 0) {
                $statusBadge = 'Cukup / Standar';
                $badgeColor = 'warning';
            }

            $avgBasket = $totalTx > 0 ? round($totalSales / $totalTx) : 0;
            $taskCompletionRate = $totalAssignedTasks > 0 ? round(($approvedTasksCount / $totalAssignedTasks) * 100) : 100;
            $onTimeRate = $attendedCount > 0 ? round(($onTimeCount / $attendedCount) * 100) : 100;

            return (object) [
                'user' => $user,
                'total_sales' => $totalSales,
                'total_tx' => $totalTx,
                'total_profit' => $totalProfitGen,
                'avg_basket' => $avgBasket,
                'scheduled_count' => $scheduledCount,
                'attended_count' => $attendedCount,
                'on_time_count' => $onTimeCount,
                'late_count' => $lateCount,
                'on_time_rate' => $onTimeRate,
                'assigned_tasks' => $totalAssignedTasks,
                'approved_tasks' => $approvedTasksCount,
                'pending_tasks' => $pendingTasksCount,
                'rejected_tasks' => $rejectedTasksCount,
                'unsubmitted_tasks' => $unsubmittedTasksCount,
                'task_completion_rate' => $taskCompletionRate,
                'overall_score' => $overallScore,
                'status_badge' => $statusBadge,
                'badge_color' => $badgeColor,
                'evaluation_notes' => implode(', ', $evaluationNotes),
            ];
        })->sortByDesc('overall_score')->values();

        // Top & Bottom performers
        $topPerformers = $cashierPerformanceList->take(3);
        $bottomPerformers = $cashierPerformanceList->filter(fn($c) => $c->overall_score < 60 || $c->rejected_tasks > 0 || $c->unsubmitted_tasks > 0 || $c->total_tx === 0)->sortBy('overall_score')->values()->take(3);

        // Task & Attendance Totals for Summary Card
        $totalScheduledShifts = CashierSchedule::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->count();
        $totalAttendedShifts = CashierAttendance::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->count();
        $shiftFulfillmentRate = $totalScheduledShifts > 0 ? round(($totalAttendedShifts / $totalScheduledShifts) * 100) : 100;

        $totalWeeklyTasksAssigned = CashierTaskAssignment::whereBetween('created_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->count();

        $totalWeeklyTasksApproved = CashierTaskSubmission::whereBetween('created_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
            ->where('approval_status', 'approved')
            ->count();
        $taskApprovedRate = $totalWeeklyTasksAssigned > 0 ? round(($totalWeeklyTasksApproved / $totalWeeklyTasksAssigned) * 100) : 100;

        // --- 4. TOP SELLING VS LEAST SELLING PRODUCTS (TEFA & REGULAR STOCK FILTERED) ---
        $productSalesCurrent = Transaction::forReporting()
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_omset'), DB::raw('SUM(unit_profit * quantity) as total_profit'))
            ->whereBetween('transacted_at', [$weekStart->format('Y-m-d 00:00:00'), $weekEnd->format('Y-m-d 23:59:59')])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $productSalesPrev = Transaction::forReporting()
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_omset'))
            ->whereBetween('transacted_at', [$prevWeekStart->format('Y-m-d 00:00:00'), $prevWeekEnd->format('Y-m-d 23:59:59')])
            ->when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $periodStartDate = $weekStart->toDateString();
        $periodEndDate = $weekEnd->toDateString();

        $allProducts = Product::when($activeJurusanId, fn($q) => $q->where('jurusan_id', $activeJurusanId))
            ->where('is_active', true)
            ->whereHas('stockEntries', function($stq) use ($periodStartDate, $periodEndDate) {
                $stq->whereBetween('date', [$periodStartDate, $periodEndDate]);
            })
            ->with(['category', 'supplier', 'stockEntries' => function($sq) use ($periodStartDate, $periodEndDate) {
                $sq->whereBetween('date', [$periodStartDate, $periodEndDate])
                   ->orderBy('date', 'desc');
            }])
            ->get();

        $productPerformanceCollection = $allProducts->map(function ($product) use ($productSalesCurrent, $productSalesPrev, $totalRevenue) {
            $curr = $productSalesCurrent->get($product->id);
            $prev = $productSalesPrev->get($product->id);

            $qtySold = $curr ? (int)$curr->total_qty : 0;
            $omset = $curr ? (float)$curr->total_omset : 0;
            $profit = $curr ? (float)$curr->total_profit : 0;
            $contrib = $totalRevenue > 0 ? round(($omset / $totalRevenue) * 100, 1) : 0;

            $prevQty = $prev ? (int)$prev->total_qty : 0;
            $prevOmset = $prev ? (float)$prev->total_omset : 0;
            $qtyDiff = $qtySold - $prevQty;
            $qtyGrowth = $prevQty > 0 ? round((($qtySold - $prevQty) / $prevQty) * 100, 1) : ($qtySold > 0 ? 100 : 0);

            $isTefaInternal = is_null($product->supplier_id);

            $latestEntry = $product->stockEntries->first();
            $latestStock = $latestEntry ? ($latestEntry->closing_stock ?? $latestEntry->opening_stock ?? 0) : 0;

            return (object) [
                'product' => $product,
                'is_tefa_internal' => $isTefaInternal,
                'qty_sold' => $qtySold,
                'omset' => $omset,
                'profit' => $profit,
                'contribution_pct' => $contrib,
                'stock' => $latestStock,
                'prev_qty' => $prevQty,
                'prev_omset' => $prevOmset,
                'qty_diff' => $qtyDiff,
                'qty_growth' => $qtyGrowth,
            ];
        });

        // Top 5 Most Sold Products
        $topSellingProducts = $productPerformanceCollection
            ->filter(fn($p) => $p->qty_sold > 0)
            ->sortByDesc(fn($p) => $p->qty_sold * 10000000 + $p->omset)
            ->values()
            ->take(5);

        // Top 10 Least Sold / Stagnant Products (Slow-moving)
        $leastSellingProducts = $productPerformanceCollection
            ->sortBy(fn($p) => $p->qty_sold * 10000000 + $p->omset)
            ->values()
            ->take(10);

        // --- 5. CASHIER DETAIL MODAL DATA ---
        $modalCashierData = null;
        if ($this->selectedCashierId) {
            $cUser = User::find($this->selectedCashierId);
            if ($cUser) {
                $cashierDailyBreakdown = [];
                $mCurrDate = clone $weekStart;
                while ($mCurrDate->lte($weekEnd)) {
                    $dDate = clone $mCurrDate;
                    $dStr = $dDate->toDateString();
                    $dDayName = $indonesianDays[$dDate->dayOfWeek];

                    $sched = CashierSchedule::where('user_id', $cUser->id)->where('date', $dStr)->first();
                    $att = CashierAttendance::where('user_id', $cUser->id)->where('date', $dStr)->first();
                    $asgs = CashierTaskAssignment::where('assigned_to', $cUser->id)
                        ->where(function($q) use ($dStr) {
                            $q->whereDate('created_at', $dStr)
                              ->orWhereHas('taskDefinition', fn($sq) => $sq->whereDate('date', $dStr));
                        })
                        ->with(['taskDefinition', 'latestSubmission'])
                        ->get();

                    $txs = Transaction::forReporting()
                        ->where('user_id', $cUser->id)
                        ->whereDate('transacted_at', $dStr)
                        ->whereIn('status', ['uang_diterima', 'belum_kembalian']);

                    $cNotes = CashierNote::where('user_id', $cUser->id)
                        ->whereDate('date', $dStr)
                        ->get();

                    $cashierDailyBreakdown[] = [
                        'day_name' => $dDayName,
                        'date' => $dDate->format('d M Y'),
                        'is_scheduled' => $sched ? true : false,
                        'attendance' => $att,
                        'tasks' => $asgs,
                        'sales_omset' => $txs->sum('total_price'),
                        'sales_count' => $txs->count('reference'),
                        'notes' => $cNotes,
                    ];

                    $mCurrDate->addDay();
                }

                $modalCashierData = [
                    'user' => $cUser,
                    'daily_breakdown' => $cashierDailyBreakdown,
                ];
            }
        }

        return view('livewire.reports.weekly-performance', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'totalRevenue' => $totalRevenue,
            'totalProfit' => $totalProfit,
            'totalTransactions' => $totalTransactions,
            'totalItemsSold' => $totalItemsSold,
            'avgBasketSize' => $avgBasketSize,
            'revenueGrowth' => $revenueGrowth,
            'profitGrowth' => $profitGrowth,
            'txGrowth' => $txGrowth,
            'prevRevenue' => $prevRevenue,
            'prevProfit' => $prevProfit,
            'prevTxCount' => $prevTxCount,
            'dailySales' => $dailySales,
            'dailyShiftAudits' => $dailyShiftAudits,
            'maxDailyRevenue' => $maxDailyRevenue,
            'peakDay' => $peakDay,
            'cashierPerformanceList' => $cashierPerformanceList,
            'topPerformers' => $topPerformers,
            'bottomPerformers' => $bottomPerformers,
            'shiftFulfillmentRate' => $shiftFulfillmentRate,
            'taskApprovedRate' => $taskApprovedRate,
            'totalScheduledShifts' => $totalScheduledShifts,
            'totalAttendedShifts' => $totalAttendedShifts,
            'topSellingProducts' => $topSellingProducts,
            'leastSellingProducts' => $leastSellingProducts,
            'modalCashierData' => $modalCashierData,
        ])->layout('layouts.app', ['title' => 'Performa Penjualan & Kasir Mingguan']);
    }
}
