<?php

namespace App\Http\Resources\Api\Tefa;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'transaction_id'   => $this->id,
            'reference_number' => $this->reference,
            'merchant_id'      => $this->jurusan_id,
            'merchant_name'    => $this->jurusan?->name,
            'buyer_name'       => $this->buyer_name ?? 'Siswa (Dompet Digital)',
            'product_id'       => $this->product_id,
            'product_name'     => $this->product?->name,
            'quantity'         => (int) $this->quantity,
            'unit_price'       => (float) $this->unit_price,
            'total_price'      => (float) $this->total_price,
            'payment_method'   => $this->payment_method,
            'status'           => $this->status,
            'transacted_at'    => $this->transacted_at?->toIso8601String(),
            'note'             => $this->note,
        ];
    }
}
