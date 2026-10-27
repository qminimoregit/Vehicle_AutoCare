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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('salary_type', 20)->default('daily')->after('position')->index();
            $table->decimal('monthly_basic_salary', 12, 2)->default(0.00)->after('daily_wage');
            $table->string('payment_method', 30)->default('cash')->after('notes');
            $table->string('bank_name', 100)->nullable()->after('payment_method');
            $table->string('bank_branch', 100)->nullable()->after('bank_name');
            $table->string('bank_account_name', 150)->nullable()->after('bank_branch');
            $table->string('bank_account_number', 50)->nullable()->after('bank_account_name');
            $table->string('epf_number', 50)->nullable()->after('bank_account_number');
            $table->boolean('is_epf_eligible')->default(true)->after('epf_number');
            $table->boolean('is_etf_eligible')->default(true)->after('is_epf_eligible');
            $table->boolean('eligible_for_off_days')->default(true)->after('is_etf_eligible');
        });

        Schema::table('employee_wage_rates', function (Blueprint $table) {
            $table->string('salary_type', 20)->default('daily')->after('employee_id')->index();
            $table->decimal('monthly_basic_salary', 12, 2)->default(0.00)->after('daily_wage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_wage_rates', function (Blueprint $table) {
            $table->dropColumn(['salary_type', 'monthly_basic_salary']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'salary_type',
                'monthly_basic_salary',
                'payment_method',
                'bank_name',
                'bank_branch',
                'bank_account_name',
                'bank_account_number',
                'epf_number',
                'is_epf_eligible',
                'is_etf_eligible',
                'eligible_for_off_days',
            ]);
        });
    }
};
