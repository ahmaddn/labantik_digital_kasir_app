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
        Schema::table('labantik_candidate_scores', function (Blueprint $table) {
            $table->integer('attitude_score')->default(0)->after('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('labantik_candidate_scores', function (Blueprint $table) {
            $table->dropColumn('attitude_score');
        });
    }
};
