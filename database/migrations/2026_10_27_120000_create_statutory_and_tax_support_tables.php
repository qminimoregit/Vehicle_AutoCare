<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Sri Lanka EPF, ETF & APIT statutory configurations and tracking.
     */
    public function up(): void
    {
        // 1. Create payroll_statutory_settings table for configurable, effective-dated rules
        if (! Schema::hasTable('payroll_statutory_settings')) {
            Schema::create('payroll_statutory_settings', function (Blueprint $table) {
                $table->id();
                $table->date('effective_from')->index();
                $table->date('effective_to')->nullable()->index();
                $table->decimal('epf_employee_rate', 5, 2)->default(8.00); // 8% Employee
                $table->decimal('epf_employer_rate', 5, 2)->default(12.00); // 12% Employer
                $table->decimal('etf_employer_rate', 5, 2)->default(3.00);  // 3% Employer ETF
                $table->string('no_pay_divisor_type', 30)->default('calendar_days'); // calendar_days, fixed_30, fixed_26, fixed_22, custom
                $table->unsignedInteger('no_pay_custom_divisor')->nullable();
                $table->decimal('overtime_normal_multiplier', 4, 2)->default(1.50);
                $table->decimal('overtime_holiday_multiplier', 4, 2)->default(2.00);
                $table->decimal('overtime_monthly_divisor_hours', 5, 2)->default(200.00); // Standard 200 hours/month divisor
                $table->boolean('apit_enabled')->default(true);
                $table->decimal('apit_monthly_relief', 12, 2)->default(150000.00); // LKR 150,000 / month (Effective April 1, 2025)
                $table->json('apit_tax_slabs')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });

            // Seed historical statutory reference setting: Pre-April 1, 2025 (YA 2023/24 - 2024/25)
            DB::table('payroll_statutory_settings')->insert([
                [
                    'effective_from' => '2023-01-01',
                    'effective_to' => '2025-03-31',
                    'epf_employee_rate' => 8.00,
                    'epf_employer_rate' => 12.00,
                    'etf_employer_rate' => 3.00,
                    'no_pay_divisor_type' => 'calendar_days',
                    'no_pay_custom_divisor' => null,
                    'overtime_normal_multiplier' => 1.50,
                    'overtime_holiday_multiplier' => 2.00,
                    'overtime_monthly_divisor_hours' => 200.00,
                    'apit_enabled' => true,
                    'apit_monthly_relief' => 100000.00,
                    'apit_tax_slabs' => json_encode([
                        ['slab' => 1, 'limit' => 41666.67, 'rate' => 6],
                        ['slab' => 2, 'limit' => 41666.67, 'rate' => 12],
                        ['slab' => 3, 'limit' => 41666.67, 'rate' => 18],
                        ['slab' => 4, 'limit' => 41666.67, 'rate' => 24],
                        ['slab' => 5, 'limit' => 41666.67, 'rate' => 30],
                        ['slab' => 6, 'limit' => null,     'rate' => 36],
                    ]),
                    'is_active' => true,
                    'notes' => 'Sri Lanka Inland Revenue Department (IRD) APIT Table 01 (Pre-April 2025, YA 2023/24 - 2024/25)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'effective_from' => '2025-04-01',
                    'effective_to' => null,
                    'epf_employee_rate' => 8.00,
                    'epf_employer_rate' => 12.00,
                    'etf_employer_rate' => 3.00,
                    'no_pay_divisor_type' => 'calendar_days',
                    'no_pay_custom_divisor' => null,
                    'overtime_normal_multiplier' => 1.50,
                    'overtime_holiday_multiplier' => 2.00,
                    'overtime_monthly_divisor_hours' => 200.00,
                    'apit_enabled' => true,
                    'apit_monthly_relief' => 150000.00,
                    'apit_tax_slabs' => json_encode([
                        ['slab' => 1, 'limit' => 83333.33, 'rate' => 6],
                        ['slab' => 2, 'limit' => 41666.67, 'rate' => 18],
                        ['slab' => 3, 'limit' => 41666.67, 'rate' => 24],
                        ['slab' => 4, 'limit' => 41666.67, 'rate' => 30],
                        ['slab' => 5, 'limit' => null,     'rate' => 36],
                    ]),
                    'is_active' => true,
                    'notes' => 'Sri Lanka Inland Revenue Department (IRD) APIT Table 01 (Effective April 1, 2025 onwards, YA 2025/26)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 2. Add statutory tracking columns to payroll_items
        if (Schema::hasTable('payroll_items')) {
            Schema::table('payroll_items', function (Blueprint $table) {
                if (! Schema::hasColumn('payroll_items', 'apit_amount')) {
                    $table->decimal('apit_amount', 10, 2)->default(0.00)->after('etf_employer_amount');
                }
                if (! Schema::hasColumn('payroll_items', 'epf_qualifying_earnings')) {
                    $table->decimal('epf_qualifying_earnings', 10, 2)->default(0.00)->after('no_pay_deduction');
                }
                if (! Schema::hasColumn('payroll_items', 'etf_qualifying_earnings')) {
                    $table->decimal('etf_qualifying_earnings', 10, 2)->default(0.00)->after('epf_qualifying_earnings');
                }
                if (! Schema::hasColumn('payroll_items', 'apit_taxable_earnings')) {
                    $table->decimal('apit_taxable_earnings', 10, 2)->default(0.00)->after('etf_qualifying_earnings');
                }
            });
        }

        // 3. Add statutory totals to payroll_periods
        if (Schema::hasTable('payroll_periods')) {
            Schema::table('payroll_periods', function (Blueprint $table) {
                if (! Schema::hasColumn('payroll_periods', 'total_epf_employee')) {
                    $table->decimal('total_epf_employee', 12, 2)->default(0.00)->after('total_bonuses');
                }
                if (! Schema::hasColumn('payroll_periods', 'total_epf_employer')) {
                    $table->decimal('total_epf_employer', 12, 2)->default(0.00)->after('total_epf_employee');
                }
                if (! Schema::hasColumn('payroll_periods', 'total_etf_employer')) {
                    $table->decimal('total_etf_employer', 12, 2)->default(0.00)->after('total_epf_employer');
                }
                if (! Schema::hasColumn('payroll_periods', 'total_apit')) {
                    $table->decimal('total_apit', 12, 2)->default(0.00)->after('total_etf_employer');
                }
            });
        }

        // 4. Add statutory EPF and Tax flags to employee_bonuses
        if (Schema::hasTable('employee_bonuses')) {
            Schema::table('employee_bonuses', function (Blueprint $table) {
                if (! Schema::hasColumn('employee_bonuses', 'is_epf_eligible')) {
                    $table->boolean('is_epf_eligible')->default(false)->after('amount');
                }
                if (! Schema::hasColumn('employee_bonuses', 'is_taxable')) {
                    $table->boolean('is_taxable')->default(true)->after('is_epf_eligible');
                }
            });
        }

        // 5. Add statutory tax attributes to employees
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (! Schema::hasColumn('employees', 'tax_file_number')) {
                    $table->string('tax_file_number', 50)->nullable()->after('epf_number');
                }
                if (! Schema::hasColumn('employees', 'primary_employment_declaration')) {
                    $table->boolean('primary_employment_declaration')->default(true)->after('tax_file_number');
                }
                if (! Schema::hasColumn('employees', 'is_resident_for_tax')) {
                    $table->boolean('is_resident_for_tax')->default(true)->after('primary_employment_declaration');
                }
                if (! Schema::hasColumn('employees', 'statutory_exemption_reason')) {
                    $table->string('statutory_exemption_reason', 255)->nullable()->after('is_resident_for_tax');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                $columns = ['tax_file_number', 'primary_employment_declaration', 'is_resident_for_tax', 'statutory_exemption_reason'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('employees', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('employee_bonuses')) {
            Schema::table('employee_bonuses', function (Blueprint $table) {
                $columns = ['is_epf_eligible', 'is_taxable'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('employee_bonuses', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('payroll_periods')) {
            Schema::table('payroll_periods', function (Blueprint $table) {
                $columns = ['total_epf_employee', 'total_epf_employer', 'total_etf_employer', 'total_apit'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('payroll_periods', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('payroll_items')) {
            Schema::table('payroll_items', function (Blueprint $table) {
                $columns = ['apit_amount', 'epf_qualifying_earnings', 'etf_qualifying_earnings', 'apit_taxable_earnings'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('payroll_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        Schema::dropIfExists('payroll_statutory_settings');
    }
};
