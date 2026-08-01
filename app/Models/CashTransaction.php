<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

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
        static::saved(function ($transaction) {
            if ($transaction->jurusan_id) {
                Cache::forget('cash_balances_'.$transaction->jurusan_id);
                if ($transaction->date) {
                    $month = Carbon::parse($transaction->date)->format('Y-m');
                    Cache::forget('cash_monthly_stats_'.$transaction->jurusan_id.'_'.$month);
                }
            }
        });

        static::deleted(function ($transaction) {
            if ($transaction->jurusan_id) {
                Cache::forget('cash_balances_'.$transaction->jurusan_id);
                if ($transaction->date) {
                    $month = Carbon::parse($transaction->date)->format('Y-m');
                    Cache::forget('cash_monthly_stats_'.$transaction->jurusan_id.'_'.$month);
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

    /**
     * @param  Builder<CashTransaction>  $query
     * @return Builder<CashTransaction>
     */
    public function scopeForReporting($query)
    {
        return $query->withoutGlobalScope('active');
    }

    /**
     * @param  Builder<CashTransaction>  $query
     * @return Builder<CashTransaction>
     */
    public function scopeExcludeCarryForward($query)
    {
        return $query->where(function ($query) {
            $query->where('description', 'not like', 'Saldo Awal Modal Bawaan%')
                ->where('description', 'not like', 'Saldo Awal Keuntungan Bawaan%');
        });
    }

    /**
     * @param  Builder<CashTransaction>  $query
     * @return Builder<CashTransaction>
     */
    public function scopeCarryForwardOnly($query)
    {
        return $query->where(function ($query) {
            $query->where('description', 'like', 'Saldo Awal Modal Bawaan%')
                ->orWhere('description', 'like', 'Saldo Awal Keuntungan Bawaan%');
        });
    }
}
