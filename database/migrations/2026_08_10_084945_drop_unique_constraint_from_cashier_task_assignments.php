<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashier_task_assignments', function (Blueprint $table) {
            $table->index('task_definition_id');
            $table->dropUnique(['task_definition_id', 'assigned_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashier_task_assignments', function (Blueprint $table) {
            $table->unique(['task_definition_id', 'assigned_to']);
        });
    }
};
