<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$totalGrand = DB::table('invoices')->sum('grand_total');
$totalPaid = DB::table('invoices')->sum('amount_paid');
$totalBal = DB::table('invoices')->sum('balance_amount');
$totalPayments = DB::table('payments')->sum('amount');
$totalInvoicesCount = DB::table('invoices')->count();
$totalPaymentsCount = DB::table('payments')->count();
$totalJobCardsCount = DB::table('job_cards')->count();

echo "--- EXACT BASELINE METRICS ---\n";
echo "Total Job Cards: {$totalJobCardsCount}\n";
echo "Total Invoices: {$totalInvoicesCount}\n";
echo "Invoices Grand Total Sum: LKR " . number_format($totalGrand, 2) . "\n";
echo "Invoices Amount Paid Sum: LKR " . number_format($totalPaid, 2) . "\n";
echo "Invoices Balance Sum: LKR " . number_format($totalBal, 2) . "\n";
echo "Total Payments Count: {$totalPaymentsCount}\n";
echo "Payments Amount Sum: LKR " . number_format($totalPayments, 2) . "\n";
