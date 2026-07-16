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
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->foreignUuid('cash_category_id')->nullable()->constrained('cash_categories')->nullOnDelete();
            if (Schema::hasColumn('cash_transactions', 'name')) {
                $table->dropColumn('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropForeign(['cash_category_id']);
            $table->dropColumn('cash_category_id');
            $table->string('name')->nullable();
        });
    }
};
