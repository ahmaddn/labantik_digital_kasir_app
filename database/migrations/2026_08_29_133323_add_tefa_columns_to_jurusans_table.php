<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurusans', function (Blueprint $table) {
            $table->string('pic_name')->nullable()->after('name');
            $table->string('phone')->nullable()->after('pic_name');
            $table->string('stand_location')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('stand_location');
        });
    }

    public function down(): void
    {
        Schema::table('jurusans', function (Blueprint $table) {
            $table->dropColumn(['pic_name', 'phone', 'stand_location', 'is_active']);
        });
    }
};
