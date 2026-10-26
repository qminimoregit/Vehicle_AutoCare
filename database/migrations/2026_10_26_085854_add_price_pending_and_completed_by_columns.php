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
        Schema::table('job_cards', function (Blueprint $table) {
            if (! Schema::hasColumn('job_cards', 'completed_by')) {
                $table->foreignId('completed_by')->nullable()->after('completed_at')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('job_card_services', function (Blueprint $table) {
            if (! Schema::hasColumn('job_card_services', 'is_price_pending')) {
                $table->boolean('is_price_pending')->default(false)->after('is_price_custom');
            }
        });

        Schema::table('job_card_products', function (Blueprint $table) {
            if (! Schema::hasColumn('job_card_products', 'is_price_pending')) {
                $table->boolean('is_price_pending')->default(false)->after('line_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_products', function (Blueprint $table) {
            if (Schema::hasColumn('job_card_products', 'is_price_pending')) {
                $table->dropColumn('is_price_pending');
            }
        });

        Schema::table('job_card_services', function (Blueprint $table) {
            if (Schema::hasColumn('job_card_services', 'is_price_pending')) {
                $table->dropColumn('is_price_pending');
            }
        });

        Schema::table('job_cards', function (Blueprint $table) {
            if (Schema::hasColumn('job_cards', 'completed_by')) {
                $table->dropConstrainedForeignId('completed_by');
            }
        });
    }
};
