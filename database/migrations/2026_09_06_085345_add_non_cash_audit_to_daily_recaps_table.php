<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_recaps', function (Blueprint $table) {
            // Nominal transfer & QRIS yang dikonfirmasi masuk rekening/e-wallet
            $table->decimal('actual_transfer', 15, 2)->default(0)->after('actual_cash');
            $table->decimal('actual_qris', 15, 2)->default(0)->after('actual_transfer');
        });
    }

    public function down(): void
    {
        Schema::table('daily_recaps', function (Blueprint $table) {
            $table->dropColumn(['actual_transfer', 'actual_qris']);
        });
    }
};
