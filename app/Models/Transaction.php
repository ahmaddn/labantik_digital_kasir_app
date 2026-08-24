<?php

namespace App\Models;

use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([TransactionObserver::class])]
class Transaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'jurusan_id',
        'user_id',
        'product_id',
        'supplier_id',
        'reference',
        'transacted_at',
        'buyer_name',
        'quantity',
        'unit_price',
        'unit_profit',
        'total_price',
        'debt_amount',
        'change_due',
        'status',
        'note',
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    protected $casts = [
        'transacted_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDebt($query)
    {
        return $query->whereIn('status', ['belum_menerima_uang', 'uang_dipinjam']);
    }

    public function scopeUnreturnedChange($query)
    {
        return $query->where('status', 'belum_kembalian');
    }

    /**
     * @param  Builder<Transaction>  $query
     * @return Builder<Transaction>
     */
    public function scopeForReporting($query)
    {
        return $query->withoutGlobalScope('active');
    }

    public function modifiers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Modifier::class, 'transaction_modifiers')->withPivot('price')->withTimestamps();
    }
}
