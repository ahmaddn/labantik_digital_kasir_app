<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierTaskAssignment extends Model
{
    use HasUuids;

    protected $fillable = [
        'task_definition_id',
        'assigned_to',
        'jurusan_id',
        'assignment_status',
        'acknowledged_at',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    public static function assignmentStatusLabels(): array
    {
        return [
            'new' => 'Baru',
            'acknowledged' => 'Dilihat',
            'started' => 'Dimulai',
            'submitted' => 'Dilaporkan',
        ];
    }

    public function getAssignmentStatusLabelAttribute(): string
    {
        return self::assignmentStatusLabels()[$this->assignment_status ?? 'new'] ?? ucfirst($this->assignment_status ?? 'new');
    }

    /**
     * Relationship: Assignment belongs to Task Definition
     */
    public function taskDefinition(): BelongsTo
    {
        return $this->belongsTo(CashierTaskDefinition::class, 'task_definition_id');
    }

    /**
     * Relationship: Assignment assigned to User (Kasir)
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relationship: Assignment belongs to Jurusan
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Relationship: Assignment has many Submissions
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(CashierTaskSubmission::class, 'task_assignment_id');
    }

    /**
     * Get the latest submission for this assignment
     */
    public function latestSubmission()
    {
        return $this->hasOne(CashierTaskSubmission::class, 'task_assignment_id')
            ->latestOfMany('submitted_at');
    }
}
