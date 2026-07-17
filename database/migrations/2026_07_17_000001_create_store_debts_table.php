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
        Schema::create('store_debts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('creditor_name');
            $table->integer('amount');
            $table->integer('remaining_amount');
            $table->string('status')->default('belum_lunas'); // belum_lunas, lunas
            $table->text('note')->nullable();
            $table->date('due_date')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_debts');
    }
};
