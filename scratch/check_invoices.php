<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "--- INVOICES COLUMNS ---\n";
print_r(Schema::getColumnListing('invoices'));

$inv = DB::table('invoices')->first();
echo json_encode($inv) . "\n";

echo "--- PAYMENTS COLUMNS ---\n";
print_r(Schema::getColumnListing('payments'));
$pay = DB::table('payments')->first();
echo json_encode($pay) . "\n";

echo "--- BASELINE METRICS ---\n";
echo "Total Invoices: " . DB::table('invoices')->count() . " | Sum Total: " . DB::table('invoices')->sum('grand_total') . " | Sum Paid: " . DB::table('invoices')->sum('paid_amount') . "\n";
echo "Total Payments: " . DB::table('payments')->count() . " | Sum: " . DB::table('payments')->sum('amount') . "\n";
echo "Total Job Cards: " . DB::table('job_cards')->count() . "\n";
