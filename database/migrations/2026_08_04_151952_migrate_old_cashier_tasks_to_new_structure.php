<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrate data dari cashier_tasks lama ke 3 tabel baru dengan DEDUPLICATION:
     * - GROUP by (task_name, date, jurusan_id) → 1 task_definition
     * - Setiap assigned_to yang unik → 1 task_assignment
     * - Jika ada completion_report → 1 task_submission per kasir
     */
    public function up(): void
    {
        // Get all old tasks (including soft deleted)
        $oldTasks = DB::table('cashier_tasks')->get();

        // Group by (task_name, date, jurusan_id) untuk deduplicate
        $grouped = $oldTasks->groupBy(function($task) {
            return $task->task_name . '|||' . $task->date . '|||' . $task->jurusan_id;
        });

        foreach ($grouped as $groupKey => $tasksInGroup) {
            // Use first task as template
            $templateTask = $tasksInGroup->first();
            
            // 1. Create ONE task_definition untuk group ini
            $taskDefinitionId = Str::uuid();
            
            DB::table('cashier_task_definitions')->insert([
                'id' => $taskDefinitionId,
                'jurusan_id' => $templateTask->jurusan_id,
                'task_name' => $templateTask->task_name,
                'description' => $templateTask->description,
                'date' => $templateTask->date,
                'priority' => $templateTask->priority ?? 'medium',
                'category' => $templateTask->category,
                'is_routine' => $templateTask->is_routine ?? false,
                'requires_proof' => $templateTask->requires_proof ?? true,
                'deadline_at' => $templateTask->deadline_at,
                'created_by' => $templateTask->created_by,
                'created_at' => $templateTask->created_at ?? now(),
                'updated_at' => $templateTask->updated_at ?? now(),
            ]);

            // 2. Untuk SETIAP kasir yang berbeda di group ini, create assignment + submission
            foreach ($tasksInGroup as $oldTask) {
                // Create task_assignment
                $assignmentId = Str::uuid();
                
                DB::table('cashier_task_assignments')->insert([
                    'id' => $assignmentId,
                    'task_definition_id' => $taskDefinitionId,
                    'assigned_to' => $oldTask->assigned_to,
                    'jurusan_id' => $oldTask->jurusan_id,
                    'assignment_status' => $oldTask->is_completed ? 'completed' : ($oldTask->status === 'pending' ? 'new' : 'started'),
                    'created_at' => $oldTask->created_at ?? now(),
                    'updated_at' => $oldTask->updated_at ?? now(),
                ]);

                // 3. Jika ada completion_report/proof, create ONE submission per kasir
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
        }

        echo "Migration complete! " . count($grouped) . " unique tasks created.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('cashier_task_submissions')->truncate();
        DB::table('cashier_task_assignments')->truncate();
        DB::table('cashier_task_definitions')->truncate();
    }
};
