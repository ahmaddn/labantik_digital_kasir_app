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
        Schema::dropIfExists('monthly_closing_records');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('monthly_closing_records', function (Blueprint $table) {
            $table->id();
            $table->string('jurusan_id');
            $table->date('month');
            $table->decimal('starting_cash', 16, 2);
            $table->decimal('ending_cash', 16, 2);
            $table->decimal('carry_forward_amount', 16, 2)->nullable();
            $table->string('carry_forward_transaction_id')->nullable();
            $table->string('carry_forward_change_transaction_id')->nullable();
            $table->timestamps();
        });
    }
};
