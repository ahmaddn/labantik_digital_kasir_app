<?php

namespace App\Livewire\Management;

use App\Models\DocumentationActivity;
use App\Models\DocumentationSchedule;
use App\Models\Jurusan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DocumentationScheduling extends Component
{
    // Activity State
    public $selectedActivityId = '';
    public $selectedJurusanId = '';
    public $activityTitle = '';
    public $activityStartDate = '';
    public $activityEndDate = '';
    public $activityDescription = '';

    // Schedule Form State
    public $date = '';
    public $shift = 1;
    public $selectedUserId = '';
    public $notes = '';

    // Randomize State
    public $shiftsPerDay = 1;
    public $maxCashiersPerDay = 1;
    public $maxShiftsPerWeek = 2;
    public $randomizeStartDate = '';
    public $randomizeEndDate = '';
    public $useGradeQuotas = true;
    public $gradeQuotas = ['12' => 1, '11' => 1, '10' => 0];
    public $availableGrades = ['12', '11', '10'];

    // UI Modals
    public $showActivityModal = false;
    public $showScheduleModal = false;
    public $showRandomModal = false;
    public $showDeleteScheduleModal = false;
    public $showDeleteActivityModal = false;

    public $editingActivityId = null;
    public $deletingScheduleId = null;
    public $deletingActivityId = null;

    public function mount()
    {
        $this->activityStartDate = now()->toDateString();
        $this->activityEndDate = now()->addDays(6)->toDateString();
        $this->date = now()->toDateString();
        $this->selectedJurusanId = session('active_jurusan_id') ?? '';

        $activeJurusanId = session('active_jurusan_id') ?: ($this->selectedJurusanId ?: null);
        $latestActivity = DocumentationActivity::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            $q->where('jurusan_id', $activeJurusanId);
        })->latest('created_at')->first();

        if ($latestActivity) {
            $this->selectedActivityId = $latestActivity->id;
        }
    }

    public function openCreateActivityModal()
    {
        $this->editingActivityId = null;
        $this->activityTitle = '';
        $this->activityStartDate = now()->toDateString();
        $this->activityEndDate = now()->addDays(6)->toDateString();
        $this->activityDescription = '';
        $this->showActivityModal = true;
    }

    public function openEditActivityModal($id)
    {
        $activity = DocumentationActivity::findOrFail($id);
        $this->editingActivityId = $activity->id;
        $this->activityTitle = $activity->title;
        $this->activityStartDate = $activity->start_date->toDateString();
        $this->activityEndDate = $activity->end_date->toDateString();
        $this->activityDescription = $activity->description ?? '';
        $this->showActivityModal = true;
    }

    public function saveActivity()
    {
        $this->validate([
            'activityTitle' => 'required|string|max:255',
            'activityStartDate' => 'required|date',
            'activityEndDate' => 'required|date|after_or_equal:activityStartDate',
            'activityDescription' => 'nullable|string|max:1000',
        ]);

        $activeJurusanId = session('active_jurusan_id') ?: ($this->selectedJurusanId ?: null);

        if (!$activeJurusanId && session('active_role_name') !== 'superadmin') {
            $this->dispatch('toast', message: 'Silakan pilih jurusan terlebih dahulu.', type: 'danger');
            return;
        }

        try {
            if ($this->editingActivityId) {
                $activity = DocumentationActivity::findOrFail($this->editingActivityId);
                $activity->update([
                    'title' => $this->activityTitle,
                    'start_date' => $this->activityStartDate,
                    'end_date' => $this->activityEndDate,
                    'description' => $this->activityDescription,
                ]);
                $this->dispatch('toast', message: 'Kegiatan dokumentasi berhasil diperbarui.');
            } else {
                $activity = DocumentationActivity::create([
                    'jurusan_id' => $activeJurusanId ?: null,
                    'title' => $this->activityTitle,
                    'start_date' => $this->activityStartDate,
                    'end_date' => $this->activityEndDate,
                    'description' => $this->activityDescription,
                    'created_by' => auth()->id(),
                ]);
                $this->selectedActivityId = $activity->id;
                $this->dispatch('toast', message: 'Kegiatan dokumentasi berhasil dibuat.');
            }

            $this->showActivityModal = false;
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Gagal menyimpan kegiatan: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function confirmDeleteActivity($id)
    {
        $this->deletingActivityId = $id;
        $this->showDeleteActivityModal = true;
    }

    public function deleteActivity()
    {
        if ($this->deletingActivityId) {
            DocumentationActivity::findOrFail($this->deletingActivityId)->delete();
            if ($this->selectedActivityId === $this->deletingActivityId) {
                $this->selectedActivityId = '';
            }
            $this->deletingActivityId = null;
            $this->showDeleteActivityModal = false;
            $this->dispatch('toast', message: 'Kegiatan dokumentasi berhasil dihapus.');
        }
    }

    public function openCreateScheduleModal()
    {
        if (!$this->selectedActivityId) {
            $this->dispatch('toast', message: 'Pilih kegiatan dokumentasi terlebih dahulu.', type: 'danger');
            return;
        }
        $this->selectedUserId = '';
        $this->shift = 1;
        $this->notes = '';

        $activity = DocumentationActivity::find($this->selectedActivityId);
        if ($activity) {
            $this->date = $activity->start_date->toDateString();
        } else {
            $this->date = now()->toDateString();
        }

        $this->showScheduleModal = true;
    }

    public function saveSchedule()
    {
        $this->validate([
            'selectedUserId' => 'required|exists:users,id',
            'date' => 'required|date',
            'shift' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:255',
        ]);

        if (!$this->selectedActivityId) {
            $this->dispatch('toast', message: 'Pilih kegiatan dokumentasi terlebih dahulu.', type: 'danger');
            return;
        }

        $activity = DocumentationActivity::findOrFail($this->selectedActivityId);
        $targetDate = Carbon::parse($this->date);

        if ($targetDate->lt($activity->start_date) || $targetDate->gt($activity->end_date)) {
            $this->dispatch('toast', message: 'Tanggal penugasan berada di luar rentang tanggal kegiatan.', type: 'danger');
            return;
        }

        $alreadyScheduled = DocumentationSchedule::where('activity_id', $this->selectedActivityId)
            ->where('user_id', $this->selectedUserId)
            ->where('date', $this->date)
            ->where('shift', $this->shift)
            ->exists();

        if ($alreadyScheduled) {
            $this->dispatch('toast', message: 'Kasir sudah ditugaskan dokumentasi pada tanggal dan shift tersebut.', type: 'danger');
            return;
        }

        $hasCashierShift = DB::table('cashier_schedules')
            ->where('user_id', $this->selectedUserId)
            ->where('date', $this->date)
            ->exists();

        if ($hasCashierShift) {
            $this->dispatch('toast', message: 'Kasir ini sudah memiliki jadwal piket kasir pada tanggal tersebut. Pilihlah kasir/tanggal lain agar tidak bentrok.', type: 'danger');
            return;
        }

        try {
            $schedule = DocumentationSchedule::create([
                'activity_id' => $activity->id,
                'jurusan_id' => $activity->jurusan_id,
                'user_id' => $this->selectedUserId,
                'date' => $this->date,
                'shift' => $this->shift,
                'notes' => $this->notes ?: 'Tugas Dokumentasi (Shift ' . $this->shift . ')',
                'created_by' => auth()->id(),
            ]);

            \App\Models\Notification::create([
                'user_id' => $this->selectedUserId,
                'title' => 'Tugas Dokumentasi Baru',
                'body' => 'Anda ditugaskan dokumentasi ' . $activity->title . ' (Shift ' . $this->shift . ') pada ' . Carbon::parse($this->date)->translatedFormat('d M Y'),
                'type' => 'system',
                'action_url' => '/management/documentation-schedules'
            ]);

            $this->showScheduleModal = false;
            $this->dispatch('toast', message: 'Penugasan dokumentasi berhasil ditambahkan!');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Gagal menambah penugasan: ' . $e->getMessage(), type: 'danger');
        }
    }

    public function openRandomModal()
    {
        if (!$this->selectedActivityId) {
            $this->dispatch('toast', message: 'Pilih kegiatan dokumentasi terlebih dahulu.', type: 'danger');
            return;
        }

        $activity = DocumentationActivity::findOrFail($this->selectedActivityId);
        $this->randomizeStartDate = $activity->start_date->toDateString();
        $this->randomizeEndDate = $activity->end_date->toDateString();
        $this->shiftsPerDay = 1;

        $activeJurusanId = session('active_jurusan_id') ?: $activity->jurusan_id;

        $dbGrades = User::whereHas('roles', function ($q) use ($activeJurusanId) {
            $q->where('roles.name', 'kasir')
                ->when($activeJurusanId, function ($sq) use ($activeJurusanId) {
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

    public function randomizeSchedules()
    {
        if (!$this->selectedActivityId) {
            $this->dispatch('toast', message: 'Kegiatan dokumentasi tidak terdeteksi.', type: 'danger');
            return;
        }

        $activity = DocumentationActivity::findOrFail($this->selectedActivityId);
        $activeJurusanId = session('active_jurusan_id') ?: $activity->jurusan_id;

        $this->validate([
            'shiftsPerDay' => 'required|integer|min:1|max:5',
            'maxCashiersPerDay' => 'required|integer|min:1|max:10',
            'randomizeStartDate' => 'required|date',
            'randomizeEndDate' => 'required|date|after_or_equal:randomizeStartDate',
        ]);

        // Get cashiers
        $allJurusanCashierIds = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.jurusan_id', $activeJurusanId)
            ->where('roles.name', 'kasir')
            ->pluck('role_user.user_id')
            ->toArray();

        $cashierUsers = User::whereIn('id', $allJurusanCashierIds)->get()->filter(function ($u) {
            return DB::table('role_user')->where('user_id', $u->id)->count() === 1;
        });

        if ($cashierUsers->isEmpty()) {
            $this->dispatch('toast', message: 'Tidak ada kasir murni yang terdaftar di jurusan ini.', type: 'danger');
            return;
        }

        $startDate = Carbon::parse($this->randomizeStartDate)->startOfDay();
        $endDate = Carbon::parse($this->randomizeEndDate)->startOfDay();

        $days = [];
        if ($startDate->lte($endDate)) {
            $tempDate = $startDate->copy();
            $limit = 0;
            while ($tempDate->lte($endDate) && $limit < 90) {
                $days[] = $tempDate->toDateString();
                $tempDate->addDay();
                $limit++;
            }
        }

        $activeGradeQuotas = [];
        if ($this->useGradeQuotas) {
            foreach ($this->gradeQuotas as $g => $q) {
                $qInt = (int)$q;
                if ($qInt > 0) {
                    $activeGradeQuotas[(string)$g] = $qInt;
                }
            }
        }

        try {
            DB::transaction(function () use ($activity, $cashierUsers, $startDate, $endDate, $days, $activeJurusanId, $activeGradeQuotas) {
                // Delete existing schedules for this activity within date range
                DocumentationSchedule::where('activity_id', $activity->id)
                    ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->delete();

                // Map existing cashier shifts (cashier_schedules) to prevent clashes
                $cashierShiftMap = DB::table('cashier_schedules')
                    ->whereIn('user_id', $cashierUsers->pluck('id'))
                    ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->get()
                    ->groupBy(function ($item) {
                        return $item->user_id . '_' . $item->date;
                    });

                // Ambil total akumulasi riwayat penugasan dokumentasi global masing-masing kasir
                $globalSchedulesCount = DB::table('documentation_schedules')
                    ->where('jurusan_id', $activeJurusanId)
                    ->whereIn('user_id', $cashierUsers->pluck('id'))
                    ->select('user_id', DB::raw('count(*) as total'))
                    ->groupBy('user_id')
                    ->pluck('total', 'user_id')
                    ->toArray();

                $runCount = [];
                $userShiftCounts = [];
                $assignedSchedules = [];
                $numShifts = max(1, (int)$this->shiftsPerDay);

                foreach ($days as $day) {
                    $dayAssignedUsers = [];
                    // Randomize processing order of shifts per day
                    $shifts = range(1, $numShifts);
                    shuffle($shifts);

                    foreach ($shifts as $s) {
                        if (!empty($activeGradeQuotas)) {
                            foreach ($activeGradeQuotas as $g => $quota) {
                                $gradeCashiers = $cashierUsers->where('grade_level', (string)$g)->pluck('id')->toArray();
                                if (empty($gradeCashiers)) continue;

                                // Filter out cashiers with cashier piket clash on this date
                                $eligible = array_filter($gradeCashiers, function ($uid) use ($cashierShiftMap, $day) {
                                    return !isset($cashierShiftMap[$uid . '_' . $day]);
                                });

                                if (empty($eligible)) continue;

                                // Prefer candidates not assigned yet today
                                $unassignedToday = array_values(array_diff($eligible, $dayAssignedUsers));
                                $candidatePool = !empty($unassignedToday) ? $unassignedToday : array_values($eligible);

                                usort($candidatePool, function ($a, $b) use ($globalSchedulesCount, $runCount, $userShiftCounts, $s) {
                                    $scoreA = (($globalSchedulesCount[$a] ?? 0) * 10) + (($runCount[$a] ?? 0) * 10) + (($userShiftCounts[$a][$s] ?? 0) * 25) + rand(0, 9);
                                    $scoreB = (($globalSchedulesCount[$b] ?? 0) * 10) + (($runCount[$b] ?? 0) * 10) + (($userShiftCounts[$b][$s] ?? 0) * 25) + rand(0, 9);
                                    return $scoreA <=> $scoreB;
                                });

                                $picked = array_slice($candidatePool, 0, $quota);

                                foreach ($picked as $uid) {
                                    $dayAssignedUsers[] = $uid;
                                    $runCount[$uid] = ($runCount[$uid] ?? 0) + 1;
                                    $userShiftCounts[$uid][$s] = ($userShiftCounts[$uid][$s] ?? 0) + 1;

                                    $assignedSchedules[] = [
                                        'activity_id' => $activity->id,
                                        'jurusan_id' => $activeJurusanId,
                                        'user_id' => $uid,
                                        'date' => $day,
                                        'shift' => $s,
                                        'notes' => 'Acak Dokumentasi (Shift ' . $s . ' - Tingkat ' . $g . ')',
                                        'created_by' => auth()->id(),
                                    ];
                                }
                            }
                        } else {
                            $allCashiers = $cashierUsers->pluck('id')->toArray();
                            $eligible = array_filter($allCashiers, function ($uid) use ($cashierShiftMap, $day) {
                                return !isset($cashierShiftMap[$uid . '_' . $day]);
                            });

                            if (!empty($eligible)) {
                                $unassignedToday = array_values(array_diff($eligible, $dayAssignedUsers));
                                $candidatePool = !empty($unassignedToday) ? $unassignedToday : array_values($eligible);

                                usort($candidatePool, function ($a, $b) use ($globalSchedulesCount, $runCount, $userShiftCounts, $s) {
                                    $scoreA = (($globalSchedulesCount[$a] ?? 0) * 10) + (($runCount[$a] ?? 0) * 10) + (($userShiftCounts[$a][$s] ?? 0) * 25) + rand(0, 9);
                                    $scoreB = (($globalSchedulesCount[$b] ?? 0) * 10) + (($runCount[$b] ?? 0) * 10) + (($userShiftCounts[$b][$s] ?? 0) * 25) + rand(0, 9);
                                    return $scoreA <=> $scoreB;
                                });

                                $targetCount = (int)$this->maxCashiersPerDay;
                                $picked = array_slice($candidatePool, 0, $targetCount);

                                foreach ($picked as $uid) {
                                    $dayAssignedUsers[] = $uid;
                                    $runCount[$uid] = ($runCount[$uid] ?? 0) + 1;
                                    $userShiftCounts[$uid][$s] = ($userShiftCounts[$uid][$s] ?? 0) + 1;

                                    $assignedSchedules[] = [
                                        'activity_id' => $activity->id,
                                        'jurusan_id' => $activeJurusanId,
                                        'user_id' => $uid,
                                        'date' => $day,
                                        'shift' => $s,
                                        'notes' => 'Acak Dokumentasi (Shift ' . $s . ')',
                                        'created_by' => auth()->id(),
                                    ];
                                }
                            }
                        }
                    }
                }

                if (empty($assignedSchedules)) {
                    throw new \Exception('Tidak ada kasir yang tersedia untuk dijadwalkan (mungkin semua kasir sedang piket pada tanggal tersebut).');
                }

                foreach ($assignedSchedules as $sched) {
                    $createdSched = DocumentationSchedule::create($sched);

                    \App\Models\Notification::create([
                        'user_id' => $createdSched->user_id,
                        'title' => 'Tugas Dokumentasi Baru (Acak Otomatis)',
                        'body' => 'Anda ditugaskan dokumentasi ' . $activity->title . ' (Shift ' . $createdSched->shift . ') pada ' . Carbon::parse($createdSched->date)->translatedFormat('d M Y'),
                        'type' => 'system',
                        'action_url' => '/management/documentation-schedules'
                    ]);
                }
            });
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'danger');
            return;
        }

        $this->showRandomModal = false;
        $this->dispatch('toast', message: 'Penugasan dokumentasi berhasil dirandomize tanpa bentrok dengan piket kasir!');
    }

    public function confirmDeleteSchedule($id)
    {
        $this->deletingScheduleId = $id;
        $this->showDeleteScheduleModal = true;
    }

    public function deleteSchedule()
    {
        if ($this->deletingScheduleId) {
            DocumentationSchedule::findOrFail($this->deletingScheduleId)->delete();
            $this->showDeleteScheduleModal = false;
            $this->deletingScheduleId = null;
            $this->dispatch('toast', message: 'Penugasan dokumentasi berhasil dihapus.');
        }
    }

    public function exportExcel()
    {
        if (!$this->selectedActivityId) {
            $this->dispatch('toast', message: 'Pilih kegiatan dokumentasi terlebih dahulu.', type: 'danger');
            return;
        }

        $activity = DocumentationActivity::find($this->selectedActivityId);
        if (!$activity) return;

        $slugTitle = \Illuminate\Support\Str::slug($activity->title);
        $fileName = 'Jadwal_Dokumentasi_' . $slugTitle . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DocumentationScheduleExport($this->selectedActivityId),
            $fileName
        );
    }

    public function render()
    {
        $activeRole = session('active_role_name') ?? (auth()->user()?->roles->first()?->name ?? 'kasir');
        $activeJurusanId = session('active_jurusan_id') ?: ($this->selectedJurusanId ?: null);
        $jurusans = Jurusan::all();
        $dbError = null;

        $activities = collect();
        $activeActivity = null;
        $activitySchedules = collect();
        $daysList = [];
        $cashiers = collect();
        $cashierStats = collect();

        try {
            // Fetch Activities
            $activities = DocumentationActivity::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
            ->latest('start_date')
            ->get();

            // Selected Activity Details & Schedules
            if ($this->selectedActivityId) {
                $activeActivity = $activities->firstWhere('id', $this->selectedActivityId);
                if ($activeActivity) {
                    $activitySchedules = DocumentationSchedule::with('user')
                        ->where('activity_id', $activeActivity->id)
                        ->orderBy('date')
                        ->orderBy('shift')
                        ->get();

                    // Days list between start and end date safely
                    if ($activeActivity->start_date && $activeActivity->end_date) {
                        $startDate = Carbon::parse($activeActivity->start_date)->startOfDay();
                        $endDate = Carbon::parse($activeActivity->end_date)->startOfDay();

                        if ($startDate->lte($endDate)) {
                            $curr = $startDate->copy();
                            $limit = 0;
                            while ($curr->lte($endDate) && $limit < 90) {
                                $daysList[] = $curr->copy();
                                $curr->addDay();
                                $limit++;
                            }
                        }
                    }
                }
            }

            // Fetch Cashiers
            $cashiers = User::whereHas('roles', function ($q) use ($activeJurusanId) {
                $q->where('roles.name', 'kasir')
                    ->when($activeJurusanId, function ($sq) use ($activeJurusanId) {
                        $sq->where('role_user.jurusan_id', $activeJurusanId);
                    });
            })->get();

            // Akumulasi riwayat tugas dokumentasi global per kasir
            $allDocSchedules = DocumentationSchedule::when($activeJurusanId, function ($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })->get();

            $cashierStats = $cashiers->map(function ($cashier) use ($allDocSchedules) {
                $count = $allDocSchedules->where('user_id', $cashier->id)->count();
                return [
                    'id' => $cashier->id,
                    'name' => $cashier->name,
                    'email' => $cashier->email,
                    'grade_level' => $cashier->grade_level,
                    'doc_count' => $count,
                ];
            })->sortByDesc('doc_count');
            // Fetch logged-in user's documentation schedules
            $mySchedules = DocumentationSchedule::with('activity')
                ->where('user_id', auth()->id())
                ->orderBy('date', 'desc')
                ->get();
        } catch (\Exception $e) {
            $dbError = 'Database Error / Tabel belum dimigrasi di server production: ' . $e->getMessage();
        }

        return view('livewire.management.documentation-scheduling', [
            'activities' => $activities,
            'activeActivity' => $activeActivity,
            'activitySchedules' => $activitySchedules,
            'daysList' => $daysList,
            'cashiers' => $cashiers,
            'cashierStats' => $cashierStats,
            'mySchedules' => $mySchedules ?? collect(),
            'jurusans' => $jurusans,
            'dbError' => $dbError,
        ])->layout('layouts.app', ['title' => 'Jadwal Dokumentasi Labantik']);
    }
}
