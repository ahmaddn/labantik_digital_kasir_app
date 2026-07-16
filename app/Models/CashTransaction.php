<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'jurusan_id',
        'date',
        'cash_type',
        'cash_category_id',
        'type',
        'amount',
        'description',
        'reference',
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function cashCategory()
    {
        return $this->belongsTo(CashCategory::class, 'cash_category_id');
    }
}
