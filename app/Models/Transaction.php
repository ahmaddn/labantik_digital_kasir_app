<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([TransactionObserver::class])]
class Transaction extends Model
{
    protected $fillable = [
        'product_id',
        'transacted_at',
        'buyer_name',
        'quantity',
        'unit_price',
        'unit_profit',
        'total_price',
        'status',
        'note'
    ];

    protected $casts = [
        'transacted_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
