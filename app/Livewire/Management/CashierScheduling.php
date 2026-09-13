<?php

namespace App\Livewire\Management;

use App\Models\CashierSchedule;
use App\Models\Jurusan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CashierScheduling extends Component
{
    use WithPagination;

    public $currentWeekStart;
    public $schedules = [];
    public $notes = '';
    public $date = '';
    public $selectedUserId = '';
    public $selectedJurusanId = '';
    public $maxCashiersPerDay = 1; // Default 1 person per day
    public $maxShiftsPerWeek = 1; // Default max 1 shift per week
    public $randomizeStartDate = '';
    public $randomizeEndDate = '';

    // Grade quotas state for randomizer
    public $useGradeQuotas = true;
    public $gradeQuotas = ['12' => 1, '11' => 1, '10' => 0];
    public $availableGrades = ['12', '11', '10'];

    // Modal UI states
    public $showCreateModal = false;
    public $showDeleteModal = false;
    public $showDeleteAllModal = false;
    public $showRandomModal = false; // Modal to input randomizer settings
    public $deletingScheduleId = null;

    protected $rules = [
        'date' => 'required|date',
        'selectedUserId' => 'required|exists:users,id',
        'notes' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->currentWeekStart = now()->startOfWeek();
        $this->date = now()->toDateString();
        $this->selectedJurusanId = session('active_jurusan_id') ?? '';
        $this->randomizeStartDate = now()->startOfWeek()->toDateString();
        $this->randomizeEndDate = now()->startOfWeek()->addDays(5)->toDateString();
    }

    public function openRandomModal()
    {
        $start = Carbon::parse($this->currentWeekStart);
        $this->randomizeStartDate = $start->toDateString();
        $this->randomizeEndDate = $start->copy()->addDays(5)->toDateString();

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;

        $dbGrades = User::whereHas('roles', function($q) use ($activeJurusanId) {
            $q->where('roles.name', 'kasir')
              ->when($activeJurusanId, function($sq) use ($activeJurusanId) {
                  $sq->where('role_user.jurusan_id', $activeJurusanId);
              });
        })
        ->whereNotNull('grade_level')
        ->where('grade_level', '!=', '')
        ->pluck('grade_level')
        ->unique()
        ->values()
        ->toArray();

        $mergedGrades = array_unique(array_merge(['12', '11', '10'], $dbGrades));
        rsort($mergedGrades);
        $this->availableGrades = array_values($mergedGrades);

        foreach ($this->availableGrades as $g) {
            if (!isset($this->gradeQuotas[(string)$g])) {
                $this->gradeQuotas[(string)$g] = 0;
            }
        }

        if (array_sum($this->gradeQuotas) === 0) {
            if (in_array('12', $this->availableGrades)) $this->gradeQuotas['12'] = 1;
            if (in_array('11', $this->availableGrades)) $this->gradeQuotas['11'] = 1;
        }

        $this->showRandomModal = true;
    }

    public function previousWeek()
    {
        $this->currentWeekStart = Carbon::parse($this->currentWeekStart)->subWeek();
        $this->resetPage();
    }

    public function nextWeek()
    {
        $this->currentWeekStart = Carbon::parse($this->currentWeekStart)->addWeek();
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function resetForm()
    {
        $this->date = now()->toDateString();
        $this->selectedUserId = '';
        $this->notes = '';
    }

    public function saveSchedule()
    {
        $this->validate();

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;
        if (!$activeJurusanId) {
            $this->dispatch('toast', message: 'Silakan pilih jurusan terlebih dahulu.', type: 'danger');
            return;
        }

        // Validate user belongs to the active jurusan and has cashier role
        $isCashier = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $this->selectedUserId)
            ->where('role_user.jurusan_id', $activeJurusanId)
            ->where('roles.name', 'kasir')
            ->exists();

        if (!$isCashier && session('active_role_name') !== 'superadmin') {
            $this->dispatch('toast', message: 'User tersebut bukan kasir di jurusan ini.', type: 'danger');
            return;
        }

        // Check if user is already scheduled more than twice in the same week
        $targetDate = Carbon::parse($this->date);
        $startOfWeek = $targetDate->copy()->startOfWeek()->toDateString();
        $endOfWeek = $targetDate->copy()->endOfWeek()->toDateString();

        $weeklyCount = CashierSchedule::where('user_id', $this->selectedUserId)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->count();

        if ($weeklyCount >= 2) {
            $this->dispatch('toast', message: 'Kasir tersebut sudah memiliki 2 jadwal di minggu yang sama.', type: 'danger');
            return;
        }

        // Prevent duplicate schedule on same day
        $alreadyScheduled = CashierSchedule::where('user_id', $this->selectedUserId)
            ->where('date', $this->date)
            ->exists();

        if ($alreadyScheduled) {
            $this->dispatch('toast', message: 'Kasir sudah dijadwalkan pada tanggal tersebut.', type: 'danger');
            return;
        }

        $newSchedule = CashierSchedule::create([
            'jurusan_id' => $activeJurusanId,
            'user_id' => $this->selectedUserId,
            'date' => $this->date,
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ]);

        \App\Models\Notification::create([
            'user_id' => $this->selectedUserId,
            'title' => 'Jadwal Jaga Baru',
            'body' => 'Anda dijadwalkan jaga kasir pada tanggal ' . Carbon::parse($this->date)->translatedFormat('d M Y') . ($this->notes ? ' (' . $this->notes . ')' : ''),
            'type' => 'system',
            'action_url' => '/management/schedules'
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        $this->dispatch('toast', message: 'Jadwal berhasil ditambahkan!');
    }

    public function randomizeSchedules()
    {
        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;
        if (!$activeJurusanId) {
            $this->dispatch('toast', message: 'Jurusan tidak terdeteksi.', type: 'danger');
            return;
        }

        $this->validate([
            'maxCashiersPerDay' => 'required|integer|min:1|max:10',
            'maxShiftsPerWeek' => 'required|integer|min:1|max:5',
            'randomizeStartDate' => 'required|date',
            'randomizeEndDate' => 'required|date|after_or_equal:randomizeStartDate',
        ]);

        // Get all cashiers in this jurusan
        $allJurusanCashierIds = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.jurusan_id', $activeJurusanId)
            ->where('roles.name', 'kasir')
            ->pluck('role_user.user_id')
            ->toArray();

        // Filter out users who have more than 1 role (e.g. kasir + admin)
        $cashierUsers = User::whereIn('id', $allJurusanCashierIds)->get()->filter(function($u) {
            return DB::table('role_user')->where('user_id', $u->id)->count() === 1;
        });

        if ($cashierUsers->isEmpty()) {
            $this->dispatch('toast', message: 'Tidak ada kasir murni (tanpa role lain) yang terdaftar di jurusan ini.', type: 'danger');
            return;
        }

        // Calculate custom date range days
        $startDate = Carbon::parse($this->randomizeStartDate);
        $endDate = Carbon::parse($this->randomizeEndDate);
        
        $days = [];
        $tempDate = $startDate->copy();
        while ($tempDate->lte($endDate)) {
            $days[] = $tempDate->toDateString();
            $tempDate->addDay();
        }

        $totalDays = count($days);

        // Process grade quotas if enabled
        $activeGradeQuotas = [];
        if ($this->useGradeQuotas) {
            foreach ($this->gradeQuotas as $g => $q) {
                $qInt = (int)$q;
                if ($qInt > 0) {
                    $activeGradeQuotas[(string)$g] = $qInt;
                }
            }
        }

        if (!empty($activeGradeQuotas)) {
            $this->maxCashiersPerDay = array_sum($activeGradeQuotas);

            // Validate capacity per grade
            foreach ($activeGradeQuotas as $g => $quota) {
                $gradeCashierCount = $cashierUsers->where('grade_level', (string)$g)->count();
                $neededGradeSlots = $totalDays * $quota;
                $maxGradeCapacity = $gradeCashierCount * $this->maxShiftsPerWeek;

                if ($neededGradeSlots > $maxGradeCapacity) {
                    $this->dispatch('toast', message: "Jumlah kasir tingkat {$g} tidak cukup ({$gradeCashierCount} orang) untuk memenuhi kuota {$quota} orang/hari selama {$totalDays} hari (kapasitas max: {$maxGradeCapacity} shift, dibutuhkan: {$neededGradeSlots} shift).", type: 'danger');
                    return;
                }
            }
        } else {
            $neededSlots = $totalDays * $this->maxCashiersPerDay;
            $maxCapacity = $cashierUsers->count() * $this->maxShiftsPerWeek;

            if ($neededSlots > $maxCapacity) {
                $this->dispatch('toast', message: 'Jumlah kasir tidak cukup untuk slot harian dengan batas maksimal shift mingguan saat ini.', type: 'danger');
                return;
            }
        }

        try {
            DB::transaction(function () use ($cashierUsers, $startDate, $endDate, $days, $activeJurusanId, $activeGradeQuotas, $totalDays) {
                // Delete existing schedules for this week and jurusan to prevent clashes
                CashierSchedule::where('jurusan_id', $activeJurusanId)
                    ->whereBetween('date', [
                        $startDate->toDateString(),
                        $endDate->toDateString()
                    ])
                    ->delete();

                // Ambil total akumulasi tugas jaga global masing-masing kasir
                $globalSchedulesCount = DB::table('cashier_schedules')
                    ->where('jurusan_id', $activeJurusanId)
                    ->whereIn('user_id', $cashierUsers->pluck('id'))
                    ->select('user_id', DB::raw('count(*) as total'))
                    ->groupBy('user_id')
                    ->pluck('total', 'user_id')
                    ->toArray();

                $assignedSchedules = [];
                $success = false;

                if (!empty($activeGradeQuotas)) {
                    // Build pool per grade
                    $gradePools = [];
                    foreach ($activeGradeQuotas as $g => $quota) {
                        $cInGrade = $cashierUsers->where('grade_level', (string)$g)->pluck('id')->toArray();
                        
                        usort($cInGrade, function($a, $b) use ($globalSchedulesCount) {
                            $cA = $globalSchedulesCount[$a] ?? 0;
                            $cB = $globalSchedulesCount[$b] ?? 0;
                            if ($cA === $cB) return rand(-1, 1);
                            return $cA <=> $cB;
                        });

                        $n = count($cInGrade);
                        $totalSlotsForGrade = $totalDays * $quota;
                        $baseShifts = (int) floor($totalSlotsForGrade / $n);
                        $extraShifts = $totalSlotsForGrade % $n;

                        $pool = [];
                        foreach ($cInGrade as $idx => $uid) {
                            $target = $baseShifts + ($idx < $extraShifts ? 1 : 0);
                            for ($j = 0; $j < $target; $j++) {
                                $pool[] = $uid;
                            }
                        }
                        $gradePools[(string)$g] = $pool;
                    }

                    for ($attempt = 0; $attempt < 150; $attempt++) {
                        $success = true;
                        $assignedSchedules = [];
                        $tempGradePools = [];
                        foreach ($gradePools as $g => $p) {
                            $temp = $p;
                            shuffle($temp);
                            $tempGradePools[$g] = $temp;
                        }

                        foreach ($days as $day) {
                            $dayAssigned = [];
                            foreach ($activeGradeQuotas as $g => $quota) {
                                for ($slot = 0; $slot < $quota; $slot++) {
                                    $candIdx = null;
                                    foreach ($tempGradePools[$g] as $idx => $uid) {
                                        if (!in_array($uid, $dayAssigned)) {
                                            $candIdx = $idx;
                                            break;
                                        }
                                    }

                                    if ($candIdx === null) {
                                        $success = false;
                                        break 3;
                                    }

                                    $selectedUser = $tempGradePools[$g][$candIdx];
                                    $dayAssigned[] = $selectedUser;
                                    array_splice($tempGradePools[$g], $candIdx, 1);

                                    $assignedSchedules[] = [
                                        'jurusan_id' => $activeJurusanId,
                                        'user_id' => $selectedUser,
                                        'date' => $day,
                                        'notes' => 'Acak Otomatis (Tingkat ' . $g . ')',
                                        'created_by' => auth()->id(),
                                    ];
                                }
                            }
                        }

                        if ($success) break;
                    }
                } else {
                    // Standard randomization pool
                    $cashierIds = $cashierUsers->pluck('id')->toArray();
                    usort($cashierIds, function($a, $b) use ($globalSchedulesCount) {
                        $countA = $globalSchedulesCount[$a] ?? 0;
                        $countB = $globalSchedulesCount[$b] ?? 0;
                        if ($countA === $countB) {
                            return rand(-1, 1);
                        }
                        return $countA <=> $countB;
                    });

                    $neededSlots = $totalDays * $this->maxCashiersPerDay;
                    $n = count($cashierIds);
                    $baseShifts = (int) floor($neededSlots / $n);
                    $extraShiftsCount = $neededSlots % $n;

                    $pool = [];
                    foreach ($cashierIds as $index => $uid) {
                        $targetShifts = $baseShifts + ($index < $extraShiftsCount ? 1 : 0);
                        for ($j = 0; $j < $targetShifts; $j++) {
                            $pool[] = $uid;
                        }
                    }

                    for ($attempt = 0; $attempt < 150; $attempt++) {
                        $success = true;
                        $assignedSchedules = [];
                        $tempPool = $pool;
                        shuffle($tempPool);

                        foreach ($days as $day) {
                            $dayAssigned = [];
                            for ($slot = 0; $slot < $this->maxCashiersPerDay; $slot++) {
                                $candidateIndex = null;
                                foreach ($tempPool as $idx => $uid) {
                                    if (!in_array($uid, $dayAssigned)) {
                                        $candidateIndex = $idx;
                                        break;
                                    }
                                }

                                if ($candidateIndex === null) {
                                    $success = false;
                                    break 2;
                                }

                                $selectedUser = $tempPool[$candidateIndex];
                                $dayAssigned[] = $selectedUser;
                                array_splice($tempPool, $candidateIndex, 1);

                                $assignedSchedules[] = [
                                    'jurusan_id' => $activeJurusanId,
                                    'user_id' => $selectedUser,
                                    'date' => $day,
                                    'notes' => 'Acak Otomatis',
                                    'created_by' => auth()->id(),
                                ];
                            }
                        }

                        if ($success) {
                            break;
                        }
                    }
                }

                if (!$success) {
                    throw new \Exception('Gagal mendistribusikan jadwal secara merata tanpa bentrok hari. Silakan coba lagi.');
                }

                foreach ($assignedSchedules as $sched) {
                    $createdSched = CashierSchedule::create($sched);

                    \App\Models\Notification::create([
                        'user_id' => $createdSched->user_id,
                        'title' => 'Jadwal Jaga Baru (Acak Otomatis)',
                        'body' => 'Anda dijadwalkan jaga kasir pada tanggal ' . Carbon::parse($createdSched->date)->translatedFormat('d M Y'),
                        'type' => 'system',
                        'action_url' => '/management/schedules'
                    ]);
                }
            });
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'danger');
            return;
        }

        $this->showRandomModal = false;
        $this->dispatch('toast', message: 'Jadwal minggu ini berhasil dirandomize secara merata!');
    }

    public function confirmDelete($id)
    {
        $this->deletingScheduleId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteSchedule()
    {
        if ($this->deletingScheduleId) {
            CashierSchedule::findOrFail($this->deletingScheduleId)->delete();
            $this->showDeleteModal = false;
            $this->deletingScheduleId = null;
            $this->dispatch('toast', message: 'Jadwal berhasil dihapus.');
        }
    }

    public function confirmDeleteAll()
    {
        $this->showDeleteAllModal = true;
    }

    public function deleteAllSchedules()
    {
        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;
        if (!$activeJurusanId) {
            $this->dispatch('toast', message: 'Jurusan tidak terdeteksi.', type: 'danger');
            return;
        }

        $startOfWeek = Carbon::parse($this->currentWeekStart);
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        CashierSchedule::where('jurusan_id', $activeJurusanId)
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->delete();

        $this->showDeleteAllModal = false;
        $this->dispatch('toast', message: 'Semua jadwal minggu ini berhasil dihapus.');
    }

    public function exportExcel()
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan'])) {
            abort(403, 'Unauthorized.');
        }

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;
        $start = Carbon::parse($this->currentWeekStart);
        $filename = 'Jadwal_Kasir_' . $start->format('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CashierScheduleExport($this->currentWeekStart, $activeJurusanId),
            $filename
        );
    }

    public function render()
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'kasir'])) {
            abort(403, 'Unauthorized.');
        }

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;

        // Fetch Cashiers
        $cashiers = User::whereHas('roles', function($q) use ($activeJurusanId) {
            $q->where('roles.name', 'kasir')
              ->when($activeJurusanId, function($sq) use ($activeJurusanId) {
                  $sq->where('role_user.jurusan_id', $activeJurusanId);
              });
        })->get();

        // Get start and end of week
        $startOfWeek = Carbon::parse($this->currentWeekStart);
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Query schedules
        $weekSchedules = CashierSchedule::with('user')
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->when($activeJurusanId, function($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
            ->orderBy('date')
            ->get();

        // Hitung total shift per kasir untuk seluruh periode (akumulasi global)
        $allSchedules = CashierSchedule::when($activeJurusanId, function($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
            ->get();

        $cashierStats = $cashiers->map(function($cashier) use ($allSchedules) {
            $shiftCount = $allSchedules->where('user_id', $cashier->id)->count();
            return [
                'id' => $cashier->id,
                'name' => $cashier->name,
                'email' => $cashier->email,
                'grade_level' => $cashier->grade_level,
                'shifts_count' => $shiftCount,
            ];
        })->sortByDesc('shifts_count');

        return view('livewire.management.cashier-scheduling', [
            'cashiers' => $cashiers,
            'weekSchedules' => $weekSchedules,
            'cashierStats' => $cashierStats,
            'jurusans' => Jurusan::all(),
            'weekRange' => $startOfWeek->translatedFormat('d M Y') . ' - ' . $endOfWeek->translatedFormat('d M Y'),
        ])->layout('layouts.app', ['title' => 'Penjadwalan Kasir']);
    }
}
