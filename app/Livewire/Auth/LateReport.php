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
        $isEarlyClockOut = false;
        $isBonusClockOut = false;
        $deductedClockOut = 0;
        $bonusPoints = 10;

        $jurusan = \App\Models\Jurusan::find($activeJurusanId);
        $settings = $jurusan ? ($jurusan->theme_settings ?: []) : [];
        $targetClockOut = $settings['clock_out_time'] ?? '15:00';
        $penaltyClockOut = (int) ($settings['late_clock_out_penalty'] ?? 0);

        $status = 'present';
        $clockInStatus = $attendance ? $attendance->status : 'present';
        if (str_contains($clockInStatus, '_')) {
            $parts = explode('_', $clockInStatus);
            $clockInStatus = $parts[0];
        }

        $clockOutStatus = 'overtime';
        if ($targetClockOut) {
            try {
                $targetTime = \Carbon\Carbon::createFromFormat('H:i', $targetClockOut);
                $targetTime->setDate($currentTime->year, $currentTime->month, $currentTime->day);
                if ($currentTime->lt($targetTime)) {
                    $isEarlyClockOut = true;
                    $clockOutStatus = 'early_checkout';
                    if ($penaltyClockOut > 0) {
                        $deductedClockOut = $penaltyClockOut;
                        auth()->user()->decrement('points', $penaltyClockOut);
                        if (auth()->user()->points < 0) {
                            auth()->user()->update(['points' => 0]);
                        }
                    }
                } else {
                    $isBonusClockOut = true;
                    $clockOutStatus = 'overtime';
                    auth()->user()->increment('points', $bonusPoints);
                    auth()->user()->increment('streak', 1);
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        $status = $clockInStatus . '_' . $clockOutStatus;

        if ($attendance) {
            $attendance->update([
                'clock_out' => $currentTime,
                'closing_cash' => (float)$this->closingCashInput,
                'closing_report' => $this->closingReportText,
                'status' => $status,
                'points_at_closing' => (int)(auth()->user()->points + auth()->user()->pending_points),
                'clock_in_status' => $clockInStatus,
                'clock_out_status' => $clockOutStatus,
            ]);
        }

        if ($isEarlyClockOut && $deductedClockOut > 0) {
            session()->flash('toast', 'Laporan closing berhasil dikirim. Anda clock-out TERLALU CEPAT! Poin berkurang ' . $deductedClockOut);
        } elseif ($isBonusClockOut) {
            session()->flash('toast', 'Laporan closing berhasil dikirim. Selamat, Anda mendapat bonus +' . $bonusPoints . ' poin & +1 streak!');
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
