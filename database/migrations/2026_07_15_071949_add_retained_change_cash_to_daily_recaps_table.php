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
            $table->integer('retained_change_cash')->default(0)->after('actual_cash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_recaps', function (Blueprint $table) {
            $table->dropColumn('retained_change_cash');
        });
    }
};
