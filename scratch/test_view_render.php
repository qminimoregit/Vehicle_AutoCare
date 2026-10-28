<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

session()->flash('error', 'Payment failed: Some error');
$invoice = App\Models\Invoice::find(78);
$company = App\Models\CompanySetting::getSettings();
$amountInWords = '';

$html = view('operations.invoices.show', compact('invoice', 'company', 'amountInWords'))->render();

preg_match('/showPaymentModal:\s*(true|false)/', $html, $matches);
echo "showPaymentModal match: " . ($matches[0] ?? 'not found') . "\n";

