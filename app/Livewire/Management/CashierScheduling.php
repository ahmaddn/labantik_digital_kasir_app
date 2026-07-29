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

    // Modal UI states
    public $showCreateModal = false;
    public $showDeleteModal = false;
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

        CashierSchedule::create([
            'jurusan_id' => $activeJurusanId,
            'user_id' => $this->selectedUserId,
            'date' => $this->date,
            'notes' => $this->notes,
            'created_by' => auth()->id(),
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
        ]);

        // Get all cashiers in this jurusan
        $allJurusanCashierIds = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.jurusan_id', $activeJurusanId)
            ->where('roles.name', 'kasir')
            ->pluck('role_user.user_id')
            ->toArray();

        // Filter out users who have more than 1 role (e.g. kasir + admin)
        $cashierIds = [];
        foreach ($allJurusanCashierIds as $uid) {
            $rolesCount = DB::table('role_user')
                ->where('user_id', $uid)
                ->count();
            
            if ($rolesCount === 1) {
                $cashierIds[] = $uid;
            }
        }

        if (empty($cashierIds)) {
            $this->dispatch('toast', message: 'Tidak ada kasir murni (tanpa role lain) yang terdaftar di jurusan ini.', type: 'danger');
            return;
        }

        // Limit: total slots available vs total cashiers max capacity (cashiers count * 2)
        $totalDays = 6;
        $neededSlots = $totalDays * $this->maxCashiersPerDay;
        $maxCapacity = count($cashierIds) * 2;

        if ($neededSlots > $maxCapacity) {
            $this->dispatch('toast', message: 'Jumlah kasir tidak cukup untuk slot harian yang ditentukan (Batas maksimal 2x jaga per kasir).', type: 'danger');
            return;
        }

        $startOfWeek = Carbon::parse($this->currentWeekStart);
        
        DB::transaction(function () use ($cashierIds, $startOfWeek, $activeJurusanId) {
            // Delete existing schedules for this week and jurusan to prevent clashes
            CashierSchedule::where('jurusan_id', $activeJurusanId)
                ->whereBetween('date', [
                    $startOfWeek->copy()->toDateString(),
                    $startOfWeek->copy()->endOfWeek()->toDateString()
                ])
                ->delete();

            // Distribute cashiers randomly to 6 days (Mon-Sat)
            $days = [];
            for ($i = 0; $i < 6; $i++) {
                $days[] = $startOfWeek->copy()->addDays($i)->toDateString();
            }

            $userStats = array_fill_keys($cashierIds, 0);

            foreach ($days as $day) {
                // Keep track of cashiers assigned today to prevent assigning same cashier twice on the same day
                $assignedToday = [];

                for ($slot = 0; $slot < $this->maxCashiersPerDay; $slot++) {
                    // Find candidates who have been scheduled < 2 times and not scheduled today
                    $candidates = array_keys(array_filter($userStats, function($val, $uid) use ($assignedToday) {
                        return $val < 2 && !in_array($uid, $assignedToday);
                    }, ARRAY_FILTER_USE_BOTH));

                    if (empty($candidates)) {
                        break;
                    }

                    $randomUser = $candidates[array_rand($candidates)];
                    
                    CashierSchedule::create([
                        'jurusan_id' => $activeJurusanId,
                        'user_id' => $randomUser,
                        'date' => $day,
                        'notes' => 'Acak Otomatis',
                        'created_by' => auth()->id(),
                    ]);

                    $userStats[$randomUser]++;
                    $assignedToday[] = $randomUser;
                }
            }
        });

        $this->showRandomModal = false;
        $this->dispatch('toast', message: 'Jadwal minggu ini berhasil dirandomize!');
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

        // Query all schedules for Calendar JS view
        $allSchedules = CashierSchedule::with('user')
            ->when($activeJurusanId, function($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
            ->orderBy('date')
            ->get()
            ->map(function($s) {
                return [
                    'id' => $s->id,
                    'title' => $s->user->name . ($s->notes ? ' (' . $s->notes . ')' : ''),
                    'start' => $s->date->toDateString(),
                    'allDay' => true,
                ];
            });

        return view('livewire.management.cashier-scheduling', [
            'cashiers' => $cashiers,
            'weekSchedules' => $weekSchedules,
            'jurusans' => Jurusan::all(),
            'weekRange' => $startOfWeek->translatedFormat('d M Y') . ' - ' . $endOfWeek->translatedFormat('d M Y'),
            'allSchedulesJson' => json_encode($allSchedules),
        ])->layout('layouts.app', ['title' => 'Penjadwalan Kasir']);
    }
}
