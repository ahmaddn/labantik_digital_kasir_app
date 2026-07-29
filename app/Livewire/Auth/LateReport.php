<?php

namespace App\Livewire\Auth;

use App\Models\CashierAttendance;
use Livewire\Component;

class LateReport extends Component
{
    public $closingCashInput = '';
    public $closingReportText = '';

    public function mount()
    {
        $activeJurusanId = session('active_jurusan_id');
        $attendance = CashierAttendance::where('user_id', auth()->id())
            ->where('jurusan_id', $activeJurusanId)
            ->where('date', now()->toDateString())
            ->first();

        // If they already clocked out or don't have an attendance record for today, redirect to dashboard
        if (!$attendance || $attendance->clock_out) {
            return redirect()->route('dashboard');
        }
    }

    public function submitReport()
    {
        $this->validate([
            'closingCashInput' => 'required|numeric|min:0',
            'closingReportText' => 'required|string',
        ]);

        $activeJurusanId = session('active_jurusan_id');
        $attendance = CashierAttendance::where('user_id', auth()->id())
            ->where('jurusan_id', $activeJurusanId)
            ->where('date', now()->toDateString())
            ->first();

        if ($attendance) {
            $attendance->update([
                'clock_out' => now(),
                'closing_cash' => (float)$this->closingCashInput,
                'closing_report' => $this->closingReportText,
                'points_at_closing' => (int)(auth()->user()->points + auth()->user()->pending_points),
            ]);
        }

        session()->flash('toast', 'Laporan closing berhasil dikirim dan Anda telah clock-out.');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.late-report')
            ->layout('layouts.blank', ['title' => 'Isi Laporan Closing | Superapps TEFA']);
    }
}
