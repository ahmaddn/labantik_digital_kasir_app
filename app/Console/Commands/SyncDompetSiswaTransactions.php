<?php

namespace App\Console\Commands;

use App\Services\DompetSiswaApiService;
use Illuminate\Console\Command;

class SyncDompetSiswaTransactions extends Command
{
    /**
     * Nama dan signature command artisan.
     *
     * @var string
     */
    protected $signature = 'tefa:sync-dompet-transactions {merchant_id?}';

    /**
     * Deskripsi command artisan.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi real-time transaksi penjualan dari server Dompet Siswa SMKN 1 Talaga ke database TEFA';

    /**
     * Eksekusi console command.
     */
    public function handle(DompetSiswaApiService $apiService): int
    {
        $merchantId = $this->argument('merchant_id');

        $this->info('Memulai sinkronisasi transaksi real-time dari Dompet Siswa...');

        $result = $apiService->syncRealtimeTransactions($merchantId);

        \Illuminate\Support\Facades\Log::info('[CRON WORKER] Sync Transaksi Dompet Siswa otomatis dijalankan. Status: ' . $result['status'] . ', Total synced: ' . $result['synced_count']);

        $this->info("Sinkronisasi selesai. Total transaksi baru disinkronkan: {$result['synced_count']}");

        if (! empty($result['errors'])) {
            foreach ($result['errors'] as $error) {
                $this->warn($error);
            }
        }

        return Command::SUCCESS;
    }
}
