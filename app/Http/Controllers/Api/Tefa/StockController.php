<?php

namespace App\Http\Controllers\Api\Tefa;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Tefa\StockResource;
use App\Models\Jurusan;
use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    /**
     * GET /api/v1/tefa/products/{product_id}/stock
     * Cek stok real-time untuk 1 produk.
     */
    public function checkProductStock(string $product_id): JsonResponse
    {
        $today = now()->toDateString();

        $product = Product::with(['stockEntries' => fn ($q) => $q->where('date', $today)])
            ->find($product_id);

        if (! $product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Stok produk berhasil dimuat',
            'data'    => new StockResource($product),
        ]);
    }

    /**
     * GET /api/v1/tefa/merchants/{tefa_merchant_id}/stock
     * Cek stok real-time seluruh produk aktif pada suatu merchant (kantin).
     */
    public function checkMerchantStock(string $tefa_merchant_id): JsonResponse
    {
        $today = now()->toDateString();

        $merchant = Jurusan::find($tefa_merchant_id);

        if (! $merchant) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Merchant tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        $products = Product::where('jurusan_id', $tefa_merchant_id)
            ->where('is_active', true)
            ->with(['stockEntries' => fn ($q) => $q->where('date', $today)])
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar stok produk merchant berhasil dimuat',
            'data'    => [
                'tefa_merchant_id' => $merchant->id,
                'store_name'       => $merchant->name,
                'stocks'           => StockResource::collection($products),
            ],
        ]);
    }

    /**
     * POST /api/v1/tefa/stock/deduct
     * Pengurangan / penguraian stok produk secara atomic dari aplikasi Dompet Siswa.
     */
    public function deductStock(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items'                    => 'required|array|min:1',
            'items.*.tefa_product_id'  => 'required|string|exists:products,id',
            'items.*.quantity'         => 'required|integer|min:1',
            'reference_id'             => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi request gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $today = now()->toDateString();
        $items = $request->input('items');

        try {
            $deductedProducts = DB::transaction(function () use ($items, $today) {
                $processed = [];

                foreach ($items as $item) {
                    $productId = $item['tefa_product_id'];
                    $qtyToDeduct = (int) $item['quantity'];

                    $product = Product::lockForUpdate()->find($productId);

                    if (! $product || ! $product->is_active) {
                        throw new \Exception("Produk [ID: {$productId}] tidak aktif atau tidak ditemukan.");
                    }

                    $stockEntry = StockEntry::where('product_id', $productId)
                        ->where('date', $today)
                        ->lockForUpdate()
                        ->first();

                    if (! $stockEntry) {
                        // Cari stok akhir terakhir (misal kemarin atau pencatatan paling akhir)
                        $lastStockEntry = StockEntry::where('product_id', $productId)
                            ->where('date', '<', $today)
                            ->orderBy('date', 'desc')
                            ->first();

                        // Prioritaskan sisa stok kemarin, jika tidak ada baru pakai master product stock
                        $initialStock = $lastStockEntry ? $lastStockEntry->closing_stock : ($product->stock ?? 0);

                        // Auto-create pencatatan stok hari ini
                        $stockEntry = StockEntry::create([
                            'jurusan_id'     => $product->jurusan_id,
                            'product_id'     => $product->id,
                            'date'           => $today,
                            'opening_stock'  => $initialStock,
                            'closing_stock'  => $initialStock,
                            'expected_stock' => $initialStock,
                        ]);
                    }

                    if ($stockEntry->closing_stock < $qtyToDeduct) {
                        throw new \Exception("Stok produk '{$product->name}' tidak mencukupi. Tersisa: {$stockEntry->closing_stock}, diminta: {$qtyToDeduct}.");
                    }

                    // Kurangi stok secara atomic
                    $stockEntry->decrement('closing_stock', $qtyToDeduct);
                    $stockEntry->refresh();

                    // Catat transaksi di aplikasi TEFA Kasir dengan payment_method 'dompet_digital'
                    \App\Models\Transaction::create([
                        'jurusan_id'     => $product->jurusan_id,
                        'user_id'        => null,
                        'product_id'     => $product->id,
                        'supplier_id'    => $product->supplier_id,
                        'reference'      => $request->input('reference_id') ?? 'DOMPET-'.strtoupper(\Illuminate\Support\Str::random(8)),
                        'transacted_at'  => now(),
                        'buyer_name'     => 'Siswa (Dompet Digital)',
                        'quantity'       => $qtyToDeduct,
                        'unit_price'     => $product->price,
                        'unit_profit'    => $product->profit ?? 0,
                        'total_price'    => $product->price * $qtyToDeduct,
                        'debt_amount'    => 0,
                        'change_due'     => 0,
                        'status'         => 'lunas',
                        'payment_method' => 'dompet_digital',
                        'note'           => 'Transaksi via API Dompet Siswa (Saldo Digital)',
                    ]);

                    $processed[] = [
                        'tefa_product_id'   => $product->id,
                        'name'              => $product->name,
                        'deducted_quantity' => $qtyToDeduct,
                        'remaining_stock'   => (int) $stockEntry->closing_stock,
                    ];
                }

                return $processed;
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Pengurangan stok berhasil diproses',
                'data'    => [
                    'reference_id'     => $request->input('reference_id'),
                    'deducted_at'      => now()->toIso8601String(),
                    'deducted_items'   => $deductedProducts,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null,
            ], 400);
        }
    }
}
