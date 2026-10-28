<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "--- SERVICES COLUMNS ---\n";
print_r(Schema::getColumnListing('services'));

$services = DB::table('services')->get();
foreach ($services as $s) {
    echo json_encode($s) . "\n";
}

echo "\n--- TOTALS ---\n";
echo "Invoices: " . DB::table('invoices')->count() . " | Total Amount: " . DB::table('invoices')->sum('total_amount') . " | Paid Amount: " . DB::table('invoices')->sum('paid_amount') . "\n";
echo "Payments: " . DB::table('payments')->count() . " | Sum Amount: " . DB::table('payments')->sum('amount') . "\n";
echo "Job Cards: " . DB::table('job_cards')->count() . "\n";
