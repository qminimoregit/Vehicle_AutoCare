<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Target module tables that require branch tracking.
     *
     * @var array<int, string>
     */
    protected array $tables = [
        'vehicles',
        'job_cards',
        'direct_sales',
        'goods_received_notes',
        'stock_batches',
        'stock_movements',
        'employees',
        'expenses',
        'invoices',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add nullable branch_id column to each module table
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
                });
            }
        }

        // 2. Safe backward-compatible backfill: associate existing transactions with the main branch
        $mainBranch = DB::table('branches')->where('is_main_branch', true)->first();
        if ($mainBranch) {
            foreach ($this->tables as $tableName) {
                if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'branch_id')) {
                    DB::table($tableName)->whereNull('branch_id')->update(['branch_id' => $mainBranch->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('branch_id');
                });
            }
        }
    }
};
