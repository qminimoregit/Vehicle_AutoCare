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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number', 50)->unique()->index();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('customer_name', 150);
            $table->string('mobile_number', 50);
            $table->string('vehicle_number', 50);
            $table->string('service_name', 150)->nullable();
            $table->date('appointment_date')->index();
            $table->string('appointment_time', 20)->index();
            $table->enum('status', ['scheduled', 'confirmed', 'arrived', 'in_service', 'completed', 'cancelled', 'no_show'])->default('scheduled')->index();
            $table->text('notes')->nullable();
            $table->foreignId('job_card_id')->nullable()->constrained('job_cards')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Add appointment_id on job_cards table
        if (Schema::hasTable('job_cards') && ! Schema::hasColumn('job_cards', 'appointment_id')) {
            Schema::table('job_cards', function (Blueprint $table) {
                $table->foreignId('appointment_id')->nullable()->after('invoice_id')->constrained('appointments')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_cards') && Schema::hasColumn('job_cards', 'appointment_id')) {
            Schema::table('job_cards', function (Blueprint $table) {
                $table->dropConstrainedForeignId('appointment_id');
            });
        }

        Schema::dropIfExists('appointments');
    }
};
