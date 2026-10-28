<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: PREPARED AS REQUESTED FOR FUTURE REFERENCE — DO NOT RUN IN THIS WORKFLOW.
     */
    public function up(): void
    {
        // 1. Allow optional customer_id on quotations table
        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
        });

        // 2. Allow optional customer_id and add optional direct vehicle columns on invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();

            if (! Schema::hasColumn('invoices', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->nullable()->after('customer_id')->constrained('vehicles')->nullOnDelete();
            }

            if (! Schema::hasColumn('invoices', 'vehicle_snapshot')) {
                $table->json('vehicle_snapshot')->nullable()->after('customer_snapshot');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'vehicle_id')) {
                $table->dropConstrainedForeignId('vehicle_id');
            }
            if (Schema::hasColumn('invoices', 'vehicle_snapshot')) {
                $table->dropColumn('vehicle_snapshot');
            }
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
        });
    }
};
