<?php

namespace App\Http\Controllers\Api\Tefa;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Tefa\TransactionResource;
use App\Models\Jurusan;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * GET /api/v1/tefa/transactions
     *
     * Menampilkan histori transaksi penjualan kantin TEFA (termasuk yang menggunakan Dompet Digital).
     * Query Filter: tefa_merchant_id, start_date, end_date, payment_method, status, search, per_page
     */
    public function index(Request $request): JsonResponse
    {
        $merchantId = $request->query('tefa_merchant_id');
        $startDate  = $request->query('start_date');
        $endDate    = $request->query('end_date');
        $paymentMethod = $request->query('payment_method');
        $status     = $request->query('status');
        $search     = $request->query('search');
        $perPage    = min((int) $request->query('per_page', 15), 100);

        $query = Transaction::with(['jurusan', 'product'])
            ->when($merchantId, fn ($q) => $q->where('jurusan_id', $merchantId))
            ->when($startDate, fn ($q) => $q->whereDate('transacted_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('transacted_at', '<=', $endDate))
            ->when($paymentMethod, fn ($q) => $q->where('payment_method', $paymentMethod))
            ->when($status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('reference', 'like', "%{$search}%")
                        ->orWhere('buyer_name', 'like', "%{$search}%")
                        ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('transacted_at', 'desc');

        $paginated = $query->paginate($perPage);

        $merchantName = null;
        if ($merchantId) {
            $merchant = Jurusan::find($merchantId);
            $merchantName = $merchant?->name;
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Histori transaksi kantin TEFA berhasil dimuat.',
            'meta'    => [
                'tefa_merchant_id' => $merchantId,
                'merchant_name'    => $merchantName,
                'filters'          => [
                    'start_date'     => $startDate,
                    'end_date'       => $endDate,
                    'payment_method' => $paymentMethod,
                    'status'         => $status ?? 'all',
                ],
                'summary' => [
                    'total_transactions' => $paginated->total(),
                    'total_revenue'      => (float) $query->sum('total_price'),
                ],
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'per_page'     => $paginated->perPage(),
                    'total_items'  => $paginated->total(),
                    'total_pages'  => $paginated->lastPage(),
                ],
            ],
            'data' => TransactionResource::collection($paginated->items()),
        ]);
    }
}
