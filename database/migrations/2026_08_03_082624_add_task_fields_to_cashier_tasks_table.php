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
        Schema::table('cashier_tasks', function (Blueprint $table) {
            $table->string('status')->default('new')->after('date');
            $table->string('priority')->default('medium')->after('status');
            $table->string('category')->nullable()->after('priority');
            $table->boolean('is_routine')->default(false)->after('category');
            $table->timestamp('deadline_at')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashier_tasks', function (Blueprint $table) {
            $table->dropColumn(['status', 'priority', 'category', 'is_routine', 'deadline_at']);
        });
    }
};
