<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_cash_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->date('date');
            // 'transfer' atau 'qris'
            $table->string('source_method', 20)->default('transfer');
            // 'income' atau 'expense'
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->string('reference')->nullable()->index();
            $table->foreignUuid('cash_category_id')->nullable()->constrained('cash_categories')->nullOnDelete();
            $table->timestamps();

            $table->index(['jurusan_id', 'date']);
            $table->index(['jurusan_id', 'source_method', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_cash_transactions');
    }
};
