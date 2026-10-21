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

// Find where Option 1 appears:
$pos = strpos($content, 'Option 1: Split Invoice Payment');
echo "Position of Option 1: $pos\n";

// Show 500 characters around it:
echo "Context around Option 1:\n";
echo substr($content, max(0, $pos - 300), 700) . "\n";

// Also check where </main> is relative to $pos
$mainOpen = strpos($content, '<main id="main-content"');
$mainClose = strpos($content, '</main>');
echo "main open pos: $mainOpen\n";
echo "main close pos: $mainClose\n";
echo "Option 1 pos: $pos\n";
