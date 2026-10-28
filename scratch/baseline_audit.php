<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "--- CUSTOMER COLUMNS ---\n";
print_r(Schema::getColumnListing('customers'));

echo "--- VEHICLE COLUMNS ---\n";
print_r(Schema::getColumnListing('vehicles'));

echo "--- CUSTOMER SAMPLES ---\n";
$customers = DB::table('customers')->limit(5)->get();
foreach ($customers as $c) {
    echo json_encode($c) . "\n";
}

echo "--- VEHICLE SAMPLES ---\n";
$vehicles = DB::table('vehicles')->limit(5)->get();
foreach ($vehicles as $v) {
    echo json_encode($v) . "\n";
}

echo "--- EMPLOYEES ---\n";
$employees = DB::table('employees')->get();
foreach ($employees as $e) {
    echo "ID: " . $e->id . " | Name: " . ($e->name ?? ($e->first_name . ' ' . $e->last_name)) . " | Designation: " . ($e->designation ?? $e->role ?? 'N/A') . "\n";
}
