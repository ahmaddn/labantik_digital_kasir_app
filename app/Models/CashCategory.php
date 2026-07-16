<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashCategory extends Model
{
    use HasUuids;

    protected $fillable = ['jurusan_id', 'name'];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class);
    }
}
