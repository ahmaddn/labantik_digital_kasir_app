<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRecap extends Model
{
    protected $fillable = [
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
        'cash_note',
        'generated_at'
    ];

    protected $casts = [
        'date' => 'date',
        'generated_at' => 'datetime'
    ];
}
