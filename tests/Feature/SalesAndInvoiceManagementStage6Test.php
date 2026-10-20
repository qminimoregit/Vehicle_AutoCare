<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\DirectSale;
use App\Models\DirectSaleItem;
use App\Models\GoodsReceivedNote;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobCard;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\Inventory\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesAndInvoiceManagementStage6Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected Branch $branch;

    protected Customer $customer;

    protected Vehicle $vehicle;

    protected Product $product;

    protected StockBatch $batch1;

    protected StockBatch $batch2;

    protected StockService $stockService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockService = app(StockService::class);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->staff = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        $company = CompanySetting::getSettings();

        $this->branch = Branch::create([
            'company_id' => $company->id,
            'branch_name' => 'Main Center',
            'branch_code' => 'MAIN',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $uom = UnitOfMeasurement::create([
            'name' => 'Liter',
            'abbreviation' => 'L',
        ]);

        $supplier = Supplier::create([
            'name' => 'Lanka Lubricants',
            'contact_person' => 'Sunil',
            'phone' => '0771234567',
        ]);

        $this->product = Product::create([
            'name' => 'Engine Oil 10W-40',
            'sku' => 'OIL-10W40',
            'uom_id' => $uom->id,
            'selling_price' => 1200.00,
            'status' => 'active',
        ]);

        // Create two FIFO batches using proper GRN relationships
        $this->batch1 = $this->createBatch($this->product, $supplier, 10, 800.00, 'B001', now()->subDays(10)->toDateString());
        $this->batch2 = $this->createBatch($this->product, $supplier, 10, 950.00, 'B002', now()->subDays(5)->toDateString());

        $this->customer = Customer::create([
            'customer_name' => 'ABC Transport',
            'phone' => '0719876543',
            'customer_type' => 'company',
            'company_name' => 'ABC Transport PVT',
            'status' => 'active',
        ]);

        $vType = VehicleType::create([
            'name' => 'Van',
        ]);

        $this->vehicle = Vehicle::create([
            'customer_id' => $this->customer->id,
            'vehicle_type_id' => $vType->id,
            'vehicle_number' => 'WP-CAB-5544',
            'vehicle_number_normalized' => 'WPCAB5544',
        ]);
    }

    /**
     * Test 1-7: Avoid Direct Sale and FIFO stock restoration.
     */
    public function test_admin_can_avoid_direct_sale_and_restore_fifo_stock_batches(): void
    {
        // 1. Issue 12 units (10 from B001, 2 from B002)
        $issueResult = $this->stockService->issueStockFifo(
            $this->product,
            12.0,
            'direct_sale',
            null,
            $this->admin
        );

        $this->assertEquals(0, (float) $this->batch1->fresh()->remaining_quantity);
        $this->assertEquals(8, (float) $this->batch2->fresh()->remaining_quantity);

        // Create Direct Sale and Invoice
        $directSale = DirectSale::create([
            'sale_number' => 'DS-202610-0099',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'subtotal' => 14400.00,
            'grand_total' => 14400.00,
            'amount_paid' => 14400.00,
            'change_amount' => 0.00,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        $item = DirectSaleItem::create([
            'direct_sale_id' => $directSale->id,
            'product_id' => $this->product->id,
            'product_sku' => $this->product->sku,
            'product_name' => $this->product->name,
            'quantity' => 12.0,
            'unit_price' => 1200.00,
            'line_total' => 14400.00,
            'batch_allocations' => $issueResult['batches'],
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0099',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'direct_sale',
            'reference' => $directSale->sale_number,
            'subtotal' => 14400.00,
            'grand_total' => 14400.00,
            'amount_paid' => 14400.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'customer_id' => $this->customer->id,
            'payment_number' => 'PAY-202610-0099',
            'payment_date' => now()->toDateString(),
            'amount' => 14400.00,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        $directSale->update(['invoice_id' => $invoice->id]);

        // Search Avoid Direct Sale
        $searchRes = $this->actingAs($this->admin)->get(route('billing.avoid-direct-sale.index', ['search' => 'INV-202610-0099']));
        $searchRes->assertOk();
        $searchRes->assertSee('DS-202610-0099');
        $searchRes->assertSee('LKR 14,400.00');

        // Cancel sale with physical return of stock
        $cancelRes = $this->actingAs($this->admin)->post(route('billing.avoid-direct-sale.cancel', $directSale), [
            'cancellation_reason' => 'Customer returned incorrect grade oil',
            'confirm_avoid' => 1,
            'restore_stock' => 1,
        ]);

        $cancelRes->assertRedirect();
        $cancelRes->assertSessionHas('success');

        // Verify Direct Sale status
        $this->assertEquals('cancelled', $directSale->fresh()->status);
        $this->assertTrue($directSale->fresh()->stock_reversed);
        $this->assertEquals('Customer returned incorrect grade oil', $directSale->fresh()->cancellation_reason);

        // Verify Invoice status & financial reversal
        $freshInv = $invoice->fresh();
        $this->assertEquals('cancelled', $freshInv->status);
        $this->assertEquals(0.0, (float) $freshInv->amount_paid);
        $this->assertEquals(0.0, (float) $freshInv->balance_amount);

        // Verify Payment reversed
        $this->assertEquals('reversed', $payment->fresh()->status);

        // Verify exact FIFO batch restoration: 10 units back to B001, 2 units back to B002
        $this->assertEquals(10.0, (float) $this->batch1->fresh()->remaining_quantity);
        $this->assertEquals(10.0, (float) $this->batch2->fresh()->remaining_quantity);

        // Verify StockMovement audit
        $reversalMovements = StockMovement::where('movement_type', 'issue_reversal')->get();
        $this->assertCount(2, $reversalMovements);

        // Prevent double cancellation
        $dupRes = $this->actingAs($this->admin)->post(route('billing.avoid-direct-sale.cancel', $directSale), [
            'cancellation_reason' => 'Duplicate attempt',
            'confirm_avoid' => 1,
        ]);
        $dupRes->assertSessionHas('error');
    }

    /**
     * Test staff cannot avoid direct sale.
     */
    public function test_staff_cannot_avoid_direct_sale(): void
    {
        $directSale = DirectSale::create([
            'sale_number' => 'DS-202610-0098',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'subtotal' => 5000.00,
            'grand_total' => 5000.00,
            'amount_paid' => 5000.00,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->staff)->post(route('billing.avoid-direct-sale.cancel', $directSale), [
            'cancellation_reason' => 'Staff trying to cancel',
            'confirm_avoid' => 1,
        ]);

        $response->assertForbidden();
        $this->assertEquals('completed', $directSale->fresh()->status);
    }

    /**
     * Test 8-14: Avoid Service Sale preserving vehicle history and Job Card.
     */
    public function test_admin_can_avoid_service_sale_while_preserving_vehicle_and_job_card(): void
    {
        // Create Job Card
        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-0050',
            'branch_id' => $this->branch->id,
            'vehicle_id' => $this->vehicle->id,
            'customer_id' => $this->customer->id,
            'vehicle_type_id' => $this->vehicle->vehicle_type_id,
            'date' => now()->toDateString(),
            'status' => 'completed',
            'service_total' => 8000.00,
            'product_total' => 2400.00,
            'grand_total' => 10400.00,
            'created_by' => $this->admin->id,
        ]);

        // Create Service Invoice
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0050',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'job_card_id' => $jobCard->id,
            'invoice_type' => 'job_card',
            'reference' => $jobCard->job_number,
            'invoice_date' => now()->toDateString(),
            'subtotal' => 10400.00,
            'grand_total' => 10400.00,
            'amount_paid' => 10400.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'service',
            'item_name' => 'Full Lubrication Service',
            'quantity' => 1,
            'unit_price' => 8000.00,
            'line_total' => 8000.00,
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'customer_id' => $this->customer->id,
            'payment_number' => 'PAY-202610-0050',
            'payment_date' => now()->toDateString(),
            'amount' => 10400.00,
            'payment_method' => 'card',
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        $jobCard->update(['invoice_id' => $invoice->id]);

        // Search Avoid Service Sale by Vehicle Number
        $searchRes = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index', ['search' => 'WP-CAB-5544']));
        $searchRes->assertOk();
        $searchRes->assertSee('INV-202610-0050');
        $searchRes->assertSee('JOB-202610-0050');

        // Cancel Service Sale
        $cancelRes = $this->actingAs($this->admin)->post(route('billing.avoid-service-sale.cancel', $invoice), [
            'cancellation_reason' => 'Service billed under wrong corporate account',
            'confirm_avoid' => 1,
            'restore_parts_stock' => 0,
        ]);

        $cancelRes->assertRedirect();
        $cancelRes->assertSessionHas('success');

        // 1. Verify Invoice is cancelled and financial balances reversed
        $freshInv = $invoice->fresh();
        $this->assertEquals('cancelled', $freshInv->status);
        $this->assertEquals(0.0, (float) $freshInv->amount_paid);
        $this->assertEquals(0.0, (float) $freshInv->balance_amount);
        $this->assertEquals('reversed', $payment->fresh()->status);

        // 2. CRITICAL: Vehicle is NOT deleted
        $this->assertDatabaseHas('vehicles', [
            'id' => $this->vehicle->id,
            'vehicle_number' => 'WP-CAB-5544',
        ]);

        // 3. CRITICAL: Job Card is NOT deleted, service history preserved
        $this->assertDatabaseHas('job_cards', [
            'id' => $jobCard->id,
            'job_number' => 'JOB-202610-0050',
        ]);
        $this->assertStringContainsString('Billing cancelled', $jobCard->fresh()->service_notes);
    }

    /**
     * Test 15-19: Invoice Date Change authorized updates and audit history.
     */
    public function test_admin_can_change_invoice_date_and_record_audit_log(): void
    {
        $originalDate = now()->subDays(5)->toDateString();
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0070',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => $originalDate,
            'grand_total' => 5000.00,
            'balance_amount' => 5000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        $newDate = now()->subDays(2)->toDateString();

        $response = $this->actingAs($this->admin)->post(route('billing.invoice-date-change.update', $invoice), [
            'new_invoice_date' => $newDate,
            'reason' => 'Corrected date per customer month-end reconciliation',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fresh = $invoice->fresh();
        $this->assertEquals($newDate, $fresh->invoice_date->toDateString());
        $this->assertEquals($originalDate, $fresh->original_invoice_date->toDateString());
        $this->assertEquals($this->admin->id, $fresh->date_changed_by);
        $this->assertEquals('Corrected date per customer month-end reconciliation', $fresh->date_change_reason);
    }

    /**
     * Test 20: Split Invoice Payment across multiple methods.
     */
    public function test_can_split_invoice_payment_across_multiple_methods(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0080',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 15000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 15000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        // Split: Cash 5,000 + Card 6,000 + Bank 4,000 = 15,000
        $response = $this->actingAs($this->admin)->post(route('billing.multiple-invoice-payment.split'), [
            'invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'payments' => [
                ['payment_method' => 'cash', 'amount' => 5000.00, 'reference_number' => 'CASH-REC'],
                ['payment_method' => 'card', 'amount' => 6000.00, 'reference_number' => 'CARD-9874'],
                ['payment_method' => 'bank_transfer', 'amount' => 4000.00, 'reference_number' => 'TXN-BANK-11'],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fresh = $invoice->fresh();
        $this->assertEquals(15000.00, (float) $fresh->amount_paid);
        $this->assertEquals(0.00, (float) $fresh->balance_amount);
        $this->assertEquals('paid', $fresh->status);

        // 3 Payment records created
        $this->assertEquals(3, $invoice->payments()->count());
    }

    /**
     * Test 21-25: Pay Multiple Invoices with one single customer payment receipt.
     */
    public function test_can_allocate_single_payment_to_multiple_invoices(): void
    {
        $inv1 = Invoice::create([
            'invoice_number' => 'INV-202610-0101',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->subDays(3)->toDateString(),
            'grand_total' => 10000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 10000.00,
            'status' => 'issued',
        ]);

        $inv2 = Invoice::create([
            'invoice_number' => 'INV-202610-0102',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->subDays(2)->toDateString(),
            'grand_total' => 15000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 15000.00,
            'status' => 'issued',
        ]);

        // Customer pays 20,000 total:
        // Allocate: 10,000 to inv1 (fully paid), 10,000 to inv2 (5,000 outstanding)
        $response = $this->actingAs($this->admin)->post(route('billing.multiple-invoice-payment.bulk'), [
            'customer_id' => $this->customer->id,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
            'reference_number' => 'CHQ-778899',
            'total_received' => 20000.00,
            'notes' => 'Corporate fleet settlement',
            'allocations' => [
                ['invoice_id' => $inv1->id, 'allocated_amount' => 10000.00],
                ['invoice_id' => $inv2->id, 'allocated_amount' => 10000.00],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify Receipt created
        $receipt = PaymentReceipt::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($receipt);
        $this->assertEquals(20000.00, (float) $receipt->total_amount);
        $this->assertEquals(2, $receipt->allocations()->count());

        // Verify Inv 1 is fully paid
        $fresh1 = $inv1->fresh();
        $this->assertEquals(10000.00, (float) $fresh1->amount_paid);
        $this->assertEquals(0.00, (float) $fresh1->balance_amount);
        $this->assertEquals('paid', $fresh1->status);

        // Verify Inv 2 is partially paid
        $fresh2 = $inv2->fresh();
        $this->assertEquals(10000.00, (float) $fresh2->amount_paid);
        $this->assertEquals(5000.00, (float) $fresh2->balance_amount);
        $this->assertEquals('partially_paid', $fresh2->status);
    }

    /**
     * Test Avoid Direct Sale without stock restoration (e.g. items damaged/consumed).
     */
    public function test_avoid_direct_sale_without_restoring_stock(): void
    {
        $this->stockService->issueStockFifo($this->product, 5.0, 'direct_sale', null, $this->admin);

        $directSale = DirectSale::create([
            'sale_number' => 'DS-202610-0088',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'subtotal' => 6000.00,
            'grand_total' => 6000.00,
            'amount_paid' => 6000.00,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $beforeB1 = (float) $this->batch1->fresh()->remaining_quantity;

        // Cancel with restore_stock = 0
        $response = $this->actingAs($this->admin)->post(route('billing.avoid-direct-sale.cancel', $directSale), [
            'cancellation_reason' => 'Goods damaged by customer, cannot be resold',
            'confirm_avoid' => 1,
            'restore_stock' => 0,
        ]);

        $response->assertRedirect();
        $this->assertEquals('cancelled', $directSale->fresh()->status);
        $this->assertFalse((bool) $directSale->fresh()->stock_reversed);
        // Batch remaining quantity should NOT have changed
        $this->assertEquals($beforeB1, (float) $this->batch1->fresh()->remaining_quantity);
    }

    /**
     * Test Invoice Date Change rejects future dates and cancelled invoices.
     */
    public function test_invoice_date_change_validations_and_restrictions(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0071',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 5000.00,
            'balance_amount' => 5000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        // Future date should fail validation
        $futureResponse = $this->actingAs($this->admin)->post(route('billing.invoice-date-change.update', $invoice), [
            'new_invoice_date' => now()->addDays(5)->toDateString(),
            'reason' => 'Future date attempt',
        ]);
        $futureResponse->assertSessionHasErrors(['new_invoice_date']);

        // Cancel invoice and verify date cannot be changed
        $invoice->update(['status' => 'cancelled']);
        $cancelledResponse = $this->actingAs($this->admin)->post(route('billing.invoice-date-change.update', $invoice), [
            'new_invoice_date' => now()->subDay()->toDateString(),
            'reason' => 'Attempting on cancelled invoice',
        ]);
        $cancelledResponse->assertSessionHas('error');
    }

    /**
     * Test Split Payment prevents overpayment.
     */
    public function test_split_payment_prevents_overpayment(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0081',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 5000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 5000.00,
            'status' => 'issued',
        ]);

        // Attempt to pay 6000 on a 5000 balance
        $response = $this->actingAs($this->admin)->post(route('billing.multiple-invoice-payment.split'), [
            'invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'payments' => [
                ['payment_method' => 'cash', 'amount' => 4000.00],
                ['payment_method' => 'card', 'amount' => 2000.00],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0.00, (float) $invoice->fresh()->amount_paid);
    }

    /**
     * Test all Stage 6 screen UI pages load cleanly.
     */
    public function test_all_stage6_screens_render_successfully(): void
    {
        $res1 = $this->actingAs($this->admin)->get(route('billing.avoid-direct-sale.index'));
        $res1->assertOk();
        $this->assertStringContainsString('Avoid Direct Sale', $res1->getContent());

        $res2 = $this->actingAs($this->admin)->get(route('billing.avoid-service-sale.index'));
        $res2->assertOk();
        $this->assertStringContainsString('Avoid Service Sale', $res2->getContent());

        $res3 = $this->actingAs($this->admin)->get(route('billing.invoice-date-change.index'));
        $res3->assertOk();
        $this->assertStringContainsString('Invoice Date Change', $res3->getContent());

        $res4 = $this->actingAs($this->admin)->get(route('billing.multiple-invoice-payment.index'));
        $res4->assertOk();
        $this->assertStringContainsString('Multiple Invoice Payment', $res4->getContent());
    }

    /**
     * Test Sidebar Organization:
     * - Sales & Billing contains ONLY Quotations and Invoices.
     * - Invoice Adjustments contains the 4 adjustment features.
     */
    public function test_sidebar_organization_separates_sales_billing_and_invoice_adjustments(): void
    {
        $response = $this->actingAs($this->admin)->get(route('billing.invoices.index'));
        $response->assertOk();

        // Must see both section headers
        $response->assertSeeText('Sales & Billing');
        $response->assertSeeText('Invoice Adjustments');

        // Verify all 4 adjustment items are present under Invoice Adjustments
        $response->assertSee('Avoid Direct Sale');
        $response->assertSee('Avoid Service Sale');
        $response->assertSee('Invoice Date Change');
        $response->assertSee('Multiple Invoice Payment');

        // When visiting an adjustment route, verify activeSection initializes to 'adjustments'
        $adjResponse = $this->actingAs($this->admin)->get(route('billing.avoid-direct-sale.index'));
        $adjResponse->assertOk();
        $adjResponse->assertSee("activeSection: 'adjustments'", false);
    }

    protected function createBatch(Product $product, Supplier $supplier, float $quantity, float $cost, string $batchNo, string $purchaseDate): StockBatch
    {
        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-'.uniqid(),
            'branch_id' => $this->branch->id,
            'supplier_id' => $supplier->id,
            'received_date' => $purchaseDate,
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
            'purchase_date' => $purchaseDate,
            'received_quantity' => $quantity,
            'remaining_quantity' => $quantity,
            'original_purchase_unit_price' => $cost,
            'inventory_unit_cost' => $cost,
            'status' => 'active',
        ]);
    }
}
