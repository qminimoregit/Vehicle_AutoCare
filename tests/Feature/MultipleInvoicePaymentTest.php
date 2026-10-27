<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\CustomerCreditAccount;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultipleInvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected Branch $branch1;

    protected Branch $branch2;

    protected Customer $customer;

    protected Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $company = CompanySetting::getSettings();

        $this->branch1 = Branch::create([
            'company_id' => $company->id,
            'branch_name' => 'Colombo Main',
            'branch_code' => 'CMB',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $this->branch2 = Branch::create([
            'company_id' => $company->id,
            'branch_name' => 'Kandy Branch',
            'branch_code' => 'KDY',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->staff = User::create([
            'name' => 'Staff Colombo',
            'email' => 'staff@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
            'accessible_branch_id' => $this->branch1->id,
            'permissions' => ['record_payments', 'view_invoices'],
        ]);

        $this->customer = Customer::create([
            'customer_name' => 'ABC Transport',
            'company_name' => 'ABC Logistics Ltd',
            'customer_type' => 'company',
            'phone' => '0771234567',
            'status' => 'active',
        ]);

        $vType = VehicleType::firstOrCreate(['name' => 'Van'], ['status' => 'active']);
        $vehicle = Vehicle::create([
            'vehicle_number' => 'WP-CAD-1020',
            'customer_id' => $this->customer->id,
            'vehicle_type_id' => $vType->id,
            'status' => 'active',
        ]);

        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-0010',
            'branch_id' => $this->branch1->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $vType->id,
            'date' => now()->toDateString(),
            'status' => 'invoiced',
            'created_by' => $this->admin->id,
        ]);

        $this->invoice = Invoice::create([
            'invoice_number' => 'INV-202610-0013',
            'branch_id' => $this->branch1->id,
            'customer_id' => $this->customer->id,
            'job_card_id' => $jobCard->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 20000.00,
            'amount_paid' => 5000.00,
            'balance_amount' => 15000.00,
            'status' => 'partially_paid',
            'created_by' => $this->admin->id,
        ]);
    }

    /**
     * Test 1: Invoice search normalization supports leading '#', whitespace, and case variations.
     */
    public function test_invoice_search_normalizes_hash_prefix_and_whitespace(): void
    {
        // 1. Search with leading '#'
        $resHash = $this->actingAs($this->admin)->getJson(
            route('billing.multiple-invoice-payment.search-invoice', ['number' => '#INV-202610-0013'])
        );
        $resHash->assertStatus(200);
        $resHash->assertJson([
            'success' => true,
            'invoice' => [
                'id' => $this->invoice->id,
                'invoice_number' => 'INV-202610-0013',
                'customer_name' => 'ABC Transport',
                'grand_total' => 20000.00,
                'amount_paid' => 5000.00,
                'balance_amount' => 15000.00,
                'status' => 'partially_paid',
            ],
        ]);

        // 2. Search without '#'
        $resPlain = $this->actingAs($this->admin)->getJson(
            route('billing.multiple-invoice-payment.search-invoice', ['number' => 'INV-202610-0013'])
        );
        $resPlain->assertStatus(200);
        $resPlain->assertJsonPath('invoice.id', $this->invoice->id);

        // 3. Search with whitespace and lowercase
        $resSpaces = $this->actingAs($this->admin)->getJson(
            route('billing.multiple-invoice-payment.search-invoice', ['number' => '  #inv-202610-0013  '])
        );
        $resSpaces->assertStatus(200);
        $resSpaces->assertJsonPath('invoice.id', $this->invoice->id);
    }

    /**
     * Test 2: Invoice search handles nonexistent, cancelled, and settled invoices with distinct error messages.
     */
    public function test_invoice_search_error_messages_are_informative(): void
    {
        // Nonexistent invoice
        $res404 = $this->actingAs($this->admin)->getJson(
            route('billing.multiple-invoice-payment.search-invoice', ['number' => '#INV-999999-9999'])
        );
        $res404->assertStatus(404);
        $res404->assertJson([
            'success' => false,
            'message' => "Invoice '#INV-999999-9999' not found.",
        ]);

        // Cancelled invoice
        $cancelledInv = Invoice::create([
            'invoice_number' => 'INV-202610-CANCELLED',
            'branch_id' => $this->branch1->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 10000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 10000.00,
            'status' => 'cancelled',
            'created_by' => $this->admin->id,
        ]);
        $resCancelled = $this->actingAs($this->admin)->getJson(
            route('billing.multiple-invoice-payment.search-invoice', ['number' => '#INV-202610-CANCELLED'])
        );
        $resCancelled->assertStatus(422);
        $resCancelled->assertJson([
            'success' => false,
            'message' => "Invoice 'INV-202610-CANCELLED' is cancelled and cannot accept payments.",
        ]);

        // Fully settled invoice
        $paidInv = Invoice::create([
            'invoice_number' => 'INV-202610-PAID',
            'branch_id' => $this->branch1->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 5000.00,
            'amount_paid' => 5000.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_by' => $this->admin->id,
        ]);
        $resPaid = $this->actingAs($this->admin)->getJson(
            route('billing.multiple-invoice-payment.search-invoice', ['number' => 'INV-202610-PAID'])
        );
        $resPaid->assertStatus(422);
        $resPaid->assertJson([
            'success' => false,
            'message' => "Invoice 'INV-202610-PAID' is already fully settled.",
        ]);
    }

    /**
     * Test 3: Invoice search protects branch isolation.
     */
    public function test_invoice_search_respects_branch_permissions(): void
    {
        $kandyInvoice = Invoice::create([
            'invoice_number' => 'INV-202610-KDY01',
            'branch_id' => $this->branch2->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 12000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 12000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        // Staff assigned to Colombo should be forbidden from accessing Kandy invoice
        $response = $this->actingAs($this->staff)->getJson(
            route('billing.multiple-invoice-payment.search-invoice', ['number' => '#INV-202610-KDY01'])
        );
        $response->assertStatus(403);
    }

    /**
     * Test 4: Option 1 Split Invoice Payment saves multiple payment methods under the same invoice.
     */
    public function test_split_invoice_payment_records_multiple_methods_under_same_invoice(): void
    {
        // Invoice Outstanding: 15,000
        // New payments: Cash: 5,000, Card: 6,000, Bank: 4,000 = Total 15,000 -> Remaining: 0
        $response = $this->actingAs($this->admin)->post(route('billing.multiple-invoice-payment.split'), [
            'invoice_id' => $this->invoice->id,
            'payment_date' => now()->toDateString(),
            'payments' => [
                ['payment_method' => 'cash', 'amount' => 5000.00, 'reference_number' => 'CASH-001'],
                ['payment_method' => 'card', 'amount' => 6000.00, 'reference_number' => 'CARD-AUTH-999'],
                ['payment_method' => 'bank_transfer', 'amount' => 4000.00, 'reference_number' => 'TXN-BANK-555'],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fresh = $this->invoice->fresh();
        $this->assertEquals('INV-202610-0013', $fresh->invoice_number); // Unchanged
        $this->assertEquals(20000.00, (float) $fresh->amount_paid);
        $this->assertEquals(0.00, (float) $fresh->balance_amount);
        $this->assertEquals('paid', $fresh->status);

        // Verify Payment records
        $payments = Payment::where('invoice_id', $this->invoice->id)->get();
        $this->assertCount(3, $payments);
        foreach ($payments as $p) {
            $this->assertEquals('completed', $p->status);
            $this->assertEquals($this->invoice->job_card_id, $p->job_card_id);
        }
    }

    /**
     * Test 5: Option 1 validates references for card/bank/cheque and prevents overpayment.
     */
    public function test_split_payment_requires_reference_for_non_cash_and_rejects_overpayment(): void
    {
        // Missing reference for Card
        $responseNoRef = $this->actingAs($this->admin)->post(route('billing.multiple-invoice-payment.split'), [
            'invoice_id' => $this->invoice->id,
            'payment_date' => now()->toDateString(),
            'payments' => [
                ['payment_method' => 'card', 'amount' => 5000.00, 'reference_number' => ''],
            ],
        ]);
        $responseNoRef->assertSessionHas('error');

        // Overpayment (Balance is 15,000, paying 20,000)
        $responseOver = $this->actingAs($this->admin)->post(route('billing.multiple-invoice-payment.split'), [
            'invoice_id' => $this->invoice->id,
            'payment_date' => now()->toDateString(),
            'payments' => [
                ['payment_method' => 'cash', 'amount' => 20000.00, 'reference_number' => 'CASH'],
            ],
        ]);
        $responseOver->assertSessionHas('error');
    }

    /**
     * Test 6: Option 1 partial payments track history correctly across subsequent payments.
     */
    public function test_split_payment_handles_sequential_partial_payments(): void
    {
        // Invoice: 20,000 total, 5,000 paid, 15,000 balance
        // Payment 1: 5,000
        $this->actingAs($this->admin)->post(route('billing.multiple-invoice-payment.split'), [
            'invoice_id' => $this->invoice->id,
            'payment_date' => now()->toDateString(),
            'payments' => [
                ['payment_method' => 'cash', 'amount' => 5000.00],
            ],
        ]);

        $fresh1 = $this->invoice->fresh();
        $this->assertEquals(10000.00, (float) $fresh1->amount_paid);
        $this->assertEquals(10000.00, (float) $fresh1->balance_amount);
        $this->assertEquals('partially_paid', $fresh1->status);

        // Payment 2: 10,000
        $this->actingAs($this->admin)->post(route('billing.multiple-invoice-payment.split'), [
            'invoice_id' => $this->invoice->id,
            'payment_date' => now()->toDateString(),
            'payments' => [
                ['payment_method' => 'bank_transfer', 'amount' => 10000.00, 'reference_number' => 'TXN-FINAL'],
            ],
        ]);

        $fresh2 = $this->invoice->fresh();
        $this->assertEquals(20000.00, (float) $fresh2->amount_paid);
        $this->assertEquals(0.00, (float) $fresh2->balance_amount);
        $this->assertEquals('paid', $fresh2->status);

        // Total 2 new payments recorded
        $this->assertEquals(2, Payment::where('invoice_id', $this->invoice->id)->count());
    }

    /**
     * Test 7: Option 2 Customer Invoices endpoint returns outstanding invoices.
     */
    public function test_customer_invoices_endpoint_returns_unpaid_invoices(): void
    {
        $inv2 = Invoice::create([
            'invoice_number' => 'INV-202610-0014',
            'branch_id' => $this->branch1->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 10000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 10000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->getJson(
            route('billing.multiple-invoice-payment.customer-invoices', $this->customer)
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'customer' => [
                'id' => $this->customer->id,
                'name' => 'ABC Transport',
            ],
            'total_outstanding' => 25000.00, // 15,000 + 10,000
        ]);
        $response->assertJsonCount(2, 'invoices');
    }

    /**
     * Test 8: Option 2 Pay Multiple Invoices creates one receipt, links allocations, and updates Money Book correctly.
     */
    public function test_bulk_payment_allocates_across_invoices_and_appears_once_in_money_book(): void
    {
        $creditAccount = CustomerCreditAccount::create([
            'customer_id' => $this->customer->id,
            'credit_limit' => 100000.00,
            'opening_balance' => 0.00,
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        $inv2 = Invoice::create([
            'invoice_number' => 'INV-202610-0014',
            'branch_id' => $this->branch1->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'grand_total' => 15000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 15000.00,
            'status' => 'issued',
            'created_by' => $this->admin->id,
        ]);

        // Outstanding: inv1 = 15,000, inv2 = 15,000. Total = 30,000
        $this->assertEquals(30000.00, (float) $creditAccount->outstanding_balance);

        // Customer pays 20,000 via cheque:
        // Allocate: 15,000 to inv1 (fully settles), 5,000 to inv2 (10,000 remaining)
        $response = $this->actingAs($this->admin)->post(route('billing.multiple-invoice-payment.bulk'), [
            'customer_id' => $this->customer->id,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cheque',
            'reference_number' => 'CHQ-CORP-9988',
            'total_received' => 20000.00,
            'notes' => 'Corporate settlement for October',
            'allocations' => [
                ['invoice_id' => $this->invoice->id, 'allocated_amount' => 15000.00],
                ['invoice_id' => $inv2->id, 'allocated_amount' => 5000.00],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify Master Receipt created
        $receipt = PaymentReceipt::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($receipt);
        $this->assertEquals(20000.00, (float) $receipt->total_amount);
        $this->assertEquals('CHQ-CORP-9988', $receipt->reference_number);

        // Verify Payment Allocations
        $this->assertEquals(2, $receipt->allocations()->count());

        // Verify individual invoices
        $fresh1 = $this->invoice->fresh();
        $this->assertEquals(20000.00, (float) $fresh1->amount_paid);
        $this->assertEquals(0.00, (float) $fresh1->balance_amount);
        $this->assertEquals('paid', $fresh1->status);

        $fresh2 = $inv2->fresh();
        $this->assertEquals(5000.00, (float) $fresh2->amount_paid);
        $this->assertEquals(10000.00, (float) $fresh2->balance_amount);
        $this->assertEquals('partially_paid', $fresh2->status);

        // Customer Credit Account updated dynamically
        $freshCredit = $creditAccount->fresh();
        $this->assertEquals(10000.00, (float) $freshCredit->outstanding_balance);

        // Verify Money Book shows receipt only once with 20,000
        $moneyBookRes = $this->actingAs($this->admin)->get(route('reports.money-book.index', [
            'date_from' => now()->toDateString(),
            'date_to' => now()->toDateString(),
            'branch_id' => 'all',
        ]));
        $moneyBookRes->assertStatus(200);
        $moneyBookRes->assertSee($receipt->receipt_number);
        $moneyBookRes->assertSee('20,000.00');
    }

    /**
     * Test 9: Screen loads Option 2 tab directly via URL parameter.
     */
    public function test_screen_loads_option_2_immediately_via_query_param(): void
    {
        $response = $this->actingAs($this->admin)->get(
            route('billing.multiple-invoice-payment.index', ['tab' => 'bulk'])
        );
        $response->assertStatus(200);
        $response->assertSee('Option 2: Pay Multiple Invoices');
        $response->assertSee('initialTab: \'bulk\'', false);
    }
}
