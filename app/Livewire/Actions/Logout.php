<?php

namespace App\Livewire\Actions;

use App\Models\CashierAttendance;
use App\Models\Jurusan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        $user = Auth::user();
        $activeRoleName = Session::get('active_role_name');
        $activeJurusanId = Session::get('active_jurusan_id');

        if ($user && $activeRoleName === 'kasir') {
            try {
                $attendance = CashierAttendance::where('user_id', $user->id)
                    ->whereDate('date', now()->toDateString())
                    ->whereNull('clock_out')
                    ->first();

                if ($attendance) {
                    $currentTime = now();
                    $jurusan = Jurusan::find($activeJurusanId ?: $attendance->jurusan_id);
                    $settings = $jurusan ? ($jurusan->theme_settings ?: []) : [];
                    $targetClockOut = $settings['clock_out_time'] ?? '15:00';
                    $penaltyClockOut = (int) ($settings['late_clock_out_penalty'] ?? 0);
                    $bonusPoints = 10;

                    $clockInStatus = $attendance->status ?? 'present';
                    if (str_contains($clockInStatus, '_')) {
                        $parts = explode('_', $clockInStatus);
                        $clockInStatus = $parts[0];
                    }

                    $clockOutStatus = 'overtime';
                    if ($targetClockOut) {
                        try {
                            $targetTime = Carbon::createFromFormat('H:i', $targetClockOut);
                            $targetTime->setDate($currentTime->year, $currentTime->month, $currentTime->day);

                            if ($currentTime->lt($targetTime)) {
                                $clockOutStatus = 'early_checkout';
                                if ($penaltyClockOut > 0) {
                                    $user->decrement('points', $penaltyClockOut);
                                    if ($user->points < 0) {
                                        $user->update(['points' => 0]);
                                    }
                                }
                            } else {
                                $clockOutStatus = 'overtime';
                                $user->increment('points', $bonusPoints);
                                $user->increment('streak', 1);
                            }
                        } catch (\Exception $e) {
                            // ignore format exception
                        }
                    }

                    $status = $clockInStatus.'_'.$clockOutStatus;

                    $attendance->update([
                        'clock_out' => $currentTime,
                        'status' => $status,
                        'points_at_closing' => (int) ($user->points + $user->pending_points),
                        'clock_in_status' => $clockInStatus,
                        'clock_out_status' => $clockOutStatus,
                    ]);
                }
            } catch (\Exception $e) {
                // Ignore errors during logout attendance update to allow logout to proceed
            }
        }

        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}
