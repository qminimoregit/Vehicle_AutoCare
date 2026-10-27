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

function testRoute($app, $user, $uri)
{
    echo "========================================\n";
    echo "TESTING ROUTE: $uri\n";
    echo "========================================\n";

    // We can use Laravel's internal testing response or actingAs
    $response = $app->handle(Request::create($uri, 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    ]));
    // If redirected, see where:
    if ($response->isRedirection()) {
        echo 'Redirected to: '.$response->headers->get('Location')."\n";
    }
    echo 'STATUS: '.$response->getStatusCode()."\n";
    $content = $response->getContent();
    echo 'LENGTH: '.strlen($content)."\n";

    // Check for Multiple Invoice Payment strings
    $hasOption1 = str_contains($content, 'Option 1: Split Invoice Payment');
    $hasOption2 = str_contains($content, 'Option 2: Pay Multiple Invoices');
    $hasInvoiceSearch = str_contains($content, 'Invoice Search');
    $hasQuotations = str_contains($content, 'Quotations');
    $hasInvoiceDateChange = str_contains($content, 'Invoice Date Change');

    echo 'Has Option 1: '.($hasOption1 ? 'YES' : 'NO')."\n";
    echo 'Has Option 2: '.($hasOption2 ? 'YES' : 'NO')."\n";
    echo 'Has Invoice Search: '.($hasInvoiceSearch ? 'YES' : 'NO')."\n";
    echo 'Has Quotations title: '.($hasQuotations ? 'YES' : 'NO')."\n";
    echo 'Has Invoice Date Change title: '.($hasInvoiceDateChange ? 'YES' : 'NO')."\n";

    // Extract what's inside <main id="main-content">
    if (preg_match('/<main id="main-content"[^>]*>(.*?)<\/main>/s', $content, $m)) {
        $mainContent = $m[1];
        echo 'Main Content Length: '.strlen($mainContent)."\n";
        echo "Main Content Snippet (first 300 chars):\n".substr(trim(strip_tags($mainContent)), 0, 300)."\n";
    } else {
        echo "NO <main id=\"main-content\"> found!\n";
    }
}

testRoute($app, $user, '/billing/multiple-invoice-payment');
testRoute($app, $user, '/billing/invoice-date-change');
testRoute($app, $user, '/billing/quotations');
