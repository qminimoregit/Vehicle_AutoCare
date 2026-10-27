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
        // 1. Add additive columns to employee_advances table if missing
        Schema::table('employee_advances', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_advances', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('employee_id')->constrained('branches')->nullOnDelete();
            }
            if (! Schema::hasColumn('employee_advances', 'monthly_installment')) {
                $table->decimal('monthly_installment', 12, 2)->nullable()->after('amount');
            }
            if (! Schema::hasColumn('employee_advances', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('employee_advances', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (! Schema::hasColumn('employee_advances', 'disbursed_at')) {
                $table->date('disbursed_at')->nullable()->after('approved_at');
            }
            if (! Schema::hasColumn('employee_advances', 'disbursed_by')) {
                $table->foreignId('disbursed_by')->nullable()->after('disbursed_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('employee_advances', 'rejected_by')) {
                $table->foreignId('rejected_by')->nullable()->after('disbursed_by')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('employee_advances', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            }
            if (! Schema::hasColumn('employee_advances', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_at');
            }
            if (! Schema::hasColumn('employee_advances', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->after('rejection_reason')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('employee_advances', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
            if (! Schema::hasColumn('employee_advances', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
        });

        // 2. Create employee_advance_recoveries table for tracking partial & multi-month recoveries
        if (! Schema::hasTable('employee_advance_recoveries')) {
            Schema::create('employee_advance_recoveries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_advance_id')->constrained('employee_advances')->cascadeOnDelete();
                $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
                $table->foreignId('payroll_item_id')->nullable()->constrained('payroll_items')->nullOnDelete();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('recovery_date')->index();
                $table->decimal('amount', 12, 2);
                $table->string('status', 30)->default('recovered')->index(); // pending_payroll, recovered, reversed
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['employee_advance_id', 'status']);
                $table->index(['payroll_period_id', 'employee_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_advance_recoveries');

        Schema::table('employee_advances', function (Blueprint $table) {
            $cols = [
                'branch_id',
                'monthly_installment',
                'approved_by',
                'approved_at',
                'disbursed_at',
                'disbursed_by',
                'rejected_by',
                'rejected_at',
                'rejection_reason',
                'cancelled_by',
                'cancelled_at',
                'cancellation_reason',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('employee_advances', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
