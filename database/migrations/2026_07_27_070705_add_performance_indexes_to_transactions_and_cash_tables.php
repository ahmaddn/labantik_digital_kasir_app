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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('transacted_at');
            $table->index('status');
            $table->index(['jurusan_id', 'status', 'transacted_at']);
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->index('date');
            $table->index(['jurusan_id', 'cash_category_id', 'cash_type', 'type'], 'cash_tx_perf_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['transacted_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['jurusan_id', 'status', 'transacted_at']);
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex('cash_tx_perf_index');
        });
    }
};
