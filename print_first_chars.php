<?php

use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::first();
Auth::login($user);

$response = $app->handle(Request::create('/billing/multiple-invoice-payment', 'GET', [], [], [], [
    'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
]));

$content = $response->getContent();
echo "--- FIRST 2000 CHARS ---\n";
echo substr($content, 0, 2000)."\n";
