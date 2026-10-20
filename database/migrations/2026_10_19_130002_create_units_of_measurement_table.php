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
        Schema::create('units_of_measurement', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed initial standard units of measurement safely
        $initialUnits = [
            ['name' => 'Bottle', 'code' => 'BTL'],
            ['name' => 'Can', 'code' => 'CAN'],
            ['name' => 'Packet', 'code' => 'PKT'],
            ['name' => 'Item', 'code' => 'ITM'],
            ['name' => 'Loose', 'code' => 'LSE'],
            ['name' => 'Tube', 'code' => 'TUB'],
        ];

        $now = now();
        foreach ($initialUnits as $unit) {
            DB::table('units_of_measurement')->updateOrInsert(
                ['name' => $unit['name']],
                [
                    'code' => $unit['code'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units_of_measurement');
    }
};
