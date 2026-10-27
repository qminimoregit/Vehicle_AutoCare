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
        // 1. Employee Bonuses table
        if (! Schema::hasTable('employee_bonuses')) {
            Schema::create('employee_bonuses', function (Blueprint $table) {
                $table->id();
                $table->string('bonus_number', 50)->unique();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->string('bonus_type', 50)->default('performance'); // performance, attendance, special, festival, incentive, other
                $table->string('calculation_type', 30)->default('fixed'); // fixed, percentage
                $table->decimal('percentage_rate', 5, 2)->nullable();
                $table->decimal('amount', 12, 2);
                $table->smallInteger('period_year')->index();
                $table->tinyInteger('period_month')->index();
                $table->date('effective_date')->nullable();
                $table->string('status', 30)->default('pending')->index(); // pending, approved, rejected, applied, cancelled
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('rejected_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('cancelled_at')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->foreignId('payroll_item_id')->nullable()->constrained('payroll_items')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['employee_id', 'period_year', 'period_month']);
                $table->index(['status', 'period_year', 'period_month']);
            });
        }

        // 2. Employee Allowances table
        if (! Schema::hasTable('employee_allowances')) {
            Schema::create('employee_allowances', function (Blueprint $table) {
                $table->id();
                $table->string('allowance_number', 50)->unique();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->string('allowance_type', 50)->default('transport'); // transport, meal, special, other
                $table->string('title', 150)->nullable();
                $table->decimal('amount', 12, 2);
                $table->boolean('is_recurring')->default(true);
                $table->date('effective_from')->index();
                $table->date('effective_to')->nullable();
                $table->smallInteger('period_year')->nullable();
                $table->tinyInteger('period_month')->nullable();
                $table->boolean('is_epf_eligible')->default(false);
                $table->boolean('is_etf_eligible')->default(false);
                $table->boolean('is_taxable')->default(false);
                $table->string('status', 30)->default('active')->index(); // pending, approved, active, paused, cancelled
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('cancelled_at')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['employee_id', 'status']);
            });
        }

        // 3. Employee Overtime table
        if (! Schema::hasTable('employee_overtimes')) {
            Schema::create('employee_overtimes', function (Blueprint $table) {
                $table->id();
                $table->string('overtime_number', 50)->unique();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->date('overtime_date')->index();
                $table->decimal('hours', 6, 2);
                $table->decimal('hourly_rate', 10, 2);
                $table->decimal('rate_multiplier', 4, 2)->default(1.50);
                $table->decimal('amount', 12, 2);
                $table->string('status', 30)->default('pending')->index(); // pending, approved, rejected, applied, cancelled
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('rejected_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('cancelled_at')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->foreignId('payroll_item_id')->nullable()->constrained('payroll_items')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['employee_id', 'overtime_date', 'status'], 'emp_ot_date_status_unique');
                $table->index(['employee_id', 'overtime_date']);
            });
        }

        // 4. Add allowances_amount to payroll_items if missing
        if (! Schema::hasColumn('payroll_items', 'allowances_amount')) {
            Schema::table('payroll_items', function (Blueprint $table) {
                $table->decimal('allowances_amount', 12, 2)->default(0.00)->after('bonus_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('payroll_items', 'allowances_amount')) {
            Schema::table('payroll_items', function (Blueprint $table) {
                $table->dropColumn('allowances_amount');
            });
        }

        Schema::dropIfExists('employee_overtimes');
        Schema::dropIfExists('employee_allowances');
        Schema::dropIfExists('employee_bonuses');
    }
};
