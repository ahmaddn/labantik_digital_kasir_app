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
        Schema::create('cashier_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->foreignUuid('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_tasks');
    }
};
