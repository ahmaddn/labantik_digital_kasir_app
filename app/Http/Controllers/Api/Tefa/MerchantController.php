<?php

namespace App\Http\Controllers\Api\Tefa;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Tefa\MerchantResource;
use App\Http\Resources\Api\Tefa\MerchantWithProductsResource;
use App\Models\Jurusan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    /**
     * GET /api/v1/tefa/merchants
     *
     * Daftar seluruh merchant kantin TEFA.
     * Query param: status (active|inactive, default: active)
     *
     * Eager load 'pengelolaUsers' dalam 1 query (IN clause),
     * tidak ada N+1 meskipun ada ratusan merchant.
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status', 'active');

        $merchants = Jurusan::with('pengelolaUsers')
            ->when(
                $status === 'inactive',
                fn ($q) => $q->where('is_active', false),
                fn ($q) => $q->where('is_active', true),   // default: active
            )
            ->whereNull('parent_id')   // hanya top-level jurusan (kantin utama)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar merchant kantin TEFA berhasil dimuat',
            'data'    => MerchantResource::collection($merchants),
        ]);
    }

    /**
     * GET /api/v1/tefa/merchants/{tefa_merchant_id}/products
     *
     * Daftar produk (menu) kantin TEFA berdasarkan merchant ID.
     * tefa_merchant_id mendukung UUID string sesuai spec.
     *
     * Eager load 'pengelolaUsers', 'products.category', 'products.supplier'
     * semuanya dalam 3 query flat — nol N+1.
     */
    public function products(string $tefa_merchant_id): JsonResponse
    {
        // Eager load semua relasi yang dibutuhkan sekaligus:
        //   1 query → jurusan
        //   1 query → pengelolaUsers (via role_user + whereHas roles)
        //   1 query → products (filter is_active) + category + supplier
        $merchant = Jurusan::with([
            'pengelolaUsers',
            'products' => function ($q) {
                $q->where('is_active', true)
                    ->with(['category', 'supplier']);
            },
        ])->find($tefa_merchant_id);

        if (! $merchant) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Merchant tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar produk menu TEFA berhasil dimuat',
            'data'    => new MerchantWithProductsResource($merchant),
        ]);
    }
}
