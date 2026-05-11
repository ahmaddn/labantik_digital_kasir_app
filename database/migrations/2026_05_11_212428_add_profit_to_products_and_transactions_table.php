<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('profit')->default(0)->after('modal_price');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('unit_profit')->default(0)->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('profit');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('unit_profit');
        });
    }
};
