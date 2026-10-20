<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\DirectSale;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\Inventory\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected Branch $branch;

    protected Branch $otherBranch;

    protected CompanySetting $company;

    protected Customer $individualCustomer;

    protected Customer $companyCustomer;

    protected Vehicle $vehicle;

    protected Product $product;

    protected StockService $stockService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockService = app(StockService::class);
        $this->company = CompanySetting::getSettings();

        // Branches
        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'COL-01',
            'branch_name' => 'Colombo Main Branch',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $this->otherBranch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'KAN-02',
            'branch_name' => 'Kandy Branch',
            'is_main_branch' => false,
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

        // Assign staff to main branch
        $this->staff->branches()->attach($this->branch->id);

        // Vehicle Type
        $carType = VehicleType::create([
            'name' => 'Sedan',
            'status' => 'active',
        ]);

        // Individual Customer
        $this->individualCustomer = Customer::create([
            'customer_name' => 'Sunil Perera',
            'customer_type' => 'individual',
            'phone' => '0711112233',
            'email' => 'sunil@test.com',
            'status' => 'active',
        ]);

        // Company Customer
        $this->companyCustomer = Customer::create([
            'customer_name' => 'Nisal Fernando',
            'customer_type' => 'company',
            'company_name' => 'Lanka Logistics Ltd',
            'phone' => '0771234567',
            'email' => 'fleet@lankalogistics.lk',
            'tax_number' => 'VAT-778899',
            'status' => 'active',
        ]);

        $this->vehicle = Vehicle::create([
            'customer_id' => $this->companyCustomer->id,
            'vehicle_type_id' => $carType->id,
            'vehicle_number' => 'WP-CAB-1001',
            'status' => 'active',
        ]);

        // Unit of Measurement & Product for stock integrity test
        $uom = UnitOfMeasurement::create([
            'name' => 'Units',
            'code' => 'PCS',
        ]);

        $supplier = Supplier::create([
            'name' => 'Auto Parts Direct',
            'mobile' => '0779998877',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'name' => 'Brake Pad Set',
            'sku' => 'BP-001',
            'uom_id' => $uom->id,
            'selling_price' => 6000.00,
            'is_active' => true,
        ]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-TEST-001',
            'branch_id' => $this->branch->id,
            'supplier_id' => $supplier->id,
            'supplier_invoice_number' => 'INV-SUP-01',
            'received_date' => now()->toDateString(),
            'total_amount' => 80000.00,
            'status' => 'confirmed',
            'created_by' => $this->admin->id,
        ]);

        $grnItem = GoodsReceivedNoteItem::create([
            'grn_id' => $grn->id,
            'product_id' => $this->product->id,
            'purchase_quantity' => 20,
            'free_quantity' => 0,
            'purchase_unit_price' => 4000.00,
            'selling_price_snapshot' => $this->product->selling_price,
            'line_total' => 80000.00,
        ]);

        StockBatch::create([
            'product_id' => $this->product->id,
            'supplier_id' => $supplier->id,
            'branch_id' => $this->branch->id,
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem->id,
            'batch_number' => 'BATCH-TEST-01',
            'received_quantity' => 20,
            'remaining_quantity' => 20,
            'original_purchase_unit_price' => 4000.00,
            'inventory_unit_cost' => 4000.00,
            'purchase_date' => now()->toDateString(),
            'status' => 'active',
        ]);
    }

    /**
     * Test 1: Record multiple payments against same invoice.
     * Verifies:
     * - Invoice number unchanged
     * - Partial payment updates Total Paid, Outstanding Balance, and status to Partially Paid
     * - Subsequent payment updates correctly
     * - Final payment marks invoice Paid in Full with 0.00 balance
     * - Payment history contains all payments
     */
    public function test_multiple_payments_against_same_invoice_lifecycle(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0004',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->individualCustomer->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'manual',
            'subtotal' => 32000.00,
            'grand_total' => 32000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 32000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->staff);

        // 1. Initial State: Unpaid, Balance 32,000
        $this->assertEquals('INV-202610-0004', $invoice->invoice_number);
        $this->assertEquals(32000.00, (float) $invoice->balance_amount);
        $this->assertEquals(0.00, (float) $invoice->amount_paid);
        $this->assertEquals('Unpaid', $invoice->payment_status);

        // 2. Customer makes first payment of LKR 12,000 (Cash)
        $response1 = $this->post(route('billing.payments.store', $invoice), [
            'amount' => 12000.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'CASH-REC-01',
            'notes' => 'First installment',
        ]);

        $response1->assertRedirect();
        $invoice->refresh();

        $this->assertEquals('INV-202610-0004', $invoice->invoice_number);
        $this->assertEquals(12000.00, (float) $invoice->amount_paid);
        $this->assertEquals(20000.00, (float) $invoice->balance_amount);
        $this->assertEquals('partially_paid', $invoice->status);
        $this->assertEquals('Partially Paid', $invoice->payment_status);
        $this->assertCount(1, $invoice->payments);
        $this->assertEquals('completed', $invoice->payments->first()->status);

        // 3. Customer makes second payment of LKR 5,000 (Bank Transfer)
        $response2 = $this->post(route('billing.payments.store', $invoice), [
            'amount' => 5000.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'BTR-99882',
            'notes' => 'Second installment via bank',
        ]);

        $response2->assertRedirect();
        $invoice->refresh();

        $this->assertEquals('INV-202610-0004', $invoice->invoice_number);
        $this->assertEquals(17000.00, (float) $invoice->amount_paid);
        $this->assertEquals(15000.00, (float) $invoice->balance_amount);
        $this->assertEquals('partially_paid', $invoice->status);
        $this->assertEquals('Partially Paid', $invoice->payment_status);
        $this->assertCount(2, $invoice->payments);

        // 4. Customer pays remaining balance of LKR 15,000 (Card)
        $response3 = $this->post(route('billing.payments.store', $invoice), [
            'amount' => 15000.00,
            'payment_method' => 'card',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'VISA-4421',
            'notes' => 'Final settlement',
        ]);

        $response3->assertRedirect();
        $invoice->refresh();

        $this->assertEquals('INV-202610-0004', $invoice->invoice_number);
        $this->assertEquals(32000.00, (float) $invoice->amount_paid);
        $this->assertEquals(0.00, (float) $invoice->balance_amount);
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals('Paid in Full', $invoice->payment_status);
        $this->assertCount(3, $invoice->payments);

        // Verify each payment has a unique payment receipt number
        $paymentNumbers = $invoice->payments->pluck('payment_number')->toArray();
        $this->assertCount(3, array_unique($paymentNumbers));
        foreach ($paymentNumbers as $pNum) {
            $this->assertStringStartsWith('PAY-', $pNum);
        }
    }

    /**
     * Test 2: Overpayment prevention.
     * Attempting to pay more than the outstanding balance must be blocked.
     */
    public function test_overpayment_is_strictly_prevented(): void
    {
        $this->actingAs($this->staff);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0005',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->individualCustomer->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'manual',
            'subtotal' => 10000.00,
            'grand_total' => 10000.00,
            'amount_paid' => 6000.00,
            'balance_amount' => 4000.00,
            'status' => 'partially_paid',
            'created_by' => $this->admin->id,
        ]);

        // Attempt to pay 5,000 when balance is only 4,000
        $response = $this->post(route('billing.payments.store', $invoice), [
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        $response->assertSessionHas('error');
        $invoice->refresh();

        // Ensure balance is unchanged
        $this->assertEquals(4000.00, (float) $invoice->balance_amount);
        $this->assertEquals(6000.00, (float) $invoice->amount_paid);
        $this->assertEquals(0, Payment::where('invoice_id', $invoice->id)->count());
    }

    /**
     * Test 3: Paying an already settled or cancelled invoice is rejected.
     */
    public function test_cannot_pay_fully_settled_or_cancelled_invoice(): void
    {
        $this->actingAs($this->staff);

        $paidInvoice = Invoice::create([
            'invoice_number' => 'INV-202610-0006',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->individualCustomer->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'manual',
            'subtotal' => 5000.00,
            'grand_total' => 5000.00,
            'amount_paid' => 5000.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        $responsePaid = $this->post(route('billing.payments.store', $paidInvoice), [
            'amount' => 1000.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        $responsePaid->assertSessionHas('error');

        $cancelledInvoice = Invoice::create([
            'invoice_number' => 'INV-202610-0007',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->individualCustomer->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'manual',
            'subtotal' => 5000.00,
            'grand_total' => 5000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 5000.00,
            'status' => 'cancelled',
            'created_by' => $this->admin->id,
        ]);

        $responseCancelled = $this->post(route('billing.payments.store', $cancelledInvoice), [
            'amount' => 2000.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        $responseCancelled->assertSessionHas('error');
    }

    /**
     * Test 4: Stock quantities and FIFO batches are NEVER altered when recording payments.
     */
    public function test_stock_quantities_and_batches_are_never_altered_by_payments(): void
    {
        $this->actingAs($this->staff);

        $initialBatchRemaining = (float) StockBatch::where('product_id', $this->product->id)->sum('remaining_quantity');

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0008',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->individualCustomer->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'manual',
            'subtotal' => 12000.00,
            'grand_total' => 12000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 12000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        // Record partial payment
        $this->post(route('billing.payments.store', $invoice), [
            'amount' => 6000.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        // Verify stock is identical
        $this->assertEquals($initialBatchRemaining, (float) StockBatch::where('product_id', $this->product->id)->sum('remaining_quantity'));

        // Record remaining payment
        $this->post(route('billing.payments.store', $invoice), [
            'amount' => 6000.00,
            'payment_method' => 'card',
            'payment_date' => now()->toDateString(),
        ]);

        // Verify stock remains untouched
        $this->assertEquals($initialBatchRemaining, (float) StockBatch::where('product_id', $this->product->id)->sum('remaining_quantity'));
    }

    /**
     * Test 5: Payments work for all invoice types (Job Card, Direct Sale, Company Customer).
     */
    public function test_payments_work_across_different_invoice_types(): void
    {
        $this->actingAs($this->staff);

        // 1. Job Card Invoice
        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-9999',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->companyCustomer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicle->vehicle_type_id,
            'date' => now()->toDateString(),
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        $jobInvoice = Invoice::create([
            'invoice_number' => 'INV-202610-0009',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->companyCustomer->id,
            'job_card_id' => $jobCard->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'job_card',
            'subtotal' => 25000.00,
            'grand_total' => 25000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 25000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        $this->post(route('billing.payments.store', $jobInvoice), [
            'amount' => 10000.00,
            'payment_method' => 'cheque',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'CHQ-556677',
        ])->assertRedirect();

        $jobInvoice->refresh();
        $this->assertEquals(10000.00, (float) $jobInvoice->amount_paid);
        $this->assertEquals(15000.00, (float) $jobInvoice->balance_amount);
        $this->assertEquals('partially_paid', $jobInvoice->status);

        // 2. Direct Sale Invoice
        $directSale = DirectSale::create([
            'sale_number' => 'DS-202610-9999',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->individualCustomer->id,
            'sale_date' => now()->toDateString(),
            'subtotal' => 8000.00,
            'grand_total' => 8000.00,
            'amount_paid' => 0.00,
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        $dsInvoice = Invoice::create([
            'invoice_number' => 'INV-202610-0010',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->individualCustomer->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'direct_sale',
            'subtotal' => 8000.00,
            'grand_total' => 8000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 8000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        $this->post(route('billing.payments.store', $dsInvoice), [
            'amount' => 8000.00,
            'payment_method' => 'card',
            'payment_date' => now()->toDateString(),
        ])->assertRedirect();

        $dsInvoice->refresh();
        $this->assertEquals(8000.00, (float) $dsInvoice->amount_paid);
        $this->assertEquals(0.00, (float) $dsInvoice->balance_amount);
        $this->assertEquals('paid', $dsInvoice->status);
    }

    /**
     * Test 6: Payment reversal workflow (Admin only).
     */
    public function test_admin_can_reverse_payment_and_staff_cannot(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0011',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->individualCustomer->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'manual',
            'subtotal' => 20000.00,
            'grand_total' => 20000.00,
            'amount_paid' => 20000.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'customer_id' => $this->individualCustomer->id,
            'payment_number' => Payment::generatePaymentNumber(),
            'payment_date' => now()->toDateString(),
            'amount' => 20000.00,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $this->staff->id,
        ]);

        // Staff tries to reverse payment -> 403 Forbidden
        $this->actingAs($this->staff);
        $responseStaff = $this->post(route('billing.payments.reverse', $payment), [
            'reversal_reason' => 'Customer bounced check',
        ]);
        $responseStaff->assertStatus(403);

        // Admin reverses payment -> Success
        $this->actingAs($this->admin);
        $responseAdmin = $this->post(route('billing.payments.reverse', $payment), [
            'reversal_reason' => 'Wrong amount posted by mistake',
        ]);
        $responseAdmin->assertRedirect();

        $payment->refresh();
        $invoice->refresh();

        $this->assertEquals('reversed', $payment->status);
        $this->assertNotNull($payment->reversed_at);
        $this->assertEquals('Wrong amount posted by mistake', $payment->reversal_reason);

        // Invoice balance is restored back
        $this->assertEquals(0.00, (float) $invoice->amount_paid);
        $this->assertEquals(20000.00, (float) $invoice->balance_amount);
        $this->assertEquals('issued', $invoice->status);
        $this->assertEquals('Unpaid', $invoice->payment_status);
    }

    /**
     * Test 7: Branch isolation and authorization.
     */
    public function test_branch_scoping_prevents_unauthorized_payment_recording(): void
    {
        // Invoice belongs to Kandy branch
        $kandyInvoice = Invoice::create([
            'invoice_number' => 'INV-202610-0012',
            'branch_id' => $this->otherBranch->id,
            'customer_id' => $this->individualCustomer->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'manual',
            'subtotal' => 15000.00,
            'grand_total' => 15000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 15000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        // Staff assigned ONLY to Colombo branch
        $this->actingAs($this->staff);

        $response = $this->post(route('billing.payments.store', $kandyInvoice), [
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test 8: Payments list page and Receipt document rendering.
     */
    public function test_payments_index_and_receipt_views_render_successfully(): void
    {
        $this->actingAs($this->staff);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0013',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->companyCustomer->id,
            'invoice_date' => now()->toDateString(),
            'invoice_type' => 'manual',
            'subtotal' => 18000.00,
            'grand_total' => 18000.00,
            'amount_paid' => 10000.00,
            'balance_amount' => 8000.00,
            'status' => 'partially_paid',
            'created_by' => $this->admin->id,
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'customer_id' => $this->companyCustomer->id,
            'payment_number' => 'PAY-202610-0099',
            'payment_date' => now()->toDateString(),
            'amount' => 10000.00,
            'payment_method' => 'card',
            'reference_number' => 'CARD-1234',
            'status' => 'completed',
            'created_by' => $this->staff->id,
        ]);

        // 1. Dedicated Payments index page
        $indexResponse = $this->get(route('billing.payments.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Invoice Payments');
        $indexResponse->assertSee('INV-202610-0013');
        $indexResponse->assertSee('Lanka Logistics Ltd');
        $indexResponse->assertSee('Record Payment');

        // 2. Official Customer Payment Receipt page
        $receiptResponse = $this->get(route('billing.payments.receipt', $payment));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee('PAY-202610-0099');
        $receiptResponse->assertSee('Official Payment Receipt');
        $receiptResponse->assertSee('10,000.00');
        $receiptResponse->assertSee('INV-202610-0013');

        // 3. Invoice Show page with Payment History
        $showResponse = $this->get(route('operations.invoices.show', $invoice));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Payment History');
        $showResponse->assertSee('PAY-202610-0099');
        $showResponse->assertSee('10,000.00');
    }
}
