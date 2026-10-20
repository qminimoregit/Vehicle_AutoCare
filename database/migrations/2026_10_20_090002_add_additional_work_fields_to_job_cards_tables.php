<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Expand JobCard status enum on MySQL to include 'additional_work'
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE job_cards MODIFY COLUMN status ENUM('pending', 'in_progress', 'additional_work', 'completed', 'delivered', 'cancelled') DEFAULT 'pending'");
        }

        // 2. Add additional work columns to job_card_services
        Schema::table('job_card_services', function (Blueprint $table) {
            $table->boolean('is_additional')->default(false)->after('is_price_custom');
            $table->foreignId('job_issue_id')->nullable()->after('is_additional')->constrained('job_issues')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->after('job_issue_id')->constrained('invoices')->nullOnDelete();
        });

        // 3. Add additional work columns to job_card_products
        Schema::table('job_card_products', function (Blueprint $table) {
            $table->boolean('is_additional')->default(false)->after('is_stock_deducted');
            $table->foreignId('job_issue_id')->nullable()->after('is_additional')->constrained('job_issues')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->after('job_issue_id')->constrained('invoices')->nullOnDelete();
        });

        // 4. Backfill existing billed services and products with their job's invoice_id
        $billedJobs = DB::table('job_cards')->whereNotNull('invoice_id')->get(['id', 'invoice_id']);
        foreach ($billedJobs as $job) {
            DB::table('job_card_services')->where('job_card_id', $job->id)->update(['invoice_id' => $job->invoice_id]);
            DB::table('job_card_products')->where('job_card_id', $job->id)->update(['invoice_id' => $job->invoice_id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_id');
            $table->dropConstrainedForeignId('job_issue_id');
            $table->dropColumn('is_additional');
        });

        Schema::table('job_card_services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_id');
            $table->dropConstrainedForeignId('job_issue_id');
            $table->dropColumn('is_additional');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE job_cards MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed', 'delivered', 'cancelled') DEFAULT 'pending'");
        }
    }
};
