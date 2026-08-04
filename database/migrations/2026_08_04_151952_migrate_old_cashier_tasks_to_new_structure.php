<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrate data dari cashier_tasks lama ke 3 tabel baru dengan grouping:
     * - GROUP by (task_name, date) → 1 task_definition
     * - Setiap assigned_to yang unik → 1 task_assignment
     * - Jika ada completion_report → 1 task_submission per kasir
     */
    public function up(): void
    {
        // Map untuk track task_definition yang sudah dibuat: key = "task_name|date|jurusan_id"
        $taskDefMap = [];

        // Group old tasks by (task_name, date, jurusan_id)
        $groupedTasks = DB::table('cashier_tasks')
            ->get()
            ->groupBy(function($task) {
                return $task->task_name . '|' . $task->date . '|' . $task->jurusan_id;
            });

        foreach ($groupedTasks as $groupKey => $tasksGroup) {
            // Get sample task dari group (semua field sama kecuali assigned_to)
            $sampleTask = $tasksGroup->first();
            
            // 1. Create task_definition (sekali per group)
            $taskDefinitionId = Str::uuid();
            
            DB::table('cashier_task_definitions')->insert([
                'id' => $taskDefinitionId,
                'jurusan_id' => $sampleTask->jurusan_id,
                'task_name' => $sampleTask->task_name,
                'description' => $sampleTask->description,
                'date' => $sampleTask->date,
                'priority' => $sampleTask->priority ?? 'medium',
                'category' => $sampleTask->category,
                'is_routine' => $sampleTask->is_routine ?? false,
                'requires_proof' => $sampleTask->requires_proof ?? true,
                'deadline_at' => $sampleTask->deadline_at,
                'created_by' => $sampleTask->created_by,
                'created_at' => $sampleTask->created_at ?? now(),
                'updated_at' => $sampleTask->updated_at ?? now(),
            ]);

            // 2. Untuk setiap kasir yang berbeda di group ini, create assignment & submission
            foreach ($tasksGroup as $oldTask) {
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
        }

        echo "Migration complete! " . count($groupedTasks) . " unique tasks created with multiple kasir assignments.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete all migrated data
        DB::table('cashier_task_submissions')->delete();
        DB::table('cashier_task_assignments')->delete();
        DB::table('cashier_task_definitions')->delete();
    }
};
