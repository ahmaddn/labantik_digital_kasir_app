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
        Schema::table('documentation_schedules', function (Blueprint $table) {
            $table->unsignedTinyInteger('shift')->default(1)->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentation_schedules', function (Blueprint $table) {
            $table->dropColumn('shift');
        });
    }
};
