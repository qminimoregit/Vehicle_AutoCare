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
        // 1. Add enable_carpets boolean flag to vehicle_types table
        if (! Schema::hasColumn('vehicle_types', 'enable_carpets')) {
            Schema::table('vehicle_types', function (Blueprint $table) {
                $table->boolean('enable_carpets')->default(false)->after('image');
            });
        }

        // 2. Create carpet_categories table for shared reusable carpet categories
        if (! Schema::hasTable('carpet_categories')) {
            Schema::create('carpet_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('status', 20)->default('active');
                $table->timestamps();
            });

            // Seed standard default carpet categories
            $now = now();
            DB::table('carpet_categories')->insert([
                ['name' => 'Front Carpet', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Rear Carpet', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Boot Carpet', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        // 3. Create pivot table mapping vehicle types to applicable carpet categories
        if (! Schema::hasTable('carpet_category_vehicle_type')) {
            Schema::create('carpet_category_vehicle_type', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->cascadeOnDelete();
                $table->foreignId('carpet_category_id')->constrained('carpet_categories')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['vehicle_type_id', 'carpet_category_id'], 'vtype_carpet_cat_unique');
            });

            // Map default categories to Car, SUV, Van if they exist
            $catIds = DB::table('carpet_categories')->pluck('id')->toArray();
            $carpetVehicleTypeIds = DB::table('vehicle_types')
                ->whereIn('name', ['Car', 'SUV', 'Van'])
                ->pluck('id')
                ->toArray();

            if (! empty($carpetVehicleTypeIds) && ! empty($catIds)) {
                // Enable carpets for Car, SUV, Van
                DB::table('vehicle_types')
                    ->whereIn('id', $carpetVehicleTypeIds)
                    ->update(['enable_carpets' => true]);

                $pivotInserts = [];
                $now = now();
                foreach ($carpetVehicleTypeIds as $vtId) {
                    foreach ($catIds as $catId) {
                        $pivotInserts[] = [
                            'vehicle_type_id' => $vtId,
                            'carpet_category_id' => $catId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
                DB::table('carpet_category_vehicle_type')->insertOrIgnore($pivotInserts);
            }
        }

        // 4. Create job_card_carpets table for visit-specific carpet operational records
        if (! Schema::hasTable('job_card_carpets')) {
            Schema::create('job_card_carpets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_card_id')->constrained('job_cards')->cascadeOnDelete();
                $table->foreignId('carpet_category_id')->nullable()->constrained('carpet_categories')->nullOnDelete();
                $table->string('category_name', 100);
                $table->unsignedSmallInteger('quantity')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_carpets');
        Schema::dropIfExists('carpet_category_vehicle_type');
        Schema::dropIfExists('carpet_categories');

        if (Schema::hasColumn('vehicle_types', 'enable_carpets')) {
            Schema::table('vehicle_types', function (Blueprint $table) {
                $table->dropColumn('enable_carpets');
            });
        }
    }
};
