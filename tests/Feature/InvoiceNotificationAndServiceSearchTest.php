<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\DirectSale;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Service;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceNotificationAndServiceSearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected Branch $branch;

    protected Branch $otherBranch;

    protected Customer $customer;

    protected Vehicle $vehicle;

    protected VehicleType $vehicleType;

    protected Service $service;

    protected JobCard $jobCard;

    protected Invoice $serviceInvoice;

    protected DirectSale $directSale;

    protected function setUp(): void
    {
        parent::setUp();

        $company = CompanySetting::getSettings();

        $this->branch = Branch::firstOrCreate(
            ['company_id' => $company->id, 'branch_code' => 'BR-001'],
            [
                'branch_name' => 'Colombo Main Branch',
                'is_main_branch' => true,
                'is_active' => true,
            ]
        );

        $this->otherBranch = Branch::firstOrCreate(
            ['company_id' => $company->id, 'branch_code' => 'BR-002'],
            [
                'branch_name' => 'Kandy Branch',
                'is_main_branch' => false,
                'is_active' => true,
            ]
        );

        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();
        $this->branch->users()->syncWithoutDetaching([$this->staff->id]);

        $this->customer = Customer::create([
            'customer_name' => 'Shehan Perera',
            'phone' => '0714624024',
            'is_active' => true,
        ]);

        $this->vehicleType = VehicleType::firstOrCreate(
            ['name' => 'Motorbike'],
            ['description' => 'Two wheeler', 'is_active' => true]
        );

        $this->vehicle = Vehicle::create([
            'customer_id' => $this->customer->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'vehicle_number' => 'BFH-4242',
            'vehicle_number_normalized' => 'BFH4242',
            'current_mileage' => 3400,
            'next_service_mileage' => 6900,
        ]);

        $this->service = Service::create([
            'name' => 'General Inspection & Oil Change',
            'code' => 'SRV-OIL-01',
            'base_price' => 1200.00,
            'is_active' => true,
        ]);

        // Create completed Job Card
        $this->jobCard = JobCard::create([
            'job_number' => 'JOB-202610-0013',
            'branch_id' => $this->branch->id,
            'date' => now()->toDateString(),
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'current_mileage' => 3400,
            'next_service_mileage' => 6900,
            'status' => 'completed',
            'service_total' => 1200.00,
            'product_total' => 0.00,
            'discount_amount' => 0.00,
            'tax_amount' => 0.00,
            'grand_total' => 1200.00,
            'created_by' => $this->admin->id,
        ]);

        JobCardService::create([
            'job_card_id' => $this->jobCard->id,
            'service_id' => $this->service->id,
            'service_name' => $this->service->name,
            'service_code' => $this->service->code,
            'quantity' => 1,
            'unit_price' => 1200.00,
            'line_total' => 1200.00,
        ]);

        // Create Service Invoice linked to Job Card
        $this->serviceInvoice = Invoice::create([
            'invoice_number' => 'INV-202610-0009',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'job_card_id' => $this->jobCard->id,
            'invoice_type' => 'job_card',
            'reference' => $this->jobCard->job_number,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 1200.00,
            'grand_total' => 1200.00,
            'amount_paid' => 1200.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'customer_snapshot' => [
                'customer_name' => 'Shehan Perera',
                'phone' => '0714624024',
                'vehicle_number' => 'BFH-4242',
            ],
            'created_by' => $this->admin->id,
        ]);

        $this->jobCard->update(['invoice_id' => $this->serviceInvoice->id]);

        Payment::create([
            'invoice_id' => $this->serviceInvoice->id,
            'customer_id' => $this->customer->id,
            'payment_number' => 'PAY-202610-0009',
            'payment_date' => now()->toDateString(),
            'amount' => 1200.00,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        // Create a separate Direct Sale invoice
        $uom = UnitOfMeasurement::firstOrCreate(['name' => 'Item'], ['code' => 'ITM', 'is_active' => true]);
        $prod = Product::create([
            'name' => 'Air Filter',
            'sku' => 'FLT-001',
            'uom_id' => $uom->id,
            'selling_price' => 2500.00,
            'is_active' => true,
        ]);

        $directInvoice = Invoice::create([
            'invoice_number' => 'INV-202610-0010',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_type' => 'direct_sale',
            'invoice_date' => now()->toDateString(),
            'subtotal' => 2500.00,
            'grand_total' => 2500.00,
            'amount_paid' => 2500.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        $this->directSale = DirectSale::create([
            'sale_number' => 'POS-202610-0010',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_id' => $directInvoice->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 2500.00,
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);
    }

    /**
     * 1. Generate an invoice from a Job Card and verify the success notification appears once.
     */
    public function test_generating_invoice_from_job_card_shows_notification_only_once(): void
    {
        // Create another completed Job Card without an invoice
        $freshJob = JobCard::create([
            'job_number' => 'JOB-202610-0099',
            'branch_id' => $this->branch->id,
            'date' => now()->toDateString(),
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status' => 'completed',
            'grand_total' => 5000.00,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->followingRedirects()
            ->post(route('operations.job-cards.generate-invoice', $freshJob));

        $response->assertOk();

        // The flash message should appear exactly ONCE in the rendered HTML
        $html = $response->getContent();
        $occurrences = substr_count($html, 'generated for Job #JOB-202610-0099');
        $this->assertEquals(1, $occurrences, 'The flash success notification must be rendered exactly once.');
    }

    /**
     * 2. Search by invoice number without '#'
     */
    public function test_search_by_invoice_number_without_hash(): void
    {
        $response = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => 'INV-202610-0009',
        ]));

        $response->assertOk();
        $response->assertSee('INV-202610-0009');
        $response->assertSee('JOB-202610-0013');
        $response->assertSee('BFH-4242');
        $response->assertDontSee('No matching service invoice found');
    }

    /**
     * 3. Search by invoice number with '#'
     */
    public function test_search_by_invoice_number_with_hash(): void
    {
        $response = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => '#INV-202610-0009',
        ]));

        $response->assertOk();
        $response->assertSee('INV-202610-0009');
        $response->assertSee('JOB-202610-0013');
        $response->assertSee('BFH-4242');
        $response->assertDontSee('No matching service invoice found');
    }

    /**
     * 4. Search by Job Card number (with and without '#')
     */
    public function test_search_by_job_card_number(): void
    {
        $response1 = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => 'JOB-202610-0013',
        ]));
        $response1->assertOk();
        $response1->assertSee('INV-202610-0009');

        $response2 = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => '#JOB-202610-0013',
        ]));
        $response2->assertOk();
        $response2->assertSee('INV-202610-0009');
    }

    /**
     * 5. Search by vehicle number
     */
    public function test_search_by_vehicle_number(): void
    {
        $response1 = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => 'BFH-4242',
        ]));
        $response1->assertOk();
        $response1->assertSee('INV-202610-0009');

        $response2 = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => 'BFH4242',
        ]));
        $response2->assertOk();
        $response2->assertSee('INV-202610-0009');
    }

    /**
     * 6. Verify correct linked invoice details and status badges
     */
    public function test_linked_invoice_details_and_status_badges(): void
    {
        $response = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => 'INV-202610-0009',
        ]));

        $response->assertOk();
        $response->assertSee('Document: Issued');
        $response->assertSee('Payment: Paid in Full');
        $response->assertSee('Shehan Perera');
        $response->assertSee('BFH-4242');
    }

    /**
     * 7. Search for an unpaid service invoice
     */
    public function test_search_unpaid_service_invoice(): void
    {
        $unpaidInv = Invoice::create([
            'invoice_number' => 'INV-202610-0088',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'job_card_id' => $this->jobCard->id,
            'invoice_type' => 'job_card',
            'grand_total' => 3000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 3000.00,
            'status' => 'issued',
            'invoice_date' => now()->toDateString(),
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => '#INV-202610-0088',
        ]));

        $response->assertOk();
        $response->assertSee('INV-202610-0088');
        $response->assertSee('Document: Issued');
        $response->assertSee('Payment: Unpaid');
    }

    /**
     * 8. Search for a nonexistent invoice
     */
    public function test_search_nonexistent_invoice(): void
    {
        $response = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => 'INV-999999-9999',
        ]));

        $response->assertOk();
        $response->assertSee('No matching service invoice found');
    }

    /**
     * 9. Verify direct sale invoices are not incorrectly presented as service invoices
     */
    public function test_direct_sale_invoices_are_not_presented_as_service_invoices(): void
    {
        $response = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => '#INV-202610-0010',
        ]));

        $response->assertOk();
        $response->assertSee('Direct Sale invoice');
        $response->assertSee('Please use Avoid Direct Sale');
    }

    /**
     * 10. Verify branch permissions
     */
    public function test_branch_permissions_prevent_unauthorized_access(): void
    {
        // Create an invoice belonging to Kandy branch
        $kandyInvoice = Invoice::create([
            'invoice_number' => 'INV-202610-0077',
            'branch_id' => $this->otherBranch->id,
            'customer_id' => $this->customer->id,
            'job_card_id' => $this->jobCard->id,
            'invoice_type' => 'job_card',
            'grand_total' => 4500.00,
            'status' => 'issued',
            'invoice_date' => now()->toDateString(),
            'created_by' => $this->admin->id,
        ]);

        // Staff assigned to Colombo branch cannot access Kandy branch invoice
        $response = $this->actingAs($this->staff)->get(route('billing.avoid-service-sale.index', [
            'search' => 'INV-202610-0077',
        ]));

        $response->assertStatus(403);
    }

    /**
     * 11. Verify cancelled invoice displays ineligibility banner
     */
    public function test_cancelled_invoice_shows_meaningful_ineligibility_message(): void
    {
        $this->serviceInvoice->update([
            'status' => 'cancelled',
            'cancellation_reason' => 'Duplicate test booking',
            'cancelled_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', [
            'search' => '#INV-202610-0009',
        ]));

        $response->assertOk();
        $response->assertSee('Invoice found, but this transaction cannot currently be cancelled');
        $response->assertDontSee('Yes, Avoid Service Sale');
    }
}
