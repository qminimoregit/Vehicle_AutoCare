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
        if (! Schema::hasTable('printer_settings')) {
            Schema::create('printer_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->string('workstation_name', 100)->default('Default-Workstation');
                $table->string('bill_printer', 255)->nullable();
                $table->string('job_card_printer', 255)->nullable();
                $table->string('paper_size', 50)->default('80mm');
                $table->boolean('auto_route')->default(true);
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['branch_id', 'workstation_name']);
            });
        }

        if (! Schema::hasTable('print_logs')) {
            Schema::create('print_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->string('workstation_name', 100)->nullable();
                $table->string('document_type', 50); // bill, job_card, invoice_80mm, payment_receipt, advance_receipt, selected_items, test_print
                $table->unsignedBigInteger('document_id')->nullable();
                $table->string('document_number', 100)->nullable();
                $table->string('printer_name', 255)->nullable();
                $table->string('status', 30)->default('submitted'); // submitted, printed, failed, fallback_browser, cancelled
                $table->text('error_message')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index(['document_type', 'status']);
                $table->index(['branch_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_logs');
        Schema::dropIfExists('printer_settings');
    }
};
