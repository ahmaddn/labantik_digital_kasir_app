<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Sinkronisasi Real-time Transaksi Dompet Siswa setiap 1 menit (Real-time Background Cron)
\Illuminate\Support\Facades\Schedule::command('tefa:sync-dompet-transactions')
    ->everyMinute()
    ->withoutOverlapping();
