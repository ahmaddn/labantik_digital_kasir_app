<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierOrder extends Model
{
    use HasUuids;

    protected $fillable = [
        'supplier_id',
        'supplier_name',
        'supplier_phone',
        'status',
        'notes',
        'total_items',
        'created_by',
        'ordered_at',
        'received_at',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
        'received_at' => 'datetime',
        'total_items' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplierOrderItem::class);
    }

    /**
     * Format phone number to WhatsApp international standard (e.g. 628xxx)
     */
    public static function formatWhatsAppNumber(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        // Clean non-numeric characters except +
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Normalize leading 08 to 628
        if (str_starts_with($cleaned, '08')) {
            $cleaned = '628'.substr($cleaned, 2);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '628'.substr($cleaned, 1);
        }

        return $cleaned;
    }
}

