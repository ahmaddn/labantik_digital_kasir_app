<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products');
            $table->dateTime('transacted_at');
            $table->string('buyer_name')->nullable(); // boleh kosong, 95% anonim
            $table->integer('quantity');
            $table->integer('unit_price');   // snapshot harga saat transaksi
            $table->integer('total_price');  // = unit_price × quantity, otomatis
            $table->enum('status', ['uang_diterima', 'belum_kembalian', 'belum_menerima_uang', 'uang_dipinjam']);
            $table->string('note')->nullable(); // nominal kembalian, contoh: "1k", "2500"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
