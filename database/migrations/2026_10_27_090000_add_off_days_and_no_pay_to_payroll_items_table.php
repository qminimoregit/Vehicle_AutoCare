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
        Schema::table('payroll_items', function (Blueprint $table) {
            if (! Schema::hasColumn('payroll_items', 'salary_type_snapshot')) {
                $table->string('salary_type_snapshot', 20)->default('daily')->after('daily_wage_snapshot');
            }
            if (! Schema::hasColumn('payroll_items', 'monthly_basic_snapshot')) {
                $table->decimal('monthly_basic_snapshot', 12, 2)->default(0.00)->after('salary_type_snapshot');
            }
            if (! Schema::hasColumn('payroll_items', 'company_off_days_count')) {
                $table->decimal('company_off_days_count', 4, 2)->default(0.00)->after('day_off_days_count');
            }
            if (! Schema::hasColumn('payroll_items', 'paid_holidays_count')) {
                $table->decimal('paid_holidays_count', 4, 2)->default(0.00)->after('company_off_days_count');
            }
            if (! Schema::hasColumn('payroll_items', 'approved_leave_count')) {
                $table->decimal('approved_leave_count', 4, 2)->default(0.00)->after('paid_holidays_count');
            }
            if (! Schema::hasColumn('payroll_items', 'no_pay_days_count')) {
                $table->decimal('no_pay_days_count', 4, 2)->default(0.00)->after('approved_leave_count');
            }
            if (! Schema::hasColumn('payroll_items', 'no_pay_deduction')) {
                $table->decimal('no_pay_deduction', 12, 2)->default(0.00)->after('deductions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach ([
                'salary_type_snapshot',
                'monthly_basic_snapshot',
                'company_off_days_count',
                'paid_holidays_count',
                'approved_leave_count',
                'no_pay_days_count',
                'no_pay_deduction',
            ] as $column) {
                if (Schema::hasColumn('payroll_items', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
