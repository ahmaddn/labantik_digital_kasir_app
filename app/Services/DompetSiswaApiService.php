<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DompetSiswaApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.dompet_siswa.base_url', 'https://dompet.smkn1talaga.sch.id');
        $this->apiKey = config('services.dompet_siswa.api_key', env('DOMPET_SISWA_API_KEY', 'ds_live_R8VgLxdlIfj3iPxnCMs10FeTe8tg2U8Q'));
    }

    /**
     * Penarikan data histori transaksi kantin TEFA dari Sistem Dompet Siswa SMKN 1 Talaga.
     * Endpoint: GET /api/v1/external/tefa/transactions
     *
     * @param string $tefaMerchantId ID Kantin TEFA (UUID/String)
     * @param array $filters (start_date, end_date, status, search, min_amount, max_amount, sort_by, sort_dir, page, per_page)
     */
    public function getTransactions(string $tefaMerchantId, array $filters = []): array
    {
        $queryParams = array_merge([
            'tefa_merchant_id' => $tefaMerchantId,
        ], $filters);

        try {
            $response = Http::withHeaders([
                'X-API-KEY'    => $this->apiKey,
                'Accept'       => 'application/json',
            ])->timeout(15)->get("{$this->baseUrl}/api/v1/external/tefa/transactions", $queryParams);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('DompetSiswaApiService failed: ' . $response->body());

            return [
                'status'  => 'error',
                'code'    => $response->status(),
                'message' => $response->json('message') ?? 'Gagal menghubungi server Dompet Siswa.',
                'data'    => [],
            ];
        } catch (\Exception $e) {
            Log::error('DompetSiswaApiService Exception: ' . $e->getMessage());

            return [
                'status'  => 'error',
                'message' => 'Terjadi kesalahan koneksi ke API Dompet Siswa: ' . $e->getMessage(),
                'data'    => [],
            ];
        }
    }
}
