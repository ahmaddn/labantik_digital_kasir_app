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
        Schema::create('cashier_task_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_definition_id')
                ->constrained('cashier_task_definitions')->cascadeOnDelete();
            $table->foreignUuid('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('jurusan_id')->constrained('jurusans')->cascadeOnDelete();

            // Assignment tracking
            $table->string('assignment_status')->default('new'); // new, acknowledged, started, submitted
            $table->dateTime('acknowledged_at')->nullable();

            $table->timestamps();
            $table->unique(['task_definition_id', 'assigned_to']);
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_task_assignments');
    }
};
