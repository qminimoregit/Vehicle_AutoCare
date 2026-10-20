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
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->string('payment_number', 50)->nullable()->unique()->after('id');
            $table->foreignId('branch_id')->nullable()->after('supplier_id')->constrained('branches')->nullOnDelete();
            $table->string('status', 20)->default('completed')->after('notes');
            $table->dateTime('reversed_at')->nullable()->after('status');
            $table->foreignId('reversed_by')->nullable()->after('reversed_at')->constrained('users')->nullOnDelete();
            $table->text('reversal_reason')->nullable()->after('reversed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['reversed_by']);
            $table->dropColumn([
                'payment_number',
                'branch_id',
                'status',
                'reversed_at',
                'reversed_by',
                'reversal_reason',
            ]);
        });
    }
};
