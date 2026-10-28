<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- PRODUCTS & STOCK BATCHES ---\n";
$products = DB::table('products')->get();
foreach ($products as $p) {
    $batches = DB::table('stock_batches')->where('product_id', $p->id)->where('status', 'active')->get();
    $totalRemaining = $batches->sum('remaining_quantity');
    echo "Product [ID {$p->id}] {$p->name} | SKU: {$p->sku} | Price: {$p->selling_price} | Available Stock: {$totalRemaining}\n";
    foreach ($batches as $b) {
        echo "   -> Batch ID {$b->id} ({$b->batch_number}): remaining={$b->remaining_quantity}, cost={$b->inventory_unit_cost}\n";
    }
}

echo "\n--- SERVICES ---\n";
$services = DB::table('services')->get();
foreach ($services as $s) {
    echo "Service [ID {$s->id}] {$s->name} | Code: {$s->code} | Price: {$s->price} | Pricing Type: {$s->pricing_type}\n";
}

echo "\n--- TOTALS ---\n";
echo "Invoices: " . DB::table('invoices')->count() . " | Total Amount: " . DB::table('invoices')->sum('total_amount') . " | Paid Amount: " . DB::table('invoices')->sum('paid_amount') . "\n";
echo "Payments: " . DB::table('payments')->count() . " | Sum Amount: " . DB::table('payments')->sum('amount') . "\n";
echo "Job Cards: " . DB::table('job_cards')->count() . "\n";
