<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageNavigationAndRenderingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Branch $branch;

    protected Customer $customer;

    protected Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $company = CompanySetting::getSettings();

        $this->branch = Branch::create([
            'company_id' => $company->id,
            'branch_name' => 'Main Branch',
            'branch_code' => 'MAIN',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Manager',
            'email' => 'admin@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->customer = Customer::create([
            'customer_name' => 'Sunil Perera',
            'company_name' => 'Perera Enterprises',
            'customer_type' => 'individual',
            'phone' => '0779876543',
            'status' => 'active',
        ]);

        $vType = VehicleType::firstOrCreate(['name' => 'Sedan'], ['status' => 'active']);
        $vehicle = Vehicle::create([
            'vehicle_number' => 'WP-CAB-1234',
            'customer_id' => $this->customer->id,
            'vehicle_type_id' => $vType->id,
            'status' => 'active',
        ]);

        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-0050',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $vType->id,
            'date' => now()->toDateString(),
            'status' => 'invoiced',
            'created_by' => $this->admin->id,
        ]);

        $this->invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0050',
            'invoice_type' => 'service',
            'invoice_date' => now()->toDateString(),
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'job_card_id' => $jobCard->id,
            'subtotal' => 10000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'grand_total' => 10000,
            'amount_paid' => 3000,
            'balance_amount' => 7000,
            'status' => 'partially_paid',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_multiple_invoice_payment_renders_inside_main_content_and_has_both_tabs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('billing.multiple-invoice-payment.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // 1. Must contain <main id="main-content">
        $this->assertStringContainsString('<main id="main-content"', $content);

        // 2. DOCTYPE must appear before main content
        $doctypePos = strpos($content, '<!DOCTYPE');
        $mainPos = strpos($content, '<main id="main-content"');
        $this->assertNotFalse($doctypePos);
        $this->assertNotFalse($mainPos);
        $this->assertLessThan($mainPos, $doctypePos, 'DOCTYPE must appear before <main id="main-content">');

        // 3. Option 1 and Option 2 must appear INSIDE <main id="main-content">
        preg_match('/<main id="main-content"[^>]*>(.*?)<\/main>/s', $content, $matches);
        $this->assertNotEmpty($matches, 'Main content container must exist and close properly');
        $mainContent = $matches[1];

        $this->assertStringContainsString('Multiple Invoice Payment', $mainContent);
        $this->assertStringContainsString('Option 1: Split Invoice Payment', $mainContent);
        $this->assertStringContainsString('Option 2: Pay Multiple Invoices', $mainContent);
        $this->assertStringContainsString('Invoice Search', $mainContent);
    }

    public function test_invoice_date_change_renders_only_its_own_form_and_does_not_contain_payment_tabs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('billing.invoice-date-change.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Must contain Invoice Date Change
        $this->assertStringContainsString('Invoice Date Change', $content);

        // Must NOT contain Multiple Invoice Payment tabs
        $this->assertStringNotContainsString('Option 1: Split Invoice Payment', $content);
        $this->assertStringNotContainsString('Option 2: Pay Multiple Invoices', $content);

        // Verify inside <main id="main-content">
        preg_match('/<main id="main-content"[^>]*>(.*?)<\/main>/s', $content, $matches);
        $this->assertNotEmpty($matches);
        $mainContent = $matches[1];

        $this->assertStringContainsString('Invoice Date Change', $mainContent);
        $this->assertStringNotContainsString('Option 1: Split Invoice Payment', $mainContent);
        $this->assertStringNotContainsString('Option 2: Pay Multiple Invoices', $mainContent);
    }

    public function test_quotations_renders_only_quotation_content_and_does_not_contain_payment_tabs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('billing.quotations.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Must contain Quotations
        $this->assertStringContainsString('Quotations', $content);

        // Must NOT contain Multiple Invoice Payment tabs
        $this->assertStringNotContainsString('Option 1: Split Invoice Payment', $content);
        $this->assertStringNotContainsString('Option 2: Pay Multiple Invoices', $content);

        // Verify inside <main id="main-content">
        preg_match('/<main id="main-content"[^>]*>(.*?)<\/main>/s', $content, $matches);
        $this->assertNotEmpty($matches);
        $mainContent = $matches[1];

        $this->assertStringContainsString('Quotations', $mainContent);
        $this->assertStringNotContainsString('Option 1: Split Invoice Payment', $mainContent);
        $this->assertStringNotContainsString('Option 2: Pay Multiple Invoices', $mainContent);
    }

    public function test_invoice_search_normalization_with_and_without_hash(): void
    {
        // 1. Search without leading #
        $responseWithoutHash = $this->actingAs($this->admin)->getJson(
            route('billing.multiple-invoice-payment.search-invoice', ['number' => 'INV-202610-0050'])
        );
        $responseWithoutHash->assertStatus(200)
            ->assertJson([
                'success' => true,
                'invoice' => [
                    'invoice_number' => 'INV-202610-0050',
                    'balance_amount' => 7000,
                ],
            ]);

        // 2. Search with leading #
        $responseWithHash = $this->actingAs($this->admin)->getJson(
            route('billing.multiple-invoice-payment.search-invoice', ['number' => '#INV-202610-0050'])
        );
        $responseWithHash->assertStatus(200)
            ->assertJson([
                'success' => true,
                'invoice' => [
                    'invoice_number' => 'INV-202610-0050',
                    'balance_amount' => 7000,
                ],
            ]);

        // 3. Search with lowercase and whitespace
        $responseFuzzy = $this->actingAs($this->admin)->getJson(
            route('billing.multiple-invoice-payment.search-invoice', ['number' => '  inv-202610-0050  '])
        );
        $responseFuzzy->assertStatus(200)
            ->assertJson([
                'success' => true,
                'invoice' => [
                    'invoice_number' => 'INV-202610-0050',
                ],
            ]);
    }
}
