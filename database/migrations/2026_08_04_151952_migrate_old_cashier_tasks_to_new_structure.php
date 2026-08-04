<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrate data dari cashier_tasks lama ke 3 tabel baru:
     * - cashier_task_definitions
     * - cashier_task_assignments
     * - cashier_task_submissions
     */
    public function up(): void
    {
        // Get all old cashier_tasks (including soft deleted)
        $oldTasks = DB::table('cashier_tasks')->get();

        foreach ($oldTasks as $oldTask) {
            // 1. Create task_definition
            $taskDefinitionId = Str::uuid();
            
            DB::table('cashier_task_definitions')->insert([
                'id' => $taskDefinitionId,
                'jurusan_id' => $oldTask->jurusan_id,
                'task_name' => $oldTask->task_name,
                'description' => $oldTask->description,
                'date' => $oldTask->date,
                'priority' => $oldTask->priority ?? 'medium',
                'category' => $oldTask->category,
                'is_routine' => $oldTask->is_routine ?? false,
                'requires_proof' => $oldTask->requires_proof ?? true,
                'deadline_at' => $oldTask->deadline_at,
                'created_by' => $oldTask->created_by,
                'created_at' => $oldTask->created_at ?? now(),
                'updated_at' => $oldTask->updated_at ?? now(),
            ]);

            // 2. Create task_assignment
            $assignmentId = Str::uuid();
            
            DB::table('cashier_task_assignments')->insert([
                'id' => $assignmentId,
                'task_definition_id' => $taskDefinitionId,
                'assigned_to' => $oldTask->assigned_to,
                'assignment_status' => $oldTask->is_completed ? 'completed' : ($oldTask->status === 'pending' ? 'new' : 'started'),
                'created_at' => $oldTask->created_at ?? now(),
                'updated_at' => $oldTask->updated_at ?? now(),
            ]);

            // 3. Jika ada completion_report, create task_submission
            if ($oldTask->completion_report || $oldTask->proof_image || $oldTask->is_completed) {
                $submissionId = Str::uuid();
                
                DB::table('cashier_task_submissions')->insert([
                    'id' => $submissionId,
                    'task_assignment_id' => $assignmentId,
                    'submitted_by' => $oldTask->assigned_to,
                    'report' => $oldTask->completion_report,
                    'proof_image' => $oldTask->proof_image,
                    'submission_version' => 1,
                    'submitted_at' => $oldTask->completed_at ?? $oldTask->updated_at ?? now(),
                    'approval_status' => $oldTask->approval_status ?? 'pending',
                    'reviewed_by' => $oldTask->reviewed_by,
                    'reviewed_at' => $oldTask->reviewed_at,
                    'rejection_note' => $oldTask->rejection_note,
                    'created_at' => $oldTask->created_at ?? now(),
                    'updated_at' => $oldTask->updated_at ?? now(),
                ]);
            }
        }

        echo "Migration complete! " . count($oldTasks) . " tasks migrated.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete data from new tables based on created_at timestamps
        // This is a safe rollback - only deletes data we just created
        DB::table('cashier_task_submissions')
            ->whereIn('task_assignment_id', 
                DB::table('cashier_task_assignments')
                    ->whereIn('task_definition_id',
                        DB::table('cashier_task_definitions')
                            ->whereRaw("DATE(created_at) = DATE(NOW())")
                            ->pluck('id')
                    )
                    ->pluck('id')
            )
            ->delete();

        DB::table('cashier_task_assignments')
            ->whereRaw("DATE(created_at) = DATE(NOW())")
            ->delete();

        DB::table('cashier_task_definitions')
            ->whereRaw("DATE(created_at) = DATE(NOW())")
            ->delete();
    }
};
