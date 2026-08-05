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
        Schema::create('labantik_registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('jurusan_id')->nullable();
            $table->foreign('jurusan_id')->references('id')->on('jurusans')->onDelete('set null');
            $table->string('full_name');
            $table->string('class_name');
            $table->text('address');
            $table->string('phone_number');
            $table->string('parent_phone_number');
            $table->text('reason');
            $table->text('illness_history')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labantik_registrations');
    }
};
