<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierAttendance extends Model
{
    use HasUuids;

    protected $fillable = [
        'cashier_schedule_id',
        'user_id',
        'jurusan_id',
        'date',
        'clock_in',
        'clock_out',
        'opening_cash',
        'closing_cash',
        'closing_report',
        'edit_count',
        'status',
        'points_at_closing',
        'clock_in_status',
        'clock_out_status',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CashierSchedule::class, 'cashier_schedule_id');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }
}
