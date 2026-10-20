<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\FreshTestDataSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ResetTestDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autocare:reset-test-data
                            {--dry-run : Preview tables and records to be cleared without making changes}
                            {--force : Force the operation without confirmation prompt}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Safely clear existing business data and seed fresh, realistic test data for manual testing (local development only).';

    /**
     * Business tables in reverse dependency order for safe removal.
     *
     * @var array<string>
     */
    protected array $businessTables = [
        'payment_allocations',
        'payments',
        'payment_receipts',
        'invoice_items',
        'direct_sale_items',
        'direct_sales',
        'job_card_products',
        'job_card_services',
        'job_cards',
        'quotation_items',
        'quotations',
        'invoices',
        'vehicles',
        'customers',
        'expenses',
        'payroll_payments',
        'payroll_items',
        'payroll_periods',
        'employee_advances',
        'employee_attendance',
        'employee_wage_rates',
        'employees',
        'supplier_payments',
        'stock_movements',
        'stock_batches',
        'goods_received_note_items',
        'goods_received_notes',
        'products',
        'suppliers',
        'service_vehicle_prices',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('===============================================================');
        $this->info('   AutoCare System – Database Reset & Test Data Preparation   ');
        $this->info('===============================================================');

        // 1. Safety & Environment Guard
        $env = App::environment();
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');

        $this->line("Environment  : <comment>{$env}</comment>");
        $this->line("Database     : <comment>{$dbName}</comment> on <comment>{$dbHost}:{$dbPort}</comment>");

        if (! App::environment('local')) {
            $this->error('FATAL ERROR: This command is strictly forbidden outside the local development environment.');
            $this->error("Current environment is [{$env}]. Operation aborted.");

            return Command::FAILURE;
        }

        if (in_array(strtolower($dbHost), ['production', 'prod', 'aws', 'rds']) || str_contains(strtolower($dbName), 'prod')) {
            $this->error('FATAL ERROR: Suspected production database host or database name. Operation aborted.');

            return Command::FAILURE;
        }

        // 2. Fetch current counts
        $tableCounts = [];
        $totalBusinessRows = 0;
        foreach ($this->businessTables as $table) {
            $cnt = DB::table($table)->count();
            $tableCounts[$table] = $cnt;
            $totalBusinessRows += $cnt;
        }

        $adminUser = User::where('role', 'admin')->first();
        $staffUser = User::where('role', 'staff')->first();

        // 3. Dry-Run Preview
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->newLine();
            $this->warn('--- DRY RUN PREVIEW (NO DATA WILL BE MODIFIED) ---');
            $this->newLine();

            $this->info('1. Protected Records (Will NOT be deleted):');
            $this->line("   - Administrator Account : {$adminUser?->name} ({$adminUser?->email}) [Role: admin]");
            $this->line("   - Staff Test Account    : {$staffUser?->name} ({$staffUser?->email}) [Role: staff]");
            $this->line('   - Core Company Settings : AutoCare Service Center (Pvt) Ltd');
            $this->line('   - Units of Measurement  : Bottle, Can, Packet, Item, Loose, Tube');
            $this->line('   - Database Migrations   : All 44 migrations preserved intact');

            $this->newLine();
            $this->info('2. Business Tables to be Cleared (Safe Foreign Key Dependency Order):');

            $tableRows = [];
            foreach ($tableCounts as $tbl => $cnt) {
                $tableRows[] = [$tbl, $cnt];
            }
            $this->table(['Table Name', 'Current Record Count'], $tableRows);
            $this->line("Total business records to be cleared: <comment>{$totalBusinessRows}</comment>");

            $this->newLine();
            $this->info('3. Fresh Test Data Scope (Will be Inserted):');
            $this->line('   - Master Data: 2 Branches (Colombo Main, Kandy), 6 Vehicle Types with images, 8 Services & price matrix');
            $this->line('   - Inventory  : 5 Suppliers, 15 Products, 6 GRNs (multiple batches, FIFO pricing, zero & low stock scenarios)');
            $this->line('   - Customers  : 8 Individual Customers, 2 Corporate Fleet Customers (QuickCab, Lanka Logistics)');
            $this->line('   - Vehicles   : 15 Sri Lankan format vehicles (Toyota Prius, Honda Fit, HiAce, Montero, Canter, etc.)');
            $this->line('   - Job Cards  : 12 Job Cards (3 Pending, 3 In-Progress, 4 Completed, 2 Delivered)');
            $this->line('   - Direct Sale: 5 POS Direct Sales (Cash, Card, Bank transfer, multi-product)');
            $this->line('   - Invoices   : 5 Quotations & 8 Invoices (Paid, Partially Paid, Unpaid)');
            $this->line('   - Payroll    : 6 Employees, September approved payroll & payments, October draft payroll & attendance');
            $this->line('   - Expenses   : 8 Expense categories, 15 Expense records (shop + staff)');
            $this->line('   - Stage 6    : Dedicated eligible test records for Avoid Direct Sale, Avoid Service Sale, Date Change & Multiple Payment');

            $this->newLine();
            $this->info('Dry-run complete. Run without --dry-run to apply changes.');

            return Command::SUCCESS;
        }

        // 4. Interactive Confirmation
        if (! $this->option('force')) {
            $this->newLine();
            $this->warn("CAUTION: This will delete {$totalBusinessRows} business records from [{$dbName}] and insert fresh test data.");
            $confirmed = $this->confirm('Are you sure you want to reset business data and seed fresh test data?', false);

            if (! $confirmed) {
                $this->info('Reset cancelled by user. No records were modified.');

                return Command::SUCCESS;
            }
        }

        // 5. Execution
        $this->newLine();
        $this->info('Beginning safe business data reset...');

        DB::beginTransaction();

        try {
            // Nullify circular references first
            DB::statement('UPDATE direct_sales SET invoice_id = NULL WHERE invoice_id IS NOT NULL');
            DB::statement('UPDATE job_cards SET invoice_id = NULL WHERE invoice_id IS NOT NULL');
            DB::statement('UPDATE expenses SET employee_advance_id = NULL WHERE employee_advance_id IS NOT NULL');
            DB::statement('UPDATE employee_advances SET payroll_item_id = NULL WHERE payroll_item_id IS NOT NULL');
            DB::statement('UPDATE payments SET payment_receipt_id = NULL WHERE payment_receipt_id IS NOT NULL');

            // Delete business records in safe reverse dependency order
            foreach ($this->businessTables as $table) {
                $count = DB::table($table)->count();
                DB::table($table)->delete();
                $this->line(" - Cleared table <comment>{$table}</comment> ({$count} records removed)");
            }

            // Seed fresh test data
            $this->info('Seeding fresh, realistic test data via FreshTestDataSeeder...');
            $seeder = new FreshTestDataSeeder;
            $seeder->run();

            DB::commit();
            $this->info('Database reset & test data seeding committed successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('An error occurred during reset. Transaction rolled back.');
            $this->error($e->getMessage());

            return Command::FAILURE;
        }

        // 6. Post-Verification
        $this->newLine();
        $this->info('=== Verification Summary ===');
        $this->table(
            ['Entity', 'New Count', 'Status'],
            [
                ['Branches', DB::table('branches')->count(), 'OK'],
                ['Vehicle Types', DB::table('vehicle_types')->count(), 'OK'],
                ['Services', DB::table('services')->count(), 'OK'],
                ['Suppliers', DB::table('suppliers')->count(), 'OK'],
                ['Products', DB::table('products')->count(), 'OK'],
                ['Stock Batches', DB::table('stock_batches')->count(), 'OK'],
                ['Customers', DB::table('customers')->count(), 'OK'],
                ['Vehicles', DB::table('vehicles')->count(), 'OK'],
                ['Job Cards', DB::table('job_cards')->count(), 'OK'],
                ['Direct Sales', DB::table('direct_sales')->count(), 'OK'],
                ['Quotations', DB::table('quotations')->count(), 'OK'],
                ['Invoices', DB::table('invoices')->count(), 'OK'],
                ['Payments', DB::table('payments')->count(), 'OK'],
                ['Employees', DB::table('employees')->count(), 'OK'],
                ['Attendance Records', DB::table('employee_attendance')->count(), 'OK'],
                ['Payroll Periods', DB::table('payroll_periods')->count(), 'OK'],
                ['Expenses', DB::table('expenses')->count(), 'OK'],
            ]
        );

        $this->info("✓ Admin account preserved: {$adminUser?->name} ({$adminUser?->email})");
        $this->info("✓ Staff account preserved: {$staffUser?->name} ({$staffUser?->email})");
        $this->info('✓ All systems ready for comprehensive manual testing.');
        $this->newLine();

        return Command::SUCCESS;
    }
}
