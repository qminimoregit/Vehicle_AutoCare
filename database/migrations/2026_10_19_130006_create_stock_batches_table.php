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
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->foreignId('grn_id')->constrained('goods_received_notes')->restrictOnDelete();
            $table->foreignId('grn_item_id')->constrained('goods_received_note_items')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->date('purchase_date');
            $table->string('supplier_lot_number')->nullable();
            $table->decimal('received_quantity', 10, 2);
            $table->decimal('free_quantity', 10, 2)->default(0.00);
            $table->decimal('remaining_quantity', 10, 2);
            $table->decimal('original_purchase_unit_price', 12, 2);
            $table->decimal('inventory_unit_cost', 12, 4);
            $table->decimal('selling_price_at_receipt', 12, 2)->nullable();
            $table->string('status')->default('active'); // 'active', 'exhausted'
            $table->timestamps();

            $table->index(['product_id', 'status', 'remaining_quantity']);
            $table->index(['product_id', 'purchase_date', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
