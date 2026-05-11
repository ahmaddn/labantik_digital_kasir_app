<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories');
            $table->string('name');        // contoh: "Kerupuk"
            $table->string('label');       // contoh: "Kerupuk - Rp1000" (untuk dropdown)
            $table->integer('price');      // harga jual, contoh: 1000
            $table->integer('modal_price'); // harga modal, contoh: 200
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
