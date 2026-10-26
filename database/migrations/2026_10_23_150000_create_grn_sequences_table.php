<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates dedicated grn_sequences table for concurrency-safe atomic GRN sequence generation.
     */
    public function up(): void
    {
        if (! Schema::hasTable('grn_sequences')) {
            Schema::create('grn_sequences', function (Blueprint $table) {
                $table->id();
                $table->string('series', 20)->unique()->comment('YearMonth series e.g. 202610');
                $table->unsignedInteger('current_number')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_sequences');
    }
};
