<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add payment tracking columns to expenses table
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0.00)->after('amount');
            }
            if (! Schema::hasColumn('expenses', 'balance_amount')) {
                $table->decimal('balance_amount', 12, 2)->default(0.00)->after('paid_amount');
            }
            if (! Schema::hasColumn('expenses', 'payment_status')) {
                $table->string('payment_status', 30)->default('paid')->after('balance_amount')->index();
            }
        });

        // 2. Create expense_payments table for tracking payment installments & history
        if (! Schema::hasTable('expense_payments')) {
            Schema::create('expense_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('expense_id')->constrained('expenses')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->string('payment_number', 50)->unique()->index();
                $table->date('payment_date')->index();
                $table->decimal('amount', 12, 2);
                $table->string('payment_method', 50)->default('cash');
                $table->string('reference_number', 100)->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 3. Backfill existing expenses
        $existingExpenses = DB::table('expenses')->get();
        foreach ($existingExpenses as $exp) {
            // Update expenses record
            DB::table('expenses')->where('id', $exp->id)->update([
                'paid_amount' => $exp->amount,
                'balance_amount' => 0.00,
                'payment_status' => 'paid',
            ]);

            // Create initial payment record if none exists
            $hasPayment = DB::table('expense_payments')->where('expense_id', $exp->id)->exists();
            if (! $hasPayment && (float) $exp->amount > 0) {
                $payNumber = 'EPAY-INIT-' . str_pad((string) $exp->id, 5, '0', STR_PAD_LEFT);
                DB::table('expense_payments')->insert([
                    'expense_id' => $exp->id,
                    'branch_id' => $exp->branch_id,
                    'payment_number' => $payNumber,
                    'payment_date' => $exp->payment_date ?? $exp->expense_date,
                    'amount' => $exp->amount,
                    'payment_method' => $exp->payment_method ?? 'cash',
                    'reference_number' => $exp->payment_reference,
                    'notes' => 'Initial payment recorded with expense',
                    'created_by' => $exp->created_by,
                    'created_at' => $exp->created_at ?? now(),
                    'updated_at' => $exp->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_payments');

        Schema::table('expenses', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('expenses', 'paid_amount')) {
                $dropCols[] = 'paid_amount';
            }
            if (Schema::hasColumn('expenses', 'balance_amount')) {
                $dropCols[] = 'balance_amount';
            }
            if (Schema::hasColumn('expenses', 'payment_status')) {
                $dropCols[] = 'payment_status';
            }
            if (! empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
