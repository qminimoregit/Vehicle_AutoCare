<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::first();
Auth::login($user);

$response = $app->handle(Illuminate\Http\Request::create('/billing/multiple-invoice-payment', 'GET', [], [], [], [
    'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
]));

$content = $response->getContent();
echo "--- FIRST 2000 CHARS ---\n";
echo substr($content, 0, 2000) . "\n";
