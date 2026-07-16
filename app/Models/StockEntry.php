<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockEntry extends Model
{
    use HasUuids;

    protected $fillable = [
        'product_id',
        'date',
        'opening_stock',
        'closing_stock',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
