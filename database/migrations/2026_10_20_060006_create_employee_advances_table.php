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
        Schema::create('employee_advances', function (Blueprint $table) {
            $table->id();
            $table->string('advance_number', 50)->unique()->index();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('advance_date')->index();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50)->default('cash');
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('pending')->index(); // pending, deducted, cancelled
            $table->foreignId('payroll_item_id')->nullable()->constrained('payroll_items')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_advances');
    }
};
