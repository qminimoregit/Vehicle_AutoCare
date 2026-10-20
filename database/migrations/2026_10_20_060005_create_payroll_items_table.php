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
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('employee_name_snapshot', 150);
            $table->decimal('daily_wage_snapshot', 12, 2);
            $table->unsignedSmallInteger('full_days_count')->default(0);
            $table->unsignedSmallInteger('half_days_count')->default(0);
            $table->unsignedSmallInteger('absent_days_count')->default(0);
            $table->unsignedSmallInteger('leave_days_count')->default(0);
            $table->unsignedSmallInteger('day_off_days_count')->default(0);
            $table->decimal('payable_days', 5, 2)->default(0.00);
            $table->decimal('base_earnings', 12, 2)->default(0.00);
            $table->decimal('overtime_amount', 12, 2)->default(0.00);
            $table->decimal('bonus_amount', 12, 2)->default(0.00);
            $table->decimal('deductions', 12, 2)->default(0.00);
            $table->decimal('epf_employee_amount', 12, 2)->default(0.00);
            $table->decimal('epf_employer_amount', 12, 2)->default(0.00);
            $table->decimal('etf_employer_amount', 12, 2)->default(0.00);
            $table->decimal('advances_applied', 12, 2)->default(0.00);
            $table->decimal('gross_salary', 12, 2)->default(0.00);
            $table->decimal('net_salary', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('balance_due', 12, 2)->default(0.00);
            $table->string('status', 30)->default('draft')->index(); // draft, approved, partially_paid, paid
            $table->json('attendance_breakdown')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id'], 'unique_period_employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
