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

    // Edit report properties
    public $editingAttendanceId = null;
    public $editedClosingReport = '';

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
    }

    public function render()
    {
        $activeRole = session('active_role_name');
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan', 'kasir'])) {
            abort(403, 'Unauthorized.');
        }

        $activeJurusanId = session('active_jurusan_id') ?: $this->selectedJurusanId;

        $attendances = CashierAttendance::with(['user', 'schedule'])
            ->when($activeRole === 'kasir', function($q) {
                // Cashiers can only view their own attendance records
                return $q->where('user_id', auth()->id());
            })
            ->when($activeRole !== 'kasir' && $activeJurusanId, function($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->when($this->search && $activeRole !== 'kasir', function($q) {
                $q->whereHas('user', function($sq) {
                    $sq->where('name', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('date', 'desc')
            ->orderBy('clock_in', 'desc')
            ->paginate(15);

        $allAttendances = CashierAttendance::with('user')
            ->when($activeRole === 'kasir', function($q) {
                return $q->where('user_id', auth()->id());
            })
            ->when($activeRole !== 'kasir' && $activeJurusanId, function($q) use ($activeJurusanId) {
                $q->where('jurusan_id', $activeJurusanId);
            })
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
            'attendances' => $attendances,
            'jurusans' => Jurusan::all(),
            'allAttendancesJson' => json_encode($allAttendances),
        ])->layout('layouts.app', ['title' => 'Laporan Absensi & Shift']);
    }
}
