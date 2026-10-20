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
        Schema::create('job_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->text('description');
            $table->text('recommended_repair');
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending_approval', 'approved', 'declined', 'fixed', 'deferred'])->default('pending_approval')->index();
            $table->enum('approval_method', ['verbal', 'phone', 'written', 'whatsapp'])->nullable();
            $table->string('approval_contact', 100)->nullable();
            $table->text('approval_note')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supplementary_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_issues');
    }
};
