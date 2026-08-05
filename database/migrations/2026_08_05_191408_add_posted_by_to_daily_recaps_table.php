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
        Schema::table('daily_recaps', function (Blueprint $table) {
            $table->uuid('posted_by')->nullable()->after('cash_note');
            $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_recaps', function (Blueprint $table) {
            $table->dropForeign(['posted_by']);
            $table->dropColumn('posted_by');
        });
    }
};
