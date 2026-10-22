<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add bill_number to direct_sales table
        Schema::table('direct_sales', function (Blueprint $table) {
            if (! Schema::hasColumn('direct_sales', 'bill_number')) {
                $table->string('bill_number', 100)->nullable()->unique()->after('sale_number');
            }
        });

        // 2. Add direct_sale_id to payments table
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'direct_sale_id')) {
                $table->foreignId('direct_sale_id')
                    ->nullable()
                    ->after('job_card_id')
                    ->constrained('direct_sales')
                    ->nullOnDelete();
            }
        });

        // 3. Backfill existing direct_sales with deterministic bill_number
        DB::statement("UPDATE direct_sales SET bill_number = CONCAT('BILL-', sale_number) WHERE bill_number IS NULL OR bill_number = ''");

        // 4. Link existing payments to direct_sales if matched via invoice_id
        DB::statement('UPDATE payments p INNER JOIN direct_sales ds ON p.invoice_id = ds.invoice_id SET p.direct_sale_id = ds.id WHERE p.direct_sale_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'direct_sale_id')) {
                $table->dropConstrainedForeignId('direct_sale_id');
            }
        });

        Schema::table('direct_sales', function (Blueprint $table) {
            if (Schema::hasColumn('direct_sales', 'bill_number')) {
                $table->dropUnique(['bill_number']);
                $table->dropColumn('bill_number');
            }
        });
    }
};
