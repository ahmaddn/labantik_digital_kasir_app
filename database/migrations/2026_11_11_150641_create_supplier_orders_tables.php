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
        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id')->nullable();
            $table->string('supplier_name');
            $table->string('supplier_phone');
            $table->enum('status', ['pending', 'ordered', 'received', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedInteger('total_items')->default(0);
            $table->uuid('created_by')->nullable();
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('supplier_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_order_id');
            $table->uuid('product_id')->nullable();
            $table->string('product_name');
            $table->integer('quantity')->default(1);
            $table->enum('unit', ['pcs', 'pack', 'dus'])->default('pcs');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('supplier_order_id')->references('id')->on('supplier_orders')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_order_items');
        Schema::dropIfExists('supplier_orders');
    }
};
