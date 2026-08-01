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
        Schema::create('monthly_closing_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->string('period', 7);
            $table->json('pending_points_snapshot')->nullable();
            $table->integer('carry_forward_modal')->default(0);
            $table->integer('carry_forward_profit')->default(0);
            $table->timestamp('closed_at');
            $table->timestamps();

            $table->unique(['jurusan_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_closing_records');
    }
};
