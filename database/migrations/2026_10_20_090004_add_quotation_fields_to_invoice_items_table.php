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
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'source_quotation_item_id')) {
                $table->foreignId('source_quotation_item_id')->nullable()->after('invoice_id')->constrained('quotation_items')->nullOnDelete();
            }
            if (!Schema::hasColumn('invoice_items', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('source_quotation_item_id');
            }
            if (!Schema::hasColumn('invoice_items', 'description')) {
                $table->text('description')->nullable()->after('item_name');
            }
            if (!Schema::hasColumn('invoice_items', 'unit')) {
                $table->string('unit', 50)->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('invoice_items', 'discount_type')) {
                $table->enum('discount_type', ['none', 'percentage', 'fixed'])->default('none')->after('discount_amount');
            }
            if (!Schema::hasColumn('invoice_items', 'discount_value')) {
                $table->decimal('discount_value', 15, 2)->default(0)->after('discount_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'source_quotation_item_id')) {
                $table->dropConstrainedForeignId('source_quotation_item_id');
            }
            $table->dropColumn([
                'sort_order',
                'description',
                'unit',
                'discount_type',
                'discount_value',
            ]);
        });
    }
};
