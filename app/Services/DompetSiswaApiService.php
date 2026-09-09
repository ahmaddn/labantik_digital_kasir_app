<?php

namespace App\Services;

use App\Models\Jurusan;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
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
                'X-API-KEY' => $this->apiKey,
                'Accept'    => 'application/json',
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

    /**
     * Sinkronisasi Real-time Transaksi dari Server Dompet Siswa ke Database Aplikasi TEFA.
     * Mengambil transaksi terbaru dan menyimpan entri transaksi + update stok TEFA secara otomatis.
     */
    public function syncRealtimeTransactions(?string $merchantId = null): array
    {
        $today = now()->toDateString();
        $merchants = $merchantId 
            ? Jurusan::where('id', $merchantId)->whereNull('parent_id')->get()
            : Jurusan::whereNull('parent_id')->where('is_active', true)->get();

        $totalSynced = 0;
        $errors = [];

        foreach ($merchants as $merchant) {
            $response = $this->getTransactions($merchant->id, [
                'start_date' => $today,
                'end_date'   => $today,
                'status'     => 'completed',
                'per_page'   => 100,
            ]);

            if (isset($response['status']) && $response['status'] === 'success' && isset($response['data'])) {
                foreach ($response['data'] as $order) {
                    $refNumber = $order['reference_number'] ?? ($order['order_id'] ? 'PAY-'.$order['order_id'] : null);
                    $studentName = $order['student']['name'] ?? 'Siswa (Dompet Digital)';
                    $transactedAt = isset($order['transaction_time']) ? \Carbon\Carbon::parse($order['transaction_time']) : now();

                    if (! $refNumber || ! isset($order['items'])) {
                        continue;
                    }

                    foreach ($order['items'] as $item) {
                        $productId = $item['tefa_product_id'] ?? null;
                        $qty = (int) ($item['quantity'] ?? 1);
                        $price = (float) ($item['price'] ?? 0);

                        if (! $productId) {
                            continue;
                        }

                        $product = Product::find($productId);
                        if (! $product) {
                            continue;
                        }

                        // Cek apakah transaksi dengan reference_number dan product_id ini sudah tercatat di TEFA
                        $exists = Transaction::where('reference', $refNumber)
                            ->where('product_id', $product->id)
                            ->exists();

                        if (! $exists) {
                            DB::transaction(function () use ($product, $merchant, $refNumber, $studentName, $qty, $price, $transactedAt, &$totalSynced) {
                                // 1. Simpan Transaksi Penjualan TEFA
                                Transaction::create([
                                    'jurusan_id'     => $merchant->id,
                                    'user_id'        => null,
                                    'product_id'     => $product->id,
                                    'supplier_id'    => $product->supplier_id,
                                    'reference'      => $refNumber,
                                    'transacted_at'  => $transactedAt,
                                    'buyer_name'     => $studentName,
                                    'quantity'       => $qty,
                                    'unit_price'     => $price > 0 ? $price : $product->price,
                                    'unit_profit'    => $product->profit ?? 0,
                                    'total_price'    => ($price > 0 ? $price : $product->price) * $qty,
                                    'debt_amount'    => 0,
                                    'change_due'     => 0,
                                    'status'         => 'lunas',
                                    'payment_method' => 'dompet_digital',
                                    'note'           => 'Sinkronisasi Realtime API Dompet Siswa',
                                ]);

                                // 2. Potong stok di stock_entries jika ada record hari ini
                                $stockEntry = StockEntry::where('product_id', $product->id)
                                    ->where('date', $transactedAt->toDateString())
                                    ->lockForUpdate()
                                    ->first();

                                if ($stockEntry && $stockEntry->closing_stock >= $qty) {
                                    $stockEntry->decrement('closing_stock', $qty);
                                }

                                $totalSynced++;
                            });
                        }
                    }
                }
            } else {
                if (isset($response['message'])) {
                    $errors[] = "Merchant {$merchant->name}: {$response['message']}";
                }
            }
        }

        return [
            'status'       => count($errors) === 0 ? 'success' : 'partial_success',
            'synced_count' => $totalSynced,
            'errors'       => $errors,
        ];
    }
}
