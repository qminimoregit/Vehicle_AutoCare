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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('company_settings')->cascadeOnDelete();
            $table->string('branch_code', 50);
            $table->string('branch_name', 150);
            $table->text('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_main_branch')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'branch_code']);
        });

        // Seed default main branch from existing company settings if present
        $company = DB::table('company_settings')->first();
        if ($company) {
            DB::table('branches')->insert([
                'company_id' => $company->id,
                'branch_code' => 'BR-001',
                'branch_name' => $company->company_name.' - Main Branch',
                'address' => $company->address ?? 'Main Workshop',
                'phone' => $company->phone,
                'email' => $company->email,
                'manager_id' => null,
                'is_main_branch' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
