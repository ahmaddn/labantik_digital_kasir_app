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
        Schema::table('monthly_closing_records', function (Blueprint $table) {
            $table->json('carry_forward_transaction_ids')->nullable()->after('carry_forward_profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_closing_records', function (Blueprint $table) {
            $table->dropColumn('carry_forward_transaction_ids');
        });
    }
};
