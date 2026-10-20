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
        // 1. Payment Receipts table for multiple invoice payments
        if (! Schema::hasTable('payment_receipts')) {
            Schema::create('payment_receipts', function (Blueprint $table) {
                $table->id();
                $table->string('receipt_number', 50)->unique()->index();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
                $table->date('receipt_date');
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->string('payment_method', 50)->default('cash');
                $table->string('reference_number', 100)->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 2. Payment Allocations table linking receipts to individual invoices
        if (! Schema::hasTable('payment_allocations')) {
            Schema::create('payment_allocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payment_receipt_id')->constrained('payment_receipts')->cascadeOnDelete();
                $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
                $table->decimal('allocated_amount', 15, 2)->default(0);
                $table->string('notes')->nullable();
                $table->timestamps();

                $table->index(['payment_receipt_id', 'invoice_id']);
            });
        }

        // 3. Add fields to invoices table for cancellation and date changes audit
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('invoices', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->after('cancellation_reason')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('invoices', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
            if (! Schema::hasColumn('invoices', 'original_invoice_date')) {
                $table->date('original_invoice_date')->nullable()->after('invoice_date');
            }
            if (! Schema::hasColumn('invoices', 'date_changed_by')) {
                $table->foreignId('date_changed_by')->nullable()->after('original_invoice_date')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('invoices', 'date_changed_at')) {
                $table->timestamp('date_changed_at')->nullable()->after('date_changed_by');
            }
            if (! Schema::hasColumn('invoices', 'date_change_reason')) {
                $table->text('date_change_reason')->nullable()->after('date_changed_at');
            }
        });

        // 4. Add fields to direct_sales table for cancellation tracking and stock reversal status
        Schema::table('direct_sales', function (Blueprint $table) {
            if (! Schema::hasColumn('direct_sales', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('direct_sales', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->after('cancellation_reason')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('direct_sales', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
            if (! Schema::hasColumn('direct_sales', 'stock_reversed')) {
                $table->boolean('stock_reversed')->default(false)->after('cancelled_at');
            }
        });

        // 5. Add fields to payments table to link to payment_receipts and support status/reversal
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'payment_receipt_id')) {
                $table->foreignId('payment_receipt_id')->nullable()->after('customer_id')->constrained('payment_receipts')->nullOnDelete();
            }
            if (! Schema::hasColumn('payments', 'status')) {
                $table->string('status', 30)->default('completed')->after('notes');
            }
            if (! Schema::hasColumn('payments', 'reversed_at')) {
                $table->timestamp('reversed_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('payments', 'reversal_reason')) {
                $table->string('reversal_reason')->nullable()->after('reversed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_receipt_id')) {
                $table->dropConstrainedForeignId('payment_receipt_id');
            }
            $table->dropColumn(['status', 'reversed_at', 'reversal_reason']);
        });

        Schema::table('direct_sales', function (Blueprint $table) {
            if (Schema::hasColumn('direct_sales', 'cancelled_by')) {
                $table->dropConstrainedForeignId('cancelled_by');
            }
            $table->dropColumn(['cancellation_reason', 'cancelled_at', 'stock_reversed']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'cancelled_by')) {
                $table->dropConstrainedForeignId('cancelled_by');
            }
            if (Schema::hasColumn('invoices', 'date_changed_by')) {
                $table->dropConstrainedForeignId('date_changed_by');
            }
            $table->dropColumn([
                'cancellation_reason',
                'cancelled_at',
                'original_invoice_date',
                'date_changed_at',
                'date_change_reason',
            ]);
        });

        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payment_receipts');
    }
};
