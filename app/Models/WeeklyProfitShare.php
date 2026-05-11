<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyProfitShare extends Model
{
    protected $fillable = [
        'month_name',
        'week_number',
        'week_start',
        'week_end',
        'total_profit',
        'kas_amount',
        'shared_amount'
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date'
    ];
}
