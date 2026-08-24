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
        Schema::create('modifiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('modifier_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->string('name');
            $table->integer('min_selection')->default(0);
            $table->integer('max_selection')->default(1);
            $table->timestamps();
        });

        Schema::create('modifier_modifier_group', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('modifier_id')->constrained('modifiers')->cascadeOnDelete();
            $table->foreignUuid('modifier_group_id')->constrained('modifier_groups')->cascadeOnDelete();
        });

        Schema::create('product_modifier_group', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('modifier_group_id')->constrained('modifier_groups')->cascadeOnDelete();
        });

        Schema::create('transaction_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignUuid('modifier_id')->constrained('modifiers')->cascadeOnDelete();
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_modifiers');
        Schema::dropIfExists('product_modifier_group');
        Schema::dropIfExists('modifier_modifier_group');
        Schema::dropIfExists('modifier_groups');
        Schema::dropIfExists('modifiers');
    }
};
