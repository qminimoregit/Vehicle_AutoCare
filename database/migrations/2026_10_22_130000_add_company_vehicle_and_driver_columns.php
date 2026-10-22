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
        // 1. Add driver and brand/model columns to vehicles table if missing
        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'brand')) {
                $table->string('brand', 100)->nullable()->after('make_model');
            }
            if (! Schema::hasColumn('vehicles', 'model')) {
                $table->string('model', 100)->nullable()->after('brand');
            }
            if (! Schema::hasColumn('vehicles', 'driver_name')) {
                $table->string('driver_name', 150)->nullable()->after('engine_number');
            }
            if (! Schema::hasColumn('vehicles', 'driver_mobile')) {
                $table->string('driver_mobile', 50)->nullable()->after('driver_name');
            }
        });

        // 2. Add driver columns to job_cards table if missing
        Schema::table('job_cards', function (Blueprint $table) {
            if (! Schema::hasColumn('job_cards', 'driver_name')) {
                $table->string('driver_name', 150)->nullable()->after('assigned_to');
            }
            if (! Schema::hasColumn('job_cards', 'driver_mobile')) {
                $table->string('driver_mobile', 50)->nullable()->after('driver_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'driver_mobile')) {
                $table->dropColumn('driver_mobile');
            }
            if (Schema::hasColumn('vehicles', 'driver_name')) {
                $table->dropColumn('driver_name');
            }
            if (Schema::hasColumn('vehicles', 'model')) {
                $table->dropColumn('model');
            }
            if (Schema::hasColumn('vehicles', 'brand')) {
                $table->dropColumn('brand');
            }
        });

        Schema::table('job_cards', function (Blueprint $table) {
            if (Schema::hasColumn('job_cards', 'driver_mobile')) {
                $table->dropColumn('driver_mobile');
            }
            if (Schema::hasColumn('job_cards', 'driver_name')) {
                $table->dropColumn('driver_name');
            }
        });
    }
};
