<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierTaskDefinition extends Model
{
    use HasUuids;

    protected $fillable = [
        'jurusan_id',
        'group_id',
        'task_name',
        'description',
        'date',
        'priority',
        'category',
        'is_routine',
        'requires_proof',
        'deadline_at',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'deadline_at' => 'datetime',
        'is_routine' => 'boolean',
        'requires_proof' => 'boolean',
    ];

    public static function statusLabels(): array
    {
        return [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'critical' => 'Paling Penting',
        ];
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::statusLabels()[$this->priority ?? 'medium'] ?? ucfirst($this->priority ?? 'medium');
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
            'high' => 'bg-orange-50 text-orange-700 dark:bg-orange-950/30 dark:text-orange-400',
            'critical' => 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400',
            default => 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400',
        };
    }

    /**
     * Relationship: Task Definition has many Assignments
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(CashierTaskAssignment::class, 'task_definition_id');
    }

    /**
     * Relationship: Task Definition belongs to Jurusan
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Relationship: Task Definition created by User (admin/pengelola)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
