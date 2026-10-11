<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Listen for Logout event to auto-clock out kasir if not already done
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            function (\Illuminate\Auth\Events\Logout $event) {
                $user = $event->user;
                if ($user) {
                    try {
                        $attendance = \App\Models\CashierAttendance::where('user_id', $user->id)
                            ->whereDate('date', now()->toDateString())
                            ->whereNull('clock_out')
                            ->first();

                        if ($attendance) {
                            $currentTime = now();
                            $jurusan = \App\Models\Jurusan::find($attendance->jurusan_id);
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
                                    $targetTime = \Carbon\Carbon::createFromFormat('H:i', $targetClockOut);
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

                            $status = $clockInStatus . '_' . $clockOutStatus;

                            $attendance->update([
                                'clock_out' => $currentTime,
                                'status' => $status,
                                'points_at_closing' => (int) ($user->points + $user->pending_points),
                                'clock_in_status' => $clockInStatus,
                                'clock_out_status' => $clockOutStatus,
                            ]);
                        }
                    } catch (\Exception $e) {
                        // ignore error
                    }
                }
            }
        );
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
