<?php

namespace App\Services;

use App\Models\CashierTaskDefinition;
use App\Models\CashierTaskAssignment;
use App\Models\CashierTaskSubmission;
use App\Models\User;
use Illuminate\Support\Str;

class CashierTaskService
{
    /**
     * Create a new task definition and assign to cashiers
     */
    public function createTask(array $data): CashierTaskDefinition
    {
        $groupId = $data['group_id'] ?? (string) Str::uuid();

        // Create task definition
        $taskDef = CashierTaskDefinition::create([
            'jurusan_id' => $data['jurusan_id'],
            'group_id' => $groupId,
            'task_name' => $data['task_name'],
            'description' => $data['description'] ?? null,
            'date' => $data['date'],
            'priority' => $data['priority'] ?? 'medium',
            'category' => $data['category'] ?? null,
            'is_routine' => $data['is_routine'] ?? false,
            'requires_proof' => $data['requires_proof'] ?? false,
            'deadline_at' => $data['deadline_at'] ?? null,
            'created_by' => $data['created_by'],
        ]);

        return $taskDef;
    }

    /**
     * Assign task to multiple cashiers
     */
    public function assignTaskToUsers(CashierTaskDefinition $taskDef, array $userIds): void
    {
        foreach ($userIds as $userId) {
            CashierTaskAssignment::firstOrCreate(
                [
                    'task_definition_id' => $taskDef->id,
                    'assigned_to' => $userId,
                ],
                [
                    'jurusan_id' => $taskDef->jurusan_id,
                    'assignment_status' => 'new',
                ]
            );
        }
    }

    /**
     * Assign routine task to cashiers scheduled on the task's date
     */
    public function assignTaskToAllCashiers(CashierTaskDefinition $taskDef): void
    {
        $targetJurusan = \App\Models\Jurusan::find($taskDef->jurusan_id);
        $allowedJurusanIds = [$taskDef->jurusan_id];
        if ($targetJurusan) {
            if ($targetJurusan->parent_id) {
                $allowedJurusanIds[] = $targetJurusan->parent_id;
            }
            $childIds = \App\Models\Jurusan::where('parent_id', $targetJurusan->id)->pluck('id')->toArray();
            $allowedJurusanIds = array_merge($allowedJurusanIds, $childIds);
        }
        $allowedJurusanIds = array_unique(array_filter($allowedJurusanIds));

        // Only assign to cashiers who are SCHEDULED on the task's date
        $scheduledUserIds = \App\Models\CashierSchedule::where('date', $taskDef->date)
            ->whereIn('jurusan_id', $allowedJurusanIds)
            ->pluck('user_id')
            ->unique();

        foreach ($scheduledUserIds as $userId) {
            $assignment = CashierTaskAssignment::firstOrCreate(
                [
                    'task_definition_id' => $taskDef->id,
                    'assigned_to' => $userId,
                ],
                [
                    'jurusan_id' => $taskDef->jurusan_id,
                    'assignment_status' => 'new',
                ]
            );

            if ($assignment->wasRecentlyCreated) {
                \App\Models\Notification::create([
                    'user_id' => $userId,
                    'title' => 'Tugas Rutin Baru',
                    'body' => 'Anda mendapatkan tugas rutin: "' . $taskDef->task_name . '"',
                    'type' => 'task',
                    'action_url' => '/my-tasks',
                ]);
            }
        }
    }

    /**
     * Auto-assign routine tasks to a kasir when they clock in / open kasir page
     * Called per kasir - only assigns tasks where kasir is scheduled today
     */
    public function autoAssignRoutineTasksForCashier(string $userId, string $jurusanId): void
    {
        $today = now()->toDateString();
        
        $targetJurusan = \App\Models\Jurusan::find($jurusanId);
        $allowedJurusanIds = [$jurusanId];
        if ($targetJurusan) {
            if ($targetJurusan->parent_id) {
                $allowedJurusanIds[] = $targetJurusan->parent_id;
            }
            $childIds = \App\Models\Jurusan::where('parent_id', $targetJurusan->id)->pluck('id')->toArray();
            $allowedJurusanIds = array_merge($allowedJurusanIds, $childIds);
        }
        $allowedJurusanIds = array_unique(array_filter($allowedJurusanIds));

        // Check if this kasir has schedule today
        $isScheduled = \App\Models\CashierSchedule::where('user_id', $userId)
            ->whereIn('jurusan_id', $allowedJurusanIds)
            ->where('date', $today)
            ->exists();

        if (!$isScheduled) {
            return;
        }

        // Find all routine task definitions that kasir doesn't have assignment for today yet
        $routineTaskDefs = CashierTaskDefinition::whereIn('jurusan_id', $allowedJurusanIds)
            ->where('is_routine', true)
            ->whereDoesntHave('assignments', function ($q) use ($userId, $today) {
                $q->where('assigned_to', $userId)
                  ->whereDate('created_at', $today);
            })
            ->get();

        foreach ($routineTaskDefs as $taskDef) {
            CashierTaskAssignment::create([
                'task_definition_id' => $taskDef->id,
                'assigned_to' => $userId,
                'jurusan_id' => $taskDef->jurusan_id,
                'assignment_status' => 'new',
            ]);

            \App\Models\Notification::create([
                'user_id' => $userId,
                'title' => 'Tugas Rutin Baru',
                'body' => 'Anda mendapatkan tugas rutin hari ini: "' . $taskDef->task_name . '"',
                'type' => 'task',
                'action_url' => '/my-tasks',
            ]);
        }
    }

    /**
     * Submit task completion report (create submission)
     */
    public function submitTaskCompletion(
        CashierTaskAssignment $assignment,
        string $report,
        ?string $proofImagePath = null
    ): CashierTaskSubmission {
        // Find latest submission to get version
        $latestSubmission = $assignment->submissions()
            ->orderBy('submission_version', 'desc')
            ->first();

        $version = $latestSubmission ? $latestSubmission->submission_version + 1 : 1;

        $submission = CashierTaskSubmission::create([
            'task_assignment_id' => $assignment->id,
            'submitted_by' => $assignment->assigned_to,
            'report' => $report,
            'proof_image' => $proofImagePath,
            'submitted_at' => now(),
            'approval_status' => 'pending',
            'submission_version' => $version,
        ]);

        // Update assignment status
        $assignment->update([
            'assignment_status' => 'submitted',
        ]);

        return $submission;
    }

    /**
     * Approve a task submission
     */
    public function approveSubmission(
        CashierTaskSubmission $submission,
        string $reviewedBy
    ): void {
        $submission->update([
            'approval_status' => 'approved',
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
        ]);

        // Award gamification points
        $this->awardPoints($submission->submitter, $submission->assignment->taskDefinition);
    }

    /**
     * Reject a task submission with note
     */
    public function rejectSubmission(
        CashierTaskSubmission $submission,
        string $rejectionNote,
        string $reviewedBy
    ): void {
        $submission->update([
            'approval_status' => 'rejected',
            'rejection_note' => $rejectionNote,
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
        ]);

        // Reset assignment status to allow revision
        $submission->assignment->update([
            'assignment_status' => 'started',
        ]);
    }

    /**
     * Award points to user based on task priority
     */
    private function awardPoints(User $user, CashierTaskDefinition $taskDef): void
    {
        $points = match ($taskDef->priority) {
            'low' => 5,
            'high' => 20,
            'critical' => 30,
            default => 10,
        };

        $user->increment('pending_points', $points);
        $user->increment('streak', 1);
    }

    /**
     * Get submissions pending review for a task definition
     */
    public function getPendingSubmissionsForTask(CashierTaskDefinition $taskDef)
    {
        return CashierTaskSubmission::query()
            ->whereIn('task_assignment_id', function ($query) use ($taskDef) {
                $query->select('id')
                    ->from('cashier_task_assignments')
                    ->where('task_definition_id', $taskDef->id);
            })
            ->where('approval_status', 'pending')
            ->with(['submitter', 'assignment'])
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    /**
     * Get all submissions for a task definition
     */
    public function getSubmissionsForTask(CashierTaskDefinition $taskDef)
    {
        return CashierTaskSubmission::query()
            ->whereIn('task_assignment_id', function ($query) use ($taskDef) {
                $query->select('id')
                    ->from('cashier_task_assignments')
                    ->where('task_definition_id', $taskDef->id);
            })
            ->with(['submitter', 'reviewer', 'assignment'])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Get submission status for a specific assignment
     */
    public function getAssignmentSubmissionStatus(CashierTaskAssignment $assignment)
    {
        $latestSubmission = $assignment->latestSubmission;

        if (!$latestSubmission) {
            return [
                'has_submission' => false,
                'status' => 'not_submitted',
                'submission' => null,
            ];
        }

        return [
            'has_submission' => true,
            'status' => $latestSubmission->approval_status,
            'submission' => $latestSubmission,
            'can_revise' => $latestSubmission->approval_status === 'rejected',
        ];
    }

    /**
     * Check if all submissions for a task are approved
     */
    public function areAllSubmissionsApproved(CashierTaskDefinition $taskDef): bool
    {
        $totalAssignments = $taskDef->assignments()->count();
        $approvedCount = CashierTaskSubmission::query()
            ->whereIn('task_assignment_id', function ($query) use ($taskDef) {
                $query->select('id')
                    ->from('cashier_task_assignments')
                    ->where('task_definition_id', $taskDef->id);
            })
            ->where('approval_status', 'approved')
            ->distinct('task_assignment_id')
            ->count('task_assignment_id');

        return $totalAssignments > 0 && $totalAssignments === $approvedCount;
    }

    /**
     * Get submission count by status for a task
     */
    public function getSubmissionCountByStatus(CashierTaskDefinition $taskDef): array
    {
        $submissions = CashierTaskSubmission::query()
            ->whereIn('task_assignment_id', function ($query) use ($taskDef) {
                $query->select('id')
                    ->from('cashier_task_assignments')
                    ->where('task_definition_id', $taskDef->id);
            })
            ->get()
            ->groupBy(fn ($s) => $s->approval_status);

        return [
            'pending' => $submissions->get('pending', collect())->count(),
            'approved' => $submissions->get('approved', collect())->count(),
            'rejected' => $submissions->get('rejected', collect())->count(),
            'total_with_submission' => $submissions->flatten()->count(),
            'total_assignments' => $taskDef->assignments()->count(),
        ];
    }
}
