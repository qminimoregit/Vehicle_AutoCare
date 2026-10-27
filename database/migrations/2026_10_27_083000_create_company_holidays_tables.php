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
        if (! Schema::hasTable('company_holidays')) {
            Schema::create('company_holidays', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->string('name', 150);
                $table->date('start_date')->index();
                $table->date('end_date')->index();
                $table->string('duration', 20)->default('full_day'); // full_day, half_day
                $table->string('holiday_type', 20)->default('paid'); // paid, unpaid
                $table->boolean('applies_to_all')->default(true);
                $table->string('status', 20)->default('active')->index(); // active, cancelled
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['branch_id', 'start_date', 'status']);
            });
        }

        if (! Schema::hasTable('company_holiday_employees')) {
            Schema::create('company_holiday_employees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_holiday_id')->constrained('company_holidays')->cascadeOnDelete();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['company_holiday_id', 'employee_id'], 'unique_holiday_employee');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_holiday_employees');
        Schema::dropIfExists('company_holidays');
    }
};
