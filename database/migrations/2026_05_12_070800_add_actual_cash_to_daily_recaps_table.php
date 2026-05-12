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
        Schema::table('daily_recaps', function (Blueprint $table) {
            $table->integer('actual_cash')->default(0)->after('total_revenue_real');
            $table->text('cash_note')->nullable()->after('actual_cash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_recaps', function (Blueprint $table) {
            $table->dropColumn(['actual_cash', 'cash_note']);
        });
    }
};
