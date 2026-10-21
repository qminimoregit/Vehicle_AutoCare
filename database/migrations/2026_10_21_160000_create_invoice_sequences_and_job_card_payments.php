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
        // 1. Create dedicated invoice_sequences table for concurrency-safe atomic sequence generation
        if (! Schema::hasTable('invoice_sequences')) {
            Schema::create('invoice_sequences', function (Blueprint $table) {
                $table->id();
                $table->string('series', 20)->unique()->comment('YearMonth series e.g. 202610');
                $table->unsignedInteger('current_number')->default(0);
                $table->timestamps();
            });
        }

        // 2. Safely modify payments table to support Job Card advance payments and make invoice_id nullable
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'job_card_id')) {
                $table->foreignId('job_card_id')->nullable()->after('invoice_id')->constrained('job_cards')->nullOnDelete();
            }
            if (! Schema::hasColumn('payments', 'is_advance')) {
                $table->boolean('is_advance')->default(false)->after('notes');
            }
        });

        // Make invoice_id nullable so advance payments can be accepted prior to final invoice creation
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'job_card_id')) {
                $table->dropConstrainedForeignId('job_card_id');
            }
            if (Schema::hasColumn('payments', 'is_advance')) {
                $table->dropColumn('is_advance');
            }
            $table->unsignedBigInteger('invoice_id')->nullable(false)->change();
        });

        Schema::dropIfExists('invoice_sequences');
    }
};
