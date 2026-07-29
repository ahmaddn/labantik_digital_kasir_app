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
        Schema::create('cashier_attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cashier_schedule_id')->nullable()->constrained('cashier_schedules')->nullOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->date('date');
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('closing_cash', 15, 2)->default(0);
            $table->text('closing_report')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, present, absent, late
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_attendances');
    }
};
