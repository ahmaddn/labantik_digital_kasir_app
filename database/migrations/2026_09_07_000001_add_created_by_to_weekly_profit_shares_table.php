<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_profit_shares', function (Blueprint $table) {
            if (! Schema::hasColumn('weekly_profit_shares', 'created_by')) {
                $table->foreignUuid('created_by')->nullable()->after('shared_amount')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('weekly_profit_shares', function (Blueprint $table) {
            if (Schema::hasColumn('weekly_profit_shares', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
