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
        $tables = ['transactions', 'cash_transactions', 'daily_recaps'];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'is_archived')) {
                Schema::table($table, function (Blueprint $table): void {
                    $table->dropColumn('is_archived');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('transactions', 'is_archived')) {
            Schema::table('transactions', function (Blueprint $table): void {
                $table->boolean('is_archived')->default(false);
            });
        }

        if (! Schema::hasColumn('cash_transactions', 'is_archived')) {
            Schema::table('cash_transactions', function (Blueprint $table): void {
                $table->boolean('is_archived')->default(false);
            });
        }

        if (! Schema::hasColumn('daily_recaps', 'is_archived')) {
            Schema::table('daily_recaps', function (Blueprint $table): void {
                $table->boolean('is_archived')->default(false);
            });
        }
    }
};
