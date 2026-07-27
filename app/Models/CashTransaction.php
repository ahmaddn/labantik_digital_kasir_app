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

    protected static function booted()
    {
        static::addGlobalScope('active', function ($builder) {
            $builder->where('is_archived', false);
        });

        static::saved(function ($transaction) {
            if ($transaction->jurusan_id) {
                \Illuminate\Support\Facades\Cache::forget('cash_balances_' . $transaction->jurusan_id);
                if ($transaction->date) {
                    $month = \Carbon\Carbon::parse($transaction->date)->format('Y-m');
                    \Illuminate\Support\Facades\Cache::forget('cash_monthly_stats_' . $transaction->jurusan_id . '_' . $month);
                }
            }
        });

        static::deleted(function ($transaction) {
            if ($transaction->jurusan_id) {
                \Illuminate\Support\Facades\Cache::forget('cash_balances_' . $transaction->jurusan_id);
                if ($transaction->date) {
                    $month = \Carbon\Carbon::parse($transaction->date)->format('Y-m');
                    \Illuminate\Support\Facades\Cache::forget('cash_monthly_stats_' . $transaction->jurusan_id . '_' . $month);
                }
            }
        });
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function cashCategory()
    {
        return $this->belongsTo(CashCategory::class, 'cash_category_id');
    }
}
