<?php

namespace App\Livewire\Reports;

use App\Models\CashierAttendance;
use App\Models\Jurusan;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceReport extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedJurusanId = '';
    public $dateFrom = '';
    public $dateTo = '';

    // Show closing report modal
    public $showReportModal = false;
    public $activeReportText = '';
    public $activeReportUser = '';

    // Show detail attendance modal
    public $showDetailModal = false;
    public $detailUserId = null;
    public $detailUserName = '';
    public $detailAttendances = [];

    // Edit report properties
    public $editingAttendanceId = null;
    public $editedClosingReport = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
        $this->selectedJurusanId = session('active_jurusan_id') ?? '';
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function showDetails($userId)
    {
        $this->detailUserId = $userId;
        $user = \App\Models\User::findOrFail($userId);
        $this->detailUserName = $user->name;

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;
        $this->detailAttendances = CashierAttendance::where('user_id', $userId)
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->when($activeJurusanId, function($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
            ->orderBy('date', 'desc')
            ->orderBy('clock_in', 'desc')
            ->get();

        $this->showDetailModal = true;
    }

    public function viewReport($id)
    {
        $attendance = CashierAttendance::with('user')->findOrFail($id);
        $this->activeReportText = $attendance->closing_report ?? 'Tidak ada laporan tertulis.';
        $this->activeReportUser = $attendance->user->name;
        $this->showReportModal = true;
    }

    public function openEditReport($id)
    {
        $attendance = CashierAttendance::findOrFail($id);
        
        // Ensure cashier can only edit their own, and only if edit_count < 1
        if ($attendance->user_id !== auth()->id() && session('active_role_name') !== 'superadmin') {
            $this->dispatch('toast', message: 'Anda tidak diizinkan mengedit laporan ini.');
            return;
        }

        if ($attendance->edit_count >= 1 && session('active_role_name') !== 'superadmin') {
            $this->dispatch('toast', message: 'Laporan ini sudah diedit 1 kali dan tidak bisa diubah lagi.');
            return;
        }

        $this->editingAttendanceId = $id;
        $this->editedClosingReport = $attendance->closing_report ?? '';
        $this->showReportModal = false; // Hide preview if open
    }

    public function updateReport()
    {
        $attendance = CashierAttendance::findOrFail($this->editingAttendanceId);
        
        // Final authorization check
        if ($attendance->user_id !== auth()->id() && session('active_role_name') !== 'superadmin') {
            $this->dispatch('toast', message: 'Anda tidak diizinkan mengedit laporan ini.');
            return;
        }

        if ($attendance->edit_count >= 1 && session('active_role_name') !== 'superadmin') {
            $this->dispatch('toast', message: 'Laporan ini sudah pernah diedit.');
            return;
        }

        $attendance->closing_report = $this->editedClosingReport;
        
        // Only increment edit count for cashiers
        if (session('active_role_name') === 'kasir') {
            $attendance->edit_count = $attendance->edit_count + 1;
        }

        $attendance->save();

        $this->editingAttendanceId = null;
        $this->editedClosingReport = '';
        $this->dispatch('toast', message: 'Laporan shift berhasil diperbarui.');

        // Refresh details modal
        if ($this->detailUserId) {
            $this->showDetails($this->detailUserId);
        }
    }

    public function render()
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'kasir'])) {
            abort(403, 'Unauthorized.');
        }

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;

        // Fetch paginated cashier users with aggregate count
        $cashiers = \App\Models\User::query()
            ->whereHas('roles', function($q) {
                $q->where('name', 'kasir');
            })
            ->when($activeRole === 'kasir', function($q) {
                return $q->where('id', auth()->id());
            })
            ->when($this->search, function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->withCount([
                'attendances as total_attendances' => function($q) use ($activeJurusanId, $activeRole) {
                    $q->whereBetween('date', [$this->dateFrom, $this->dateTo])
                        ->when($activeRole !== 'kasir' && $activeJurusanId, function($sq) use ($activeJurusanId) {
                            $sq->where('jurusan_id', $activeJurusanId);
                        });
                },
                'attendances as present_count' => function($q) use ($activeJurusanId, $activeRole) {
                    $q->where('status', 'present')
                        ->whereBetween('date', [$this->dateFrom, $this->dateTo])
                        ->when($activeRole !== 'kasir' && $activeJurusanId, function($sq) use ($activeJurusanId) {
                            $sq->where('jurusan_id', $activeJurusanId);
                        });
                },
                'attendances as late_count' => function($q) use ($activeJurusanId, $activeRole) {
                    $q->where('status', 'late')
                        ->whereBetween('date', [$this->dateFrom, $this->dateTo])
                        ->when($activeRole !== 'kasir' && $activeJurusanId, function($sq) use ($activeJurusanId) {
                            $sq->where('jurusan_id', $activeJurusanId);
                        });
                },
                'attendances as early_checkout_count' => function($q) use ($activeJurusanId, $activeRole) {
                    $q->where('status', 'early_checkout')
                        ->whereBetween('date', [$this->dateFrom, $this->dateTo])
                        ->when($activeRole !== 'kasir' && $activeJurusanId, function($sq) use ($activeJurusanId) {
                            $sq->where('jurusan_id', $activeJurusanId);
                        });
                }
            ])
            ->paginate(15);

        // Keep Calendar Events as fallback
        $allAttendances = CashierAttendance::with('user')
            ->when($activeRole === 'kasir', function($q) {
                return $q->where('user_id', auth()->id());
            })
            ->when($activeRole !== 'kasir' && $activeJurusanId, function($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->get()
            ->map(function($att) {
                $statusText = $att->status === 'present' ? 'Tepat Waktu' : ($att->status === 'late' ? 'Terlambat' : $att->status);
                $timeText = '';
                if ($att->clock_in) {
                    $timeText .= ' ' . $att->clock_in->format('H:i');
                }
                if ($att->clock_out) {
                    $timeText .= '-' . $att->clock_out->format('H:i');
                }
                
                $color = '#6b7280';
                if ($att->status === 'present') {
                    $color = '#10b981';
                } elseif ($att->status === 'late') {
                    $color = '#f43f5e';
                }
                
                return [
                    'id' => $att->id,
                    'title' => $att->user->name . ($timeText ? ' (' . trim($timeText) . ')' : '') . ' - ' . $statusText,
                    'start' => $att->date->toDateString(),
                    'allDay' => true,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                ];
            });

        return view('livewire.reports.attendance-report', [
            'cashiers' => $cashiers,
            'jurusans' => Jurusan::all(),
            'allAttendancesJson' => json_encode($allAttendances),
        ])->layout('layouts.app', ['title' => 'Laporan Absensi & Shift']);
    }
}
