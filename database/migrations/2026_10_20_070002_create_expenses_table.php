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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number', 50)->unique()->index();
            $table->foreignId('category_id')->constrained('expense_categories')->restrictOnDelete();
            $table->string('expense_type', 30)->index(); // staff, shop
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('employee_advance_id')->nullable()->constrained('employee_advances')->nullOnDelete();
            $table->date('expense_date')->index();
            $table->string('related_month', 20)->index(); // e.g. 2026-10 or October 2026
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50)->default('cash');
            $table->date('payment_date')->nullable()->index();
            $table->string('payment_reference', 100)->nullable();
            $table->string('receipt_path', 255)->nullable();
            $table->string('status', 30)->default('active')->index(); // active, voided
            $table->string('void_reason', 255)->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
