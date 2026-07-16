<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreignUuid('jurusan_id')->nullable()->after('id')->constrained('jurusans')->nullOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignUuid('jurusan_id')->nullable()->after('id')->constrained('jurusans')->nullOnDelete();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignUuid('jurusan_id')->nullable()->after('id')->constrained('jurusans')->nullOnDelete();
        });

        Schema::table('daily_recaps', function (Blueprint $table) {
            $table->foreignUuid('jurusan_id')->nullable()->after('id')->constrained('jurusans')->nullOnDelete();

            // Drop unique constraint on date because recaps are now per-date per-jurusan!
            $table->dropUnique('daily_recaps_date_unique');
            $table->unique(['date', 'jurusan_id'], 'daily_recaps_date_jurusan_unique');
        });

        Schema::table('weekly_profit_shares', function (Blueprint $table) {
            $table->foreignUuid('jurusan_id')->nullable()->after('id')->constrained('jurusans')->nullOnDelete();
        });

        Schema::table('cash_categories', function (Blueprint $table) {
            $table->foreignUuid('jurusan_id')->nullable()->after('id')->constrained('jurusans')->nullOnDelete();
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->foreignUuid('jurusan_id')->nullable()->after('id')->constrained('jurusans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
            $table->dropColumn('jurusan_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
            $table->dropColumn('jurusan_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
            $table->dropColumn('jurusan_id');
        });

        Schema::table('daily_recaps', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
            $table->dropColumn('jurusan_id');
            $table->dropUnique('daily_recaps_date_jurusan_unique');
            $table->unique('date');
        });

        Schema::table('weekly_profit_shares', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
            $table->dropColumn('jurusan_id');
        });

        Schema::table('cash_categories', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
            $table->dropColumn('jurusan_id');
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
            $table->dropColumn('jurusan_id');
        });
    }
};
