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
    public $selectedUserId = '';
    public $notes = '';

    // Randomize State
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
            ->exists();

        if ($alreadyScheduled) {
            $this->dispatch('toast', message: 'Kasir sudah ditugaskan pada tanggal tersebut.', type: 'danger');
            return;
        }

        try {
            $schedule = DocumentationSchedule::create([
                'activity_id' => $activity->id,
                'jurusan_id' => $activity->jurusan_id,
                'user_id' => $this->selectedUserId,
                'date' => $this->date,
                'notes' => $this->notes ?: 'Tugas Dokumentasi',
                'created_by' => auth()->id(),
            ]);

            \App\Models\Notification::create([
                'user_id' => $this->selectedUserId,
                'title' => 'Tugas Dokumentasi Baru',
                'body' => 'Anda ditugaskan dokumentasi ' . $activity->title . ' pada ' . Carbon::parse($this->date)->translatedFormat('d M Y'),
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
            'maxCashiersPerDay' => 'required|integer|min:1|max:10',
            'maxShiftsPerWeek' => 'required|integer|min:1|max:7',
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

        $startDate = Carbon::parse($this->randomizeStartDate);
        $endDate = Carbon::parse($this->randomizeEndDate);

        $days = [];
        $tempDate = $startDate->copy();
        while ($tempDate->lte($endDate)) {
            $days[] = $tempDate->toDateString();
            $tempDate->addDay();
        }

        $totalDays = count($days);

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

            foreach ($activeGradeQuotas as $g => $quota) {
                $gradeCashierCount = $cashierUsers->where('grade_level', (string)$g)->count();
                $neededGradeSlots = $totalDays * $quota;
                $maxGradeCapacity = $gradeCashierCount * $this->maxShiftsPerWeek;

                if ($neededGradeSlots > $maxGradeCapacity) {
                    $this->dispatch('toast', message: "Jumlah kasir tingkat {$g} tidak cukup ({$gradeCashierCount} orang) untuk kuota {$quota} orang/hari selama {$totalDays} hari.", type: 'danger');
                    return;
                }
            }
        } else {
            $neededSlots = $totalDays * $this->maxCashiersPerDay;
            $maxCapacity = $cashierUsers->count() * $this->maxShiftsPerWeek;

            if ($neededSlots > $maxCapacity) {
                $this->dispatch('toast', message: 'Jumlah kasir tidak cukup untuk memenuhi slot harian kegiatan ini.', type: 'danger');
                return;
            }
        }

        try {
            DB::transaction(function () use ($activity, $cashierUsers, $startDate, $endDate, $days, $activeJurusanId, $activeGradeQuotas, $totalDays) {
                // Delete existing schedules for this activity within date range
                DocumentationSchedule::where('activity_id', $activity->id)
                    ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->delete();

                // Ambil total akumulasi riwayat penugasan dokumentasi global masing-masing kasir
                $globalSchedulesCount = DB::table('documentation_schedules')
                    ->where('jurusan_id', $activeJurusanId)
                    ->whereIn('user_id', $cashierUsers->pluck('id'))
                    ->select('user_id', DB::raw('count(*) as total'))
                    ->groupBy('user_id')
                    ->pluck('total', 'user_id')
                    ->toArray();

                $assignedSchedules = [];
                $success = false;

                if (!empty($activeGradeQuotas)) {
                    $gradePools = [];
                    foreach ($activeGradeQuotas as $g => $quota) {
                        $cInGrade = $cashierUsers->where('grade_level', (string)$g)->pluck('id')->toArray();

                        usort($cInGrade, function ($a, $b) use ($globalSchedulesCount) {
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
                                        'activity_id' => $activity->id,
                                        'jurusan_id' => $activeJurusanId,
                                        'user_id' => $selectedUser,
                                        'date' => $day,
                                        'notes' => 'Acak Dokumentasi (Tingkat ' . $g . ')',
                                        'created_by' => auth()->id(),
                                    ];
                                }
                            }
                        }

                        if ($success) break;
                    }
                } else {
                    $cashierIds = $cashierUsers->pluck('id')->toArray();
                    usort($cashierIds, function ($a, $b) use ($globalSchedulesCount) {
                        $countA = $globalSchedulesCount[$a] ?? 0;
                        $countB = $globalSchedulesCount[$b] ?? 0;
                        if ($countA === $countB) return rand(-1, 1);
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
                                    'activity_id' => $activity->id,
                                    'jurusan_id' => $activeJurusanId,
                                    'user_id' => $selectedUser,
                                    'date' => $day,
                                    'notes' => 'Acak Dokumentasi',
                                    'created_by' => auth()->id(),
                                ];
                            }
                        }

                        if ($success) break;
                    }
                }

                if (!$success) {
                    throw new \Exception('Gagal mendistribusikan penugasan dokumentasi secara merata. Silakan coba lagi.');
                }

                foreach ($assignedSchedules as $sched) {
                    $createdSched = DocumentationSchedule::create($sched);

                    \App\Models\Notification::create([
                        'user_id' => $createdSched->user_id,
                        'title' => 'Tugas Dokumentasi Baru (Acak Otomatis)',
                        'body' => 'Anda ditugaskan dokumentasi ' . $activity->title . ' pada ' . Carbon::parse($createdSched->date)->translatedFormat('d M Y'),
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
        $this->dispatch('toast', message: 'Penugasan dokumentasi berhasil dirandomize secara adil!');
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

    public function render()
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'kasir'])) {
            abort(403, 'Unauthorized.');
        }

        $activeJurusanId = session('active_jurusan_id') ?: ($this->selectedJurusanId ?: null);
        $jurusans = Jurusan::all();

        // Fetch Activities
        $activities = DocumentationActivity::when($activeJurusanId, function ($q) use ($activeJurusanId) {
            $q->where('jurusan_id', $activeJurusanId);
        })
        ->latest('start_date')
        ->get();

        // Selected Activity Details & Schedules
        $activeActivity = null;
        $activitySchedules = collect();
        $daysList = [];

        if ($this->selectedActivityId) {
            $activeActivity = $activities->firstWhere('id', $this->selectedActivityId);
            if ($activeActivity) {
                $activitySchedules = DocumentationSchedule::with('user')
                    ->where('activity_id', $activeActivity->id)
                    ->orderBy('date')
                    ->get();

                // Days list between start and end date
                $tempDate = $activeActivity->start_date->copy();
                while ($tempDate->lte($activeActivity->end_date)) {
                    $daysList[] = $tempDate->copy();
                    $tempDate->addDay();
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

        return view('livewire.management.documentation-scheduling', [
            'activities' => $activities,
            'activeActivity' => $activeActivity,
            'activitySchedules' => $activitySchedules,
            'daysList' => $daysList,
            'cashiers' => $cashiers,
            'cashierStats' => $cashierStats,
            'jurusans' => $jurusans,
        ])->layout('layouts.app', ['title' => 'Jadwal Dokumentasi Labantik']);
    }
}
