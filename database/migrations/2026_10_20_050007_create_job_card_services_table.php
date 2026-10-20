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
        Schema::create('job_card_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->string('service_code', 50)->nullable();
            $table->string('service_name');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->boolean('is_price_custom')->default(false);
            $table->string('technician_name', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_services');
    }
};
