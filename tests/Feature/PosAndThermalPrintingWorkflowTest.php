<?php

namespace Tests\Feature;

use App\Mail\InvoiceMail;
use App\Mail\PaymentReceiptMail;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\CustomerCreditAccount;
use App\Models\GoodsReceivedNote;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\PhoneNumberHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PosAndThermalPrintingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Branch $branch;
    protected CompanySetting $company;
    protected VehicleType $vehicleType;
    protected Customer $customerWithEmail;
    protected Customer $customerNoEmail;
    protected Vehicle $vehicle;
    protected Service $service;
    protected Product $product;
    protected StockBatch $batch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = CompanySetting::getSettings();

        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-01',
            'branch_name' => 'AutoCare Main Center',
            'address' => '123 Main St, Colombo',
            'phone' => '0112345678',
            'is_active' => true,
            'is_main_branch' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@autocare.lk',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
            'branch_id' => $this->branch->id,
        ]);

        $this->staff = User::create([
            'name' => 'Test Staff',
            'email' => 'staff@autocare.lk',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
            'branch_id' => $this->branch->id,
        ]);

        $this->vehicleType = VehicleType::firstOrCreate(
            ['name' => 'Sedan'],
            ['category' => 'car', 'is_active' => true]
        );

        $this->customerWithEmail = Customer::create([
            'customer_name' => 'Alice Silva',
            'phone' => '0712345678',
            'email' => 'alice@example.com',
            'is_active' => true,
        ]);

        $this->customerNoEmail = Customer::create([
            'customer_name' => 'Bob Perera',
            'phone' => '0779876543',
            'email' => null,
            'is_active' => true,
        ]);

        $this->vehicle = Vehicle::create([
            'vehicle_number' => 'CAB-4242',
            'customer_id' => $this->customerWithEmail->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'make_model' => 'Honda Grace',
            'current_mileage' => 42000,
            'next_service_mileage' => 47000,
        ]);

        $this->service = Service::create([
            'code' => 'SRV-TUNE',
            'name' => 'Full Engine Tune Up',
            'category' => 'mechanical',
            'status' => 'active',
        ]);

        ServiceVehiclePrice::create([
            'service_id' => $this->service->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'service_price' => 5000.00,
            'service_cost' => 2000.00,
        ]);

        $uom = UnitOfMeasurement::firstOrCreate(
            ['code' => 'CAN'],
            ['name' => 'Cans', 'allows_decimal' => false]
        );

        $supplier = Supplier::create([
            'name' => 'Premier Parts',
            'contact_person' => 'Sunil',
            'phone' => '0771122334',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Synthetic Motor Oil 4L',
            'sku' => 'OIL-SYN-001',
            'uom_id' => $uom->id,
            'selling_price' => 9500.00,
            'is_active' => true,
        ]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-202610-0099',
            'branch_id' => $this->branch->id,
            'supplier_id' => $supplier->id,
            'received_date' => now()->toDateString(),
            'status' => 'confirmed',
            'subtotal' => 95000.00,
            'grand_total' => 95000.00,
            'created_by' => $this->admin->id,
        ]);

        $grnItem = $grn->items()->create([
            'product_id' => $this->product->id,
            'purchase_quantity' => 20,
            'free_quantity' => 0,
            'purchase_unit_price' => 6000.00,
            'selling_price_snapshot' => $this->product->selling_price,
            'line_total' => 120000.00,
        ]);

        $this->batch = StockBatch::create([
            'batch_number' => 'BATCH-202610-0099',
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem->id,
            'product_id' => $this->product->id,
            'branch_id' => $this->branch->id,
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'received_quantity' => 20,
            'remaining_quantity' => 20,
            'original_purchase_unit_price' => 6000.00,
            'current_cost_per_unit' => 6000.00,
            'inventory_unit_cost' => 6000.00,
            'status' => 'active',
        ]);
    }

    /**
     * Test phone number WhatsApp normalizer helper.
     */
    public function test_phone_number_helper_normalizes_sri_lankan_formats(): void
    {
        $this->assertEquals('94712345678', PhoneNumberHelper::normalizeForWhatsApp('0712345678'));
        $this->assertEquals('94712345678', PhoneNumberHelper::normalizeForWhatsApp('+94712345678'));
        $this->assertEquals('94712345678', PhoneNumberHelper::normalizeForWhatsApp('94712345678'));
        $this->assertEquals('94771234567', PhoneNumberHelper::normalizeForWhatsApp('077 123 4567'));
        $this->assertEquals('94112345678', PhoneNumberHelper::normalizeForWhatsApp('0112345678'));
        $this->assertNull(PhoneNumberHelper::normalizeForWhatsApp(''));
    }

    /**
     * Test vehicle registration handles optional email and preserves leading zero in phone.
     */
    public function test_vehicle_registration_supports_optional_email_and_preserves_phone_leading_zero(): void
    {
        $response = $this->actingAs($this->admin)->post(route('operations.vehicles.store'), [
            'vehicle_number' => 'WP-CAD-9999',
            'vehicle_type_id' => $this->vehicleType->id,
            'make_model' => 'Suzuki Swift',
            'current_mileage' => 25000,
            'customer_name' => 'Nimal Jaya',
            'phone' => '0719876543',
            'email' => 'nimal@example.com',
            'customer_type' => 'individual',
        ]);

        $response->assertRedirect();
        
        $customer = Customer::where('phone', '0719876543')->first();
        $this->assertNotNull($customer);
        $this->assertEquals('0719876543', $customer->phone);
        $this->assertEquals('nimal@example.com', $customer->email);

        // Register another vehicle without email
        $response2 = $this->actingAs($this->admin)->post(route('operations.vehicles.store'), [
            'vehicle_number' => 'WP-CBD-8888',
            'vehicle_type_id' => $this->vehicleType->id,
            'make_model' => 'Toyota Vitz',
            'current_mileage' => 15000,
            'customer_name' => 'Kamal Jaya',
            'phone' => '0721234567',
            'customer_type' => 'individual',
        ]);

        $response2->assertRedirect();
        $customer2 = Customer::where('phone', '0721234567')->first();
        $this->assertNotNull($customer2);
        $this->assertEquals('0721234567', $customer2->phone);
        $this->assertNull($customer2->email);
    }

    /**
     * Test POS direct sale supports cheque payment method and returns thermal_url.
     */
    public function test_pos_direct_sale_supports_cheque_and_returns_thermal_url(): void
    {
        $payload = [
            'customer_id' => $this->customerWithEmail->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'unit_price' => 9500.00,
                ],
            ],
            'discount_amount' => 500.00,
            'payment_method' => 'cheque',
            'reference_number' => 'CHQ-888123',
            'paid_amount' => 18500.00,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('operations.direct-sales.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $data = $response->json();
        $this->assertArrayHasKey('thermal_url', $data);
        $this->assertStringContainsString('/thermal', $data['thermal_url']);

        // Check invoice and payments
        $invoice = Invoice::find($data['invoice_id']);
        $this->assertNotNull($invoice);
        $this->assertEquals('Paid in Full', $invoice->payment_status);
        $this->assertEquals(18500.00, (float)$invoice->amount_paid);

        // Check stock batch FIFO reduction
        $this->batch->refresh();
        $this->assertEquals(18, $this->batch->remaining_quantity);
    }

    /**
     * Test 80mm thermal sales receipt view.
     */
    public function test_80mm_thermal_sales_receipt_renders_successfully(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-TEST80',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerWithEmail->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 19000.00,
            'discount_amount' => 1000.00,
            'grand_total' => 18000.00,
            'amount_paid' => 18000.00,
            'balance_amount' => 0.00,
            'status' => 'issued',
            'payment_status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        $invoice->items()->create([
            'item_type' => 'product',
            'item_name' => 'Synthetic Motor Oil 4L',
            'quantity' => 2,
            'unit_price' => 9500.00,
            'subtotal' => 19000.00,
            'total' => 19000.00,
        ]);

        $response = $this->actingAs($this->admin)->get(route('operations.invoices.thermal', $invoice));

        $response->assertStatus(200);
        $response->assertSee('80mm auto');
        $response->assertSee('SALES RECEIPT');
        $response->assertSee('INV-202610-TEST80');
        $response->assertSee('Synthetic Motor Oil 4L');
        $response->assertSee('18,000.00');
    }

    /**
     * Test 80mm thermal payment receipt view.
     */
    public function test_80mm_thermal_payment_receipt_renders_successfully(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-PAY80',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerWithEmail->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'subtotal' => 20000.00,
            'grand_total' => 20000.00,
            'amount_paid' => 10000.00,
            'balance_amount' => 10000.00,
            'status' => 'issued',
            'payment_status' => 'partially_paid',
            'created_by' => $this->admin->id,
        ]);

        $payment = Payment::create([
            'payment_number' => 'PAY-202610-0080',
            'invoice_id' => $invoice->id,
            'customer_id' => $this->customerWithEmail->id,
            'branch_id' => $this->branch->id,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'card',
            'amount' => 10000.00,
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('billing.payments.thermal', $payment));

        $response->assertStatus(200);
        $response->assertSee('PAYMENT RECEIPT');
        $response->assertSee('PAY-202610-0080');
        $response->assertSee('INV-202610-PAY80');
        $response->assertSee('10,000.00');
        $response->assertSee('CARD');
    }

    /**
     * Test 80mm pending job card printing without invoice generation.
     */
    public function test_80mm_job_card_print_works_in_pending_status_without_invoicing(): void
    {
        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-PEND',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerWithEmail->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'pending',
            'current_mileage' => 42000,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('operations.job-cards.print', $jobCard));

        $response->assertStatus(200);
        $response->assertSee('JOB-202610-PEND');
        $response->assertSee('CAB-4242');
        $response->assertSee('80mm auto');

        // Verify status remains pending and no invoice was created
        $jobCard->refresh();
        $this->assertEquals('pending', $jobCard->status);
        $this->assertNull($jobCard->invoice_id);
    }

    /**
     * Test technician reassignment logs activity.
     */
    public function test_technician_reassignment_logged_in_activity(): void
    {
        $tech1 = User::create([
            'name' => 'Technician Alpha',
            'email' => 'alpha@autocare.lk',
            'password' => bcrypt('password'),
            'role' => 'technician',
            'status' => 'active',
            'branch_id' => $this->branch->id,
        ]);

        $tech2 = User::create([
            'name' => 'Technician Beta',
            'email' => 'beta@autocare.lk',
            'password' => bcrypt('password'),
            'role' => 'technician',
            'status' => 'active',
            'branch_id' => $this->branch->id,
        ]);

        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-TECH',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerWithEmail->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'pending',
            'assigned_to' => $tech1->id,
            'created_by' => $this->admin->id,
        ]);

        // Reassign to tech2
        $response = $this->actingAs($this->admin)->put(route('operations.job-cards.update', $jobCard), [
            'assigned_to' => $tech2->id,
            'current_mileage' => 42000,
        ]);

        $response->assertRedirect();
        $jobCard->refresh();
        $this->assertEquals($tech2->id, $jobCard->assigned_to);

        // Check activity log
        $log = ActivityLog::where('action', 'technician_reassigned')
            ->where('description', 'like', "%{$jobCard->job_number}%")
            ->first();
        
        $this->assertNotNull($log);
        $this->assertStringContainsString('Technician Beta', $log->description);
    }

    /**
     * Test start work and complete job audit logging.
     */
    public function test_start_work_and_complete_job_records_audit_trail(): void
    {
        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-AUDIT',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerWithEmail->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'pending',
            'created_by' => $this->admin->id,
        ]);

        // Start work
        $this->actingAs($this->staff)->patch(route('operations.job-cards.status', $jobCard), [
            'action' => 'start_work',
        ]);

        $jobCard->refresh();
        $this->assertEquals('in_progress', $jobCard->status);

        $startLog = ActivityLog::where('action', 'started_work')
            ->where('description', 'like', "%{$jobCard->job_number}%")
            ->first();
        $this->assertNotNull($startLog);

        // Complete job
        $this->actingAs($this->staff)->patch(route('operations.job-cards.status', $jobCard), [
            'action' => 'complete_job',
        ]);

        $jobCard->refresh();
        $this->assertEquals('completed', $jobCard->status);

        $completeLog = ActivityLog::where('action', 'completed_job')
            ->where('description', 'like', "%{$jobCard->job_number}%")
            ->first();
        $this->assertNotNull($completeLog);
    }

    /**
     * Test vehicle delivery release policies (unpaid restrictions, admin override, paid release).
     */
    public function test_vehicle_delivery_policy_enforcement(): void
    {
        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-DELIV',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerWithEmail->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'completed',
            'total_amount' => 15000.00,
            'created_by' => $this->admin->id,
        ]);

        // Create an unpaid invoice
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-DELIV',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerWithEmail->id,
            'job_card_id' => $jobCard->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 15000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 15000.00,
            'status' => 'issued',
            'payment_status' => 'unpaid',
            'created_by' => $this->admin->id,
        ]);

        $jobCard->update(['invoice_id' => $invoice->id]);

        // 1. Staff attempts delivery for unpaid vehicle -> Should fail
        $resStaff = $this->actingAs($this->staff)->patch(route('operations.job-cards.status', $jobCard), [
            'action' => 'mark_delivered',
        ]);
        $resStaff->assertSessionHas('error');
        $jobCard->refresh();
        $this->assertEquals('completed', $jobCard->status);

        // 2. Admin attempts delivery without override reason -> Returns error in session
        $resAdminNoReason = $this->actingAs($this->admin)->patch(route('operations.job-cards.status', $jobCard), [
            'action' => 'mark_delivered',
        ]);
        $resAdminNoReason->assertSessionHas('error');
        $this->assertStringContainsString('override reason is required', session('error'));
        $jobCard->refresh();
        $this->assertEquals('completed', $jobCard->status);

        // 3. Admin provides override reason -> Success & audit logged
        $resAdminOverride = $this->actingAs($this->admin)->patch(route('operations.job-cards.status', $jobCard), [
            'action' => 'mark_delivered',
            'delivery_override_reason' => 'Authorized by Branch Manager - payment promised tomorrow',
        ]);
        $resAdminOverride->assertSessionHas('success');
        $jobCard->refresh();
        $this->assertEquals('delivered', $jobCard->status);

        $overrideLog = ActivityLog::where('action', 'delivered_vehicle')
            ->where('description', 'like', "%{$jobCard->job_number}%")
            ->first();
        $this->assertNotNull($overrideLog);
        $this->assertStringContainsString('Authorized by Branch Manager', $overrideLog->description);
    }

    /**
     * Test company vehicle credit release.
     */
    public function test_company_vehicle_credit_allows_delivery(): void
    {
        // Setup credit account for customer
        CustomerCreditAccount::create([
            'customer_id' => $this->customerWithEmail->id,
            'credit_limit' => 100000.00,
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-CREDIT',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerWithEmail->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'completed',
            'total_amount' => 15000.00,
            'created_by' => $this->admin->id,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-CREDIT',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerWithEmail->id,
            'job_card_id' => $jobCard->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 15000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 15000.00,
            'status' => 'issued',
            'payment_status' => 'unpaid',
            'created_by' => $this->admin->id,
        ]);

        $jobCard->update(['invoice_id' => $invoice->id]);

        // Staff can deliver directly because customer has active credit limit
        $res = $this->actingAs($this->staff)->patch(route('operations.job-cards.status', $jobCard), [
            'action' => 'mark_delivered',
        ]);

        $res->assertSessionHas('success');
        $jobCard->refresh();
        $this->assertEquals('delivered', $jobCard->status);
    }

    /**
     * Test email invoice and payment receipt dispatching.
     */
    public function test_email_invoice_and_payment_receipt(): void
    {
        Mail::fake();

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-MAIL',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerWithEmail->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 12000.00,
            'amount_paid' => 12000.00,
            'balance_amount' => 0.00,
            'status' => 'issued',
            'payment_status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        // Send Invoice email
        $res = $this->actingAs($this->admin)->post(route('operations.invoices.email', $invoice));
        $res->assertSessionHas('success');
        Mail::assertSent(InvoiceMail::class, function ($mail) {
            return $mail->hasTo('alice@example.com');
        });

        // Test invoice without customer email
        $invoiceNoMail = Invoice::create([
            'invoice_number' => 'INV-202610-NOMAIL',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customerNoEmail->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 8000.00,
            'amount_paid' => 8000.00,
            'balance_amount' => 0.00,
            'status' => 'issued',
            'payment_status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        $resNoMail = $this->actingAs($this->admin)->post(route('operations.invoices.email', $invoiceNoMail));
        $resNoMail->assertSessionHas('error');
        $this->assertEquals('No email address registered for this customer.', session('error'));

        // Payment receipt email
        $payment = Payment::create([
            'payment_number' => 'PAY-202610-MAIL',
            'invoice_id' => $invoice->id,
            'customer_id' => $this->customerWithEmail->id,
            'branch_id' => $this->branch->id,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cash',
            'amount' => 12000.00,
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        $resPayMail = $this->actingAs($this->admin)->post(route('billing.payments.email', $payment));
        $resPayMail->assertSessionHas('success');
        Mail::assertSent(PaymentReceiptMail::class, function ($mail) {
            return $mail->hasTo('alice@example.com');
        });
    }
}
