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
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'quotation_id')) {
                $table->foreignId('quotation_id')->nullable()->after('customer_id')->constrained('quotations')->nullOnDelete();
            }
            if (! Schema::hasColumn('invoices', 'discount_type')) {
                $table->enum('discount_type', ['none', 'percentage', 'fixed'])->default('none')->after('discount_amount');
            }
            if (! Schema::hasColumn('invoices', 'discount_value')) {
                $table->decimal('discount_value', 15, 2)->default(0)->after('discount_type');
            }
            if (! Schema::hasColumn('invoices', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->default(0)->after('tax_amount');
            }
            if (! Schema::hasColumn('invoices', 'additional_charges')) {
                $table->decimal('additional_charges', 15, 2)->default(0)->after('tax_percentage');
            }
            if (! Schema::hasColumn('invoices', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->after('notes');
            }
            // Modify invoice_type to allow 'quotation' and 'manual'
            $table->string('invoice_type', 50)->default('job_card')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'quotation_id')) {
                $table->dropConstrainedForeignId('quotation_id');
            }
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'tax_percentage',
                'additional_charges',
                'terms_conditions',
            ]);
        });
    }
};
