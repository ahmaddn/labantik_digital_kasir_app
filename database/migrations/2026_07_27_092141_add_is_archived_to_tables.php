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
            $table->boolean('is_archived')->default(false)->after('note');
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('description');
        });

        Schema::table('daily_recaps', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('cash_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });

        Schema::table('daily_recaps', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });
    }
};
