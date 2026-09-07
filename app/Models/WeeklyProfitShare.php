<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyProfitShare extends Model
{
    use HasUuids;

    protected $fillable = [
        'jurusan_id',
        'month_name',
        'week_number',
        'week_start',
        'week_end',
        'total_profit',
        'kas_amount',
        'shared_amount',
        'created_by',
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
    ];
}
