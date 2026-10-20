<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Stage 7: Credit Management.
     */
    public function up(): void
    {
        Schema::create('customer_credit_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->unique()->constrained('customers')->cascadeOnDelete();
            $table->decimal('credit_limit', 15, 2)->default(0.00);
            $table->integer('payment_terms_days')->default(30);
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->string('status', 20)->default('active'); // active, suspended, closed
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_credit_accounts');
    }
};
