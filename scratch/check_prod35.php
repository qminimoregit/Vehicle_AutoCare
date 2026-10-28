<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$p35 = DB::table('products')->where('id', 35)->first();
echo "Product 35: " . json_encode($p35) . "\n";

// check previous job cards products
$prevJobProd = DB::table('job_card_products')->where('product_id', 35)->get();
echo "Previous job card products for 35:\n";
foreach ($prevJobProd as $jp) {
    echo json_encode($jp) . "\n";
}

$prevJobProd34 = DB::table('job_card_products')->where('product_id', 34)->get();
echo "Previous job card products for 34:\n";
foreach ($prevJobProd34 as $jp) {
    echo json_encode($jp) . "\n";
}
