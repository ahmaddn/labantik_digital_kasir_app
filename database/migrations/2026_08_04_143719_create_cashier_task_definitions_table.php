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
        Schema::create('cashier_task_definitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->uuid('group_id')->index();

            // Task information (immutable after creation)
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->string('category')->nullable();
            $table->boolean('is_routine')->default(false);
            $table->boolean('requires_proof')->default(false);
            $table->dateTime('deadline_at')->nullable();

            // Metadata
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['group_id', 'jurusan_id']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_task_definitions');
    }
};
