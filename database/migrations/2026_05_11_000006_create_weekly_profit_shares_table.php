<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_profit_shares', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('month_name');
            $table->integer('week_number');  // minggu ke-berapa dalam bulan
            $table->date('week_start');
            $table->date('week_end');
            $table->integer('total_profit')->default(0);  // SUM keuntungan seminggu
            $table->integer('kas_amount')->default(0);    // 50% masuk kas
            $table->integer('shared_amount')->default(0); // 50% dibagi anggota
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_profit_shares');
    }
};
