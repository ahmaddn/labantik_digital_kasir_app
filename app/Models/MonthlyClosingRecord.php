<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyClosingRecord extends Model
{
    use HasUuids;

    protected $fillable = [
        'jurusan_id',
        'period',
        'pending_points_snapshot',
        'carry_forward_modal',
        'carry_forward_profit',
        'closed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pending_points_snapshot' => 'array',
            'closed_at' => 'datetime',
        ];
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }
}
