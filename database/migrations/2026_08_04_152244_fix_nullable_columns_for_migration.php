<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix nullable columns that are blocking data migration
     */
    public function up(): void
    {
        // Make jurusan_id nullable in cashier_task_assignments
        // (jurusan can be derived from task_definition, doesn't need to be stored)
        DB::statement('ALTER TABLE cashier_task_assignments MODIFY jurusan_id CHAR(36) NULL');
        
        echo "Fixed nullable columns. Now safe to run data migration.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't revert - we need these columns nullable for data integrity
    }
};
