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
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
    ];
}
