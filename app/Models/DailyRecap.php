<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyRecap extends Model
{
    use HasUuids;

    protected $fillable = [
        'jurusan_id',
        'date',
        'month_week',
        'month_name',
        'total_revenue_real',
        'total_revenue_all',
        'total_profit',
        'total_modal',
        'count_received',
        'count_unpaid_change',
        'count_no_payment',
        'count_borrowed',
        'actual_cash',
        'retained_change_cash',
        'cash_note',
        'generated_at',
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('active', function ($builder) {
            $builder->where('is_archived', false);
        });

        static::creating(function (DailyRecap $dailyRecap) {
            if ($dailyRecap->date) {
                $carbonDate = \Carbon\Carbon::parse($dailyRecap->date);
                if (empty($dailyRecap->month_week)) {
                    $dailyRecap->month_week = ceil($carbonDate->day / 7);
                }
                if (empty($dailyRecap->month_name)) {
                    $dailyRecap->month_name = $carbonDate->translatedFormat('F');
                }
            }
        });
    }

    protected $casts = [
        'date' => 'date',
        'generated_at' => 'datetime',
    ];
}
