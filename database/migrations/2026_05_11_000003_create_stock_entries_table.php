<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->date('date');
            $table->integer('opening_stock')->default(0); // stok awal (input manual)
            $table->integer('closing_stock')->default(0); // stok sisa (otomatis = opening - terjual)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_entries');
    }
};
