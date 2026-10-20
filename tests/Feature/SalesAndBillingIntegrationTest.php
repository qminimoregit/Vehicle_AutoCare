<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\DirectSale;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobCard;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\Inventory\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesAndBillingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Branch $branch;
    protected CompanySetting $company;
    protected Customer $corporateCustomer;
    protected Vehicle $vehicle1;
    protected Vehicle $vehicle2;
    protected Service $serviceOilChange;
    protected Service $serviceBrakeCheck;
    protected Product $engineOil;
    protected Product $oilFilter;
    protected StockService $stockService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockService = app(StockService::class);

        // Company Setting
        $this->company = CompanySetting::getSettings();

        // Branch
        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'COL-01',
            'branch_name' => 'Colombo Main Branch',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        // Users
        $this->admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->staff = User::create([
            'name' => 'Front Desk Staff',
            'email' => 'staff@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        // Vehicle Type
        $carType = VehicleType::create([
            'name' => 'Sedan',
            'status' => 'active',
        ]);

        // Corporate Customer with multiple vehicles
        $this->corporateCustomer = Customer::create([
            'customer_name' => 'Nisal Fernando',
            'customer_type' => 'company',
            'company_name' => 'Lanka Logistics Ltd',
            'phone' => '0771234567',
            'email' => 'fleet@lankalogistics.lk',
            'tax_number' => 'VAT-778899',
            'status' => 'active',
        ]);

        $this->vehicle1 = Vehicle::create([
            'customer_id' => $this->corporateCustomer->id,
            'vehicle_type_id' => $carType->id,
            'vehicle_number' => 'CAB-1001',
            'make_model' => 'Toyota Prius',
            'status' => 'active',
        ]);

        $this->vehicle2 = Vehicle::create([
            'customer_id' => $this->corporateCustomer->id,
            'vehicle_type_id' => $carType->id,
            'vehicle_number' => 'CAB-2002',
            'make_model' => 'Nissan Leaf',
            'status' => 'active',
        ]);

        // Services
        $this->serviceOilChange = Service::create([
            'code' => 'SRV-OIL',
            'name' => 'Full Synthetic Oil Change Service',
            'default_price' => 3500.00,
            'status' => 'active',
        ]);

        $this->serviceBrakeCheck = Service::create([
            'code' => 'SRV-BRK',
            'name' => 'Comprehensive Brake Inspection',
            'default_price' => 2000.00,
            'status' => 'active',
        ]);

        // UOM & Supplier
        $uom = UnitOfMeasurement::create([
            'name' => 'Liter',
            'abbreviation' => 'L',
            'status' => 'active',
        ]);

        $supplier = Supplier::create([
            'name' => 'Lanka Lubricants PLC',
            'mobile' => '0112345678',
            'is_active' => true,
        ]);

        // Products
        $this->engineOil = Product::create([
            'name' => 'Castrol Edge 5W-30 (1L)',
            'sku' => 'OIL-CAS-5W30',
            'barcode' => '789101112',
            'uom_id' => $uom->id,
            'cost_price' => 2200.00,
            'selling_price' => 3200.00,
            'status' => 'active',
        ]);

        $this->oilFilter = Product::create([
            'name' => 'Genuine Toyota Oil Filter',
            'sku' => 'FLT-TOY-001',
            'barcode' => '789101113',
            'uom_id' => $uom->id,
            'cost_price' => 1200.00,
            'selling_price' => 1800.00,
            'status' => 'active',
        ]);

        // Stock Batches for products (50 units each)
        $this->createStockBatch($this->engineOil, 50, 2200.00, 'BATCH-OIL-001');
        $this->createStockBatch($this->oilFilter, 50, 1200.00, 'BATCH-FLT-001');
    }

    protected function createStockBatch(Product $product, float $quantity, float $cost, string $batchNo): StockBatch
    {
        $supplier = Supplier::firstOrCreate(
            ['name' => 'Auto Parts Supplier'],
            ['mobile' => '0770001122', 'is_active' => true]
        );

        $grn = \App\Models\GoodsReceivedNote::create([
            'grn_number' => 'GRN-' . uniqid(),
            'branch_id' => $this->branch->id,
            'supplier_id' => $supplier->id,
            'received_date' => now()->toDateString(),
            'subtotal' => $quantity * $cost,
            'grand_total' => $quantity * $cost,
            'status' => 'confirmed',
            'created_by' => $this->admin->id,
        ]);

        $grnItem = $grn->items()->create([
            'product_id' => $product->id,
            'purchase_quantity' => $quantity,
            'free_quantity' => 0,
            'purchase_unit_price' => $cost,
            'selling_price_snapshot' => $product->selling_price,
            'line_total' => $quantity * $cost,
        ]);

        return StockBatch::create([
            'batch_number' => $batchNo,
            'branch_id' => $this->branch->id,
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem->id,
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'received_quantity' => $quantity,
            'remaining_quantity' => $quantity,
            'original_purchase_unit_price' => $cost,
            'inventory_unit_cost' => $cost,
            'status' => 'active',
        ]);
    }

    /**
     * Test creating a quotation with services and products does NOT deduct stock.
     */
    public function test_create_quotation_calculates_totals_and_does_not_deduct_stock(): void
    {
        $oilBeforeStock = $this->stockService->getAvailableStock($this->engineOil);

        $payload = [
            'customer_id' => $this->corporateCustomer->id,
            'vehicle_id' => $this->vehicle1->id,
            'quotation_date' => now()->toDateString(),
            'expiry_date' => now()->addDays(14)->toDateString(),
            'subject' => 'Periodic Fleet Service Estimate',
            'discount_type' => 'percentage',
            'discount_value' => 10, // 10% discount
            'tax_percentage' => 0,
            'additional_charges' => 500, // disposal fee
            'items' => [
                [
                    'item_type' => 'service',
                    'item_id' => $this->serviceOilChange->id,
                    'item_name' => $this->serviceOilChange->name,
                    'quantity' => 1,
                    'unit_price' => 3500.00,
                    'discount_type' => 'none',
                    'discount_value' => 0,
                ],
                [
                    'item_type' => 'product',
                    'item_id' => $this->engineOil->id,
                    'item_name' => $this->engineOil->name,
                    'quantity' => 4,
                    'unit_price' => 3200.00,
                    'discount_type' => 'none',
                    'discount_value' => 0,
                ],
            ],
        ];

        $response = $this->actingAs($this->staff)->post(route('billing.quotations.store'), $payload);

        $response->assertRedirect();
        
        $quotation = Quotation::where('customer_id', $this->corporateCustomer->id)->first();
        $this->assertNotNull($quotation);
        $this->assertEquals('draft', $quotation->status);
        $this->assertEquals(2, $quotation->items()->count());

        // Subtotal = 3500 + (4 * 3200) = 3500 + 12800 = 16300
        $this->assertEquals(16300.00, (float) $quotation->subtotal);

        // 10% discount = 1630
        $this->assertEquals(1630.00, (float) $quotation->discount_amount);

        // Grand total = (16300 - 1630) + 500 = 14670 + 500 = 15170
        $this->assertEquals(15170.00, (float) $quotation->grand_total);

        // Verify stock remains untouched
        $oilAfterStock = $this->stockService->getAvailableStock($this->engineOil);
        $this->assertEquals($oilBeforeStock, $oilAfterStock);
    }

    /**
     * Test quotation workflow: send, accept, and reject.
     */
    public function test_quotation_status_lifecycle(): void
    {
        $quotation = Quotation::create([
            'quotation_number' => Quotation::generateQuotationNumber(),
            'customer_id' => $this->corporateCustomer->id,
            'vehicle_id' => $this->vehicle1->id,
            'quotation_date' => now()->toDateString(),
            'status' => 'draft',
            'subtotal' => 5000,
            'grand_total' => 5000,
            'created_by' => $this->staff->id,
        ]);

        // Send quotation
        $this->actingAs($this->staff)->post(route('billing.quotations.send', $quotation))
            ->assertRedirect();
        $this->assertEquals('sent', $quotation->fresh()->status);

        // Accept quotation
        $this->actingAs($this->staff)->post(route('billing.quotations.accept', $quotation))
            ->assertRedirect();
        $this->assertEquals('accepted', $quotation->fresh()->status);
    }

    /**
     * Test converting accepted quotation to Invoice.
     */
    public function test_convert_accepted_quotation_to_invoice(): void
    {
        $quotation = Quotation::create([
            'quotation_number' => Quotation::generateQuotationNumber(),
            'customer_id' => $this->corporateCustomer->id,
            'vehicle_id' => $this->vehicle1->id,
            'quotation_date' => now()->toDateString(),
            'status' => 'accepted',
            'subtotal' => 5500.00,
            'grand_total' => 5500.00,
            'created_by' => $this->staff->id,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'sort_order' => 1,
            'item_type' => 'service',
            'item_id' => $this->serviceOilChange->id,
            'item_name' => $this->serviceOilChange->name,
            'quantity' => 1,
            'unit_price' => 3500.00,
            'line_total' => 3500.00,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'sort_order' => 2,
            'item_type' => 'service',
            'item_id' => $this->serviceBrakeCheck->id,
            'item_name' => $this->serviceBrakeCheck->name,
            'quantity' => 1,
            'unit_price' => 2000.00,
            'line_total' => 2000.00,
        ]);

        $response = $this->actingAs($this->staff)->post(route('billing.quotations.convert-to-invoice', $quotation));
        $response->assertRedirect();

        // Quotation should be marked as converted
        $this->assertEquals('converted', $quotation->fresh()->status);

        // Invoice should exist with matching total and quotation linkage
        $invoice = Invoice::where('quotation_id', $quotation->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('quotation', $invoice->invoice_type);
        $this->assertEquals(5500.00, (float) $invoice->grand_total);
        $this->assertEquals(5500.00, (float) $invoice->balance_amount);
        $this->assertEquals(0.00, (float) $invoice->amount_paid);
        $this->assertEquals(2, $invoice->items()->count());
    }

    /**
     * Test converting accepted quotation to Job Card.
     */
    public function test_create_job_card_from_accepted_quotation(): void
    {
        $quotation = Quotation::create([
            'quotation_number' => Quotation::generateQuotationNumber(),
            'customer_id' => $this->corporateCustomer->id,
            'vehicle_id' => $this->vehicle2->id,
            'quotation_date' => now()->toDateString(),
            'status' => 'accepted',
            'subtotal' => 3500.00,
            'grand_total' => 3500.00,
            'created_by' => $this->staff->id,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'sort_order' => 1,
            'item_type' => 'service',
            'item_id' => $this->serviceOilChange->id,
            'item_name' => $this->serviceOilChange->name,
            'quantity' => 1,
            'unit_price' => 3500.00,
            'line_total' => 3500.00,
        ]);

        $response = $this->actingAs($this->staff)->post(route('billing.quotations.create-job-card', $quotation));
        $response->assertRedirect();

        $this->assertEquals('converted', $quotation->fresh()->status);

        $jobCard = JobCard::where('customer_id', $this->corporateCustomer->id)
            ->where('vehicle_id', $this->vehicle2->id)
            ->first();

        $this->assertNotNull($jobCard);
        $this->assertEquals('pending', $jobCard->status);
        $this->assertEquals(1, $jobCard->services()->count());
    }

    /**
     * Test manual direct invoice creation is restricted to Admin only.
     */
    public function test_manual_invoice_creation_restricted_to_admin(): void
    {
        // 1. Staff gets 403 Forbidden
        $response = $this->actingAs($this->staff)->get(route('operations.invoices.create'));
        $response->assertForbidden();

        // 2. Admin can access create form
        $response = $this->actingAs($this->admin)->get(route('operations.invoices.create'));
        $response->assertOk();

        // 3. Admin can store a manual direct invoice
        $payload = [
            'customer_id' => $this->corporateCustomer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subject' => 'Fleet Quarterly Retainer Fee',
            'discount_type' => 'none',
            'discount_value' => 0,
            'items' => [
                [
                    'item_type' => 'custom',
                    'item_name' => 'Corporate Maintenance Retainer',
                    'quantity' => 1,
                    'unit_price' => 25000.00,
                ],
            ],
        ];

        $storeResponse = $this->actingAs($this->admin)->post(route('operations.invoices.store'), $payload);
        $storeResponse->assertRedirect();

        $invoice = Invoice::where('customer_id', $this->corporateCustomer->id)
            ->where('invoice_type', 'manual')
            ->first();

        $this->assertNotNull($invoice);
        $this->assertEquals(25000.00, (float) $invoice->grand_total);
        $this->assertEquals('issued', $invoice->status);
    }

    /**
     * Test recording payments and balance updates.
     */
    public function test_invoice_payment_recording_and_status_transitions(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'invoice_date' => now()->toDateString(),
            'customer_id' => $this->corporateCustomer->id,
            'invoice_type' => 'manual',
            'subtotal' => 10000.00,
            'grand_total' => 10000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 10000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        // 1. Partial payment of 4000
        $this->actingAs($this->staff)->post(route('operations.invoices.payment', $invoice), [
            'amount' => 4000.00,
            'payment_method' => 'card',
            'reference_number' => 'AUTH-449102',
        ])->assertRedirect();

        $invoice->refresh();
        $this->assertEquals('partially_paid', $invoice->status);
        $this->assertEquals(4000.00, (float) $invoice->amount_paid);
        $this->assertEquals(6000.00, (float) $invoice->balance_amount);
        $this->assertEquals(1, $invoice->payments()->count());

        // 2. Final settlement payment of 6000
        $this->actingAs($this->staff)->post(route('operations.invoices.payment', $invoice), [
            'amount' => 6000.00,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'SLIP-99812',
        ])->assertRedirect();

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(10000.00, (float) $invoice->amount_paid);
        $this->assertEquals(0.00, (float) $invoice->balance_amount);
        $this->assertEquals(2, $invoice->payments()->count());
    }

    /**
     * Test PDF download endpoints return valid PDF stream.
     */
    public function test_pdf_generation_endpoints(): void
    {
        $quotation = Quotation::create([
            'quotation_number' => Quotation::generateQuotationNumber(),
            'customer_id' => $this->corporateCustomer->id,
            'vehicle_id' => $this->vehicle1->id,
            'quotation_date' => now()->toDateString(),
            'status' => 'draft',
            'subtotal' => 3500.00,
            'grand_total' => 3500.00,
            'created_by' => $this->staff->id,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'sort_order' => 1,
            'item_type' => 'service',
            'item_id' => $this->serviceOilChange->id,
            'item_name' => $this->serviceOilChange->name,
            'quantity' => 1,
            'unit_price' => 3500.00,
            'line_total' => 3500.00,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'invoice_date' => now()->toDateString(),
            'customer_id' => $this->corporateCustomer->id,
            'invoice_type' => 'manual',
            'subtotal' => 3500.00,
            'grand_total' => 3500.00,
            'amount_paid' => 3500.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        // Quotation PDF
        $quoPdfResponse = $this->actingAs($this->staff)->get(route('billing.quotations.pdf', $quotation));
        $quoPdfResponse->assertOk();
        $quoPdfResponse->assertHeader('content-type', 'application/pdf');

        // Invoice PDF
        $invPdfResponse = $this->actingAs($this->staff)->get(route('operations.invoices.pdf', $invoice));
        $invPdfResponse->assertOk();
        $invPdfResponse->assertHeader('content-type', 'application/pdf');
    }
}
