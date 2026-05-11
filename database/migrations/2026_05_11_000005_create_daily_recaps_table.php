<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_recaps', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();  // 1 baris per hari
            $table->integer('month_week');   // minggu ke-berapa dalam bulan (1–5)
            $table->string('month_name');    // contoh: "Januari"
            $table->integer('total_revenue_real')->default(0);  // uang tunai masuk (status uang_diterima)
            $table->integer('total_revenue_all')->default(0);   // semua transaksi termasuk hutang
            $table->integer('total_profit')->default(0);        // SUM (unit_price - modal_price) × qty
            $table->integer('total_modal')->default(0);         // SUM modal_price × qty
            $table->integer('count_received')->default(0);      // jumlah transaksi uang_diterima
            $table->integer('count_unpaid_change')->default(0); // jumlah transaksi belum_kembalian
            $table->integer('count_no_payment')->default(0);    // jumlah transaksi belum_menerima_uang
            $table->integer('count_borrowed')->default(0);      // jumlah transaksi uang_dipinjam
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_recaps');
    }
};
