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

        $currentTime = now();
        $isLateClockOut = false;
        $deductedClockOut = 0;

        $jurusan = \App\Models\Jurusan::find($activeJurusanId);
        $settings = $jurusan ? ($jurusan->theme_settings ?: []) : [];
        $targetClockOut = $settings['clock_out_time'] ?? '15:00';
        $penaltyClockOut = (int) ($settings['late_clock_out_penalty'] ?? 0);

        if ($targetClockOut) {
            try {
                $targetTime = \Carbon\Carbon::createFromFormat('H:i', $targetClockOut);
                $targetTime->setDate($currentTime->year, $currentTime->month, $currentTime->day);
                if ($currentTime->gt($targetTime)) {
                    $isLateClockOut = true;
                    if ($penaltyClockOut > 0) {
                        $deductedClockOut = $penaltyClockOut;
                        auth()->user()->decrement('points', $penaltyClockOut);
                        if (auth()->user()->points < 0) {
                            auth()->user()->update(['points' => 0]);
                        }
                    }
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        if ($attendance) {
            $attendance->update([
                'clock_out' => $currentTime,
                'closing_cash' => (float)$this->closingCashInput,
                'closing_report' => $this->closingReportText,
                'points_at_closing' => (int)(auth()->user()->points + auth()->user()->pending_points),
            ]);
        }

        if ($isLateClockOut && $deductedClockOut > 0) {
            session()->flash('toast', 'Laporan closing berhasil dikirim. Anda TERLAMBAT clock-out! Poin berkurang ' . $deductedClockOut);
        } else {
            session()->flash('toast', 'Laporan closing berhasil dikirim dan Anda telah clock-out.');
        }
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.late-report')
            ->layout('layouts.blank', ['title' => 'Isi Laporan Closing | Superapps TEFA']);
    }
}
