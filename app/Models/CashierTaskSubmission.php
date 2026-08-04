<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierTaskSubmission extends Model
{
    use HasUuids;

    protected $fillable = [
        'task_assignment_id',
        'submitted_by',
        'report',
        'proof_image',
        'submitted_at',
        'approval_status',
        'reviewed_by',
        'rejection_note',
        'reviewed_at',
        'submission_version',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public static function approvalStatusLabels(): array
    {
        return [
            'pending' => 'Menunggu Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        return self::approvalStatusLabels()[$this->approval_status ?? 'pending'] ?? ucfirst($this->approval_status ?? 'pending');
    }

    public function getApprovalStatusBadgeClassAttribute(): string
    {
        return match ($this->approval_status) {
            'approved' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400',
            'rejected' => 'bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400',
            'pending' => 'bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400',
            default => 'bg-gray-50 text-gray-600 dark:bg-gray-900/30 dark:text-gray-300',
        };
    }

    /**
     * Relationship: Submission belongs to Task Assignment
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(CashierTaskAssignment::class, 'task_assignment_id');
    }

    /**
     * Relationship: Submission submitted by User (Kasir)
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Relationship: Submission reviewed by User (Admin)
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Eager load task definition through assignment
     */
    public function taskDefinition()
    {
        return $this->assignment->taskDefinition;
    }
}
