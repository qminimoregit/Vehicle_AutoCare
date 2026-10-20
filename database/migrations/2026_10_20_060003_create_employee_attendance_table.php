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
        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('attendance_date')->index();
            $table->string('status', 30)->index(); // full_day, half_day, absent, leave, day_off
            $table->decimal('payable_day_fraction', 4, 2)->default(1.00); // 1.00, 0.50, 0.00
            $table->decimal('applied_daily_wage', 12, 2);
            $table->decimal('daily_earnings', 12, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date'], 'unique_emp_attendance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendance');
    }
};
