<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierOrderItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'supplier_order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class, 'supplier_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

