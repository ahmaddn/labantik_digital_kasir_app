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
        Schema::table('cashier_attendances', function (Blueprint $table) {
            $table->string('clock_in_status')->nullable()->after('clock_in'); // on_time, late
            $table->string('clock_out_status')->nullable()->after('clock_out'); // on_time, early_checkout, overtime
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashier_attendances', function (Blueprint $table) {
            $table->dropColumn(['clock_in_status', 'clock_out_status']);
        });
    }
};
