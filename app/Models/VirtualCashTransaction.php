<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualCashTransaction extends Model
{
    use HasUuids;

    protected $table = 'virtual_cash_transactions';

    protected $fillable = [
        'jurusan_id',
        'date',
        'source_method',  // 'transfer' atau 'qris'
        'type',           // 'income' atau 'expense'
        'amount',
        'description',
        'reference',
        'cash_category_id',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Scope untuk filter per metode (transfer / qris)
     */
    public function scopeTransfer($query)
    {
        return $query->where('source_method', 'transfer');
    }

    public function scopeQris($query)
    {
        return $query->where('source_method', 'qris');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function cashCategory(): BelongsTo
    {
        return $this->belongsTo(CashCategory::class, 'cash_category_id');
    }
}
