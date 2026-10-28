<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "--- PRODUCT COLUMNS ---\n";
print_r(Schema::getColumnListing('products'));

echo "--- PRODUCTS SAMPLES ---\n";
$products = DB::table('products')->where('name', 'like', '%Toyota%')->get();
foreach ($products as $p) {
    echo json_encode($p) . "\n";
}

echo "--- STOCK BATCHES FOR TOYOTA PRODUCTS ---\n";
foreach ($products as $p) {
    $batches = DB::table('stock_batches')->where('product_id', $p->id)->get();
    echo "Product " . $p->id . " (" . $p->name . ") Total Batches: " . $batches->count() . " | Available Qty: " . $batches->sum('quantity_available') . "\n";
    foreach ($batches as $b) {
        echo "  -> Batch " . $b->id . " | Avail: " . $b->quantity_available . " | Cost: " . $b->unit_cost . "\n";
    }
}

echo "--- SERVICES ---\n";
$services = DB::table('services')->get();
foreach ($services as $s) {
    echo "ID: " . $s->id . " | Code: " . $s->code . " | Name: " . $s->name . " | Price: " . $s->price . " | Type: " . $s->pricing_type . "\n";
}

echo "--- BASELINE FINANCIAL TOTALS ---\n";
echo "Total Invoices: " . DB::table('invoices')->count() . " | Sum Total: " . DB::table('invoices')->sum('total_amount') . " | Sum Paid: " . DB::table('invoices')->sum('paid_amount') . " | Sum Outstanding: " . (DB::table('invoices')->sum('total_amount') - DB::table('invoices')->sum('paid_amount')) . "\n";
echo "Total Payments: " . DB::table('payments')->count() . " | Sum: " . DB::table('payments')->sum('amount') . "\n";
echo "Total Job Cards: " . DB::table('job_cards')->count() . "\n";
