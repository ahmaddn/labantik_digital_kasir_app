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
        Schema::table('labantik_registrations', function (Blueprint $table) {
            $table->boolean('is_joined_group')->default(false)->after('illness_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('labantik_registrations', function (Blueprint $table) {
            $table->dropColumn('is_joined_group');
        });
    }
};
