<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierTask extends Model
{
    use HasUuids;

    protected $fillable = [
        'jurusan_id',
        'group_id',
        'assigned_to',
        'date',
        'task_name',
        'description',
        'deadline_at',
        'status',
        'priority',
        'category',
        'is_routine',
        'is_completed',
        'completed_at',
        'created_by',
        'completion_report',
        'proof_image',
        'approval_status',
        'rejection_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'date' => 'date',
        'deadline_at' => 'datetime',
        'is_completed' => 'boolean',
        'is_routine' => 'boolean',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public static function statusLabels(): array
    {
        return [
            'new' => 'Baru',
            'pending' => 'Sedang Dikerjakan',
            'completed' => 'Selesai',
        ];
    }

    public static function priorityLabels(): array
    {
        return [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'critical' => 'Paling Penting',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status ?? 'new'] ?? ucfirst($this->status ?? 'new');
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::priorityLabels()[$this->priority ?? 'medium'] ?? ucfirst($this->priority ?? 'medium');
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

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'new' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400',
            'pending' => 'bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400',
            'completed' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400',
            default => 'bg-gray-50 text-gray-600 dark:bg-gray-900/30 dark:text-gray-300',
        };
    }

    public function getGroupAssigneesNamesAttribute(): string
    {
        if ($this->is_routine) {
            return 'Semua Kasir';
        }

        if (! $this->group_id) {
            return $this->user?->name ?: '-';
        }

        $assigneeNames = \App\Models\CashierTask::where('group_id', $this->group_id)
            ->with('user')
            ->get()
            ->pluck('user.name')
            ->filter()
            ->unique()
            ->join(', ');

        return $assigneeNames ?: ($this->user?->name ?: '-');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
