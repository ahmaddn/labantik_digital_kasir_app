<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cashier_task_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_assignment_id')
                ->constrained('cashier_task_assignments')->cascadeOnDelete();
            $table->foreignUuid('submitted_by')->constrained('users')->cascadeOnDelete();

            // Report data
            $table->text('report')->nullable();
            $table->string('proof_image')->nullable();
            $table->dateTime('submitted_at');

            // Review/Approval (INDEPENDENT per submission)
            $table->string('approval_status')->default('pending'); // pending, approved, rejected
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->text('rejection_note')->nullable();
            $table->dateTime('reviewed_at')->nullable();

            // Submission version for tracking revisions
            $table->integer('submission_version')->default(1);

            $table->timestamps();
            $table->index('task_assignment_id');
            $table->index('submitted_by');
            $table->index('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_task_submissions');
    }
};
