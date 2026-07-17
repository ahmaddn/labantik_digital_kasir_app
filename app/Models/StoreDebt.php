<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreDebt extends Model
{
    use HasUuids;

    protected $fillable = [
        'jurusan_id',
        'supplier_id',
        'creditor_name',
        'amount',
        'remaining_amount',
        'status',
        'note',
        'due_date',
        'date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'date' => 'date',
        'amount' => 'integer',
        'remaining_amount' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }
}
