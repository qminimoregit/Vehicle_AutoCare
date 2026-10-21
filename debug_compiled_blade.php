<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$compiler = app('blade.compiler');
$path = resource_path('views/billing/multiple-invoice-payment/index.blade.php');
$compiledPath = $compiler->getCompiledPath($path);
echo "Path: $path\n";
echo "Compiled: $compiledPath\n";
if (file_exists($compiledPath)) {
    echo "--- COMPILED PHP (First 40 lines) ---\n";
    $lines = file($compiledPath);
    echo implode('', array_slice($lines, 0, 40));
    echo "\n--- COMPILED PHP (Last 30 lines) ---\n";
    echo implode('', array_slice($lines, -30));
}
