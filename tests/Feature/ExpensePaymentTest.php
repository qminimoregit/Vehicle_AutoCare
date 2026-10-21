<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpensePayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpensePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected Branch $mainBranch;

    protected Branch $kandyBranch;

    protected ExpenseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Branches
        $company = \App\Models\CompanySetting::getSettings();

        $this->mainBranch = Branch::create([
            'company_id' => $company->id,
            'branch_name' => 'Main Center',
            'branch_code' => 'MAIN',
            'phone' => '0112345678',
            'address' => 'Colombo 03',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $this->kandyBranch = Branch::create([
            'company_id' => $company->id,
            'branch_name' => 'Kandy Branch',
            'branch_code' => 'KDY',
            'phone' => '0812345678',
            'address' => 'Kandy City',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        // 2. Create Users
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@autocare.test',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
            'permissions' => json_encode(['view_expenses' => true, 'create_expenses' => true]),
        ]);
        $this->staff->branches()->attach($this->mainBranch->id);

        // 3. Create Default Category
        $this->category = ExpenseCategory::create([
            'name' => 'Electricity',
            'is_active' => true,
        ]);
    }

    /**
     * Test 1: Add a paid expense.
     */
    public function test_can_add_a_paid_expense_with_initial_payment(): void
    {
        $response = $this->actingAs($this->admin)->post(route('expenses.shop.store'), [
            'category_id' => $this->category->id,
            'branch_id' => $this->mainBranch->id,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'amount' => 8500.00,
            'payment_status_choice' => 'paid_now',
            'payment_method' => 'cash',
            'payment_date' => '2026-10-21',
            'payment_amount' => 8500.00,
            'payment_reference' => 'CEB-REC-1001',
            'description' => 'Service bay electricity bill',
        ]);

        $response->assertRedirect(route('expenses.records.index'));

        // Verify Expense record is saved as paid
        $expense = Expense::where('description', 'Service bay electricity bill')->first();
        $this->assertNotNull($expense);
        $this->assertEquals(8500.00, (float) $expense->amount);
        $this->assertEquals(8500.00, (float) $expense->paid_amount);
        $this->assertEquals(0.00, (float) $expense->balance_amount);
        $this->assertEquals(Expense::PAYMENT_STATUS_PAID, $expense->payment_status);
        $this->assertTrue($expense->isPaid());
        $this->assertFalse($expense->hasOutstandingBalance());

        // Verify initial ExpensePayment record was created
        $this->assertDatabaseHas('expense_payments', [
            'expense_id' => $expense->id,
            'branch_id' => $this->mainBranch->id,
            'amount' => 8500.00,
            'payment_method' => 'cash',
            'reference_number' => 'CEB-REC-1001',
        ]);
    }

    /**
     * Test 2: Add an unpaid expense.
     */
    public function test_can_add_an_unpaid_expense_with_outstanding_balance(): void
    {
        $response = $this->actingAs($this->admin)->post(route('expenses.shop.store'), [
            'category_id' => $this->category->id,
            'branch_id' => $this->mainBranch->id,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'amount' => 12000.00,
            'payment_status_choice' => 'pay_later',
            'description' => 'Water utility invoice received',
        ]);

        $response->assertRedirect(route('expenses.records.index'));

        $expense = Expense::where('description', 'Water utility invoice received')->first();
        $this->assertNotNull($expense);
        $this->assertEquals(12000.00, (float) $expense->amount);
        $this->assertEquals(0.00, (float) $expense->paid_amount);
        $this->assertEquals(12000.00, (float) $expense->balance_amount);
        $this->assertEquals(Expense::PAYMENT_STATUS_UNPAID, $expense->payment_status);
        $this->assertTrue($expense->isUnpaid());
        $this->assertTrue($expense->hasOutstandingBalance());

        // Unpaid expense must have NO payment records
        $this->assertCount(0, $expense->payments);
    }

    /**
     * Test 3: Pay an unpaid expense in full.
     */
    public function test_can_pay_an_unpaid_expense_in_full(): void
    {
        // Create an unpaid expense
        $expense = Expense::create([
            'expense_number' => Expense::generateNextExpenseNumber(),
            'branch_id' => $this->mainBranch->id,
            'category_id' => $this->category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'description' => 'Compressor service fees',
            'amount' => 8500.00,
            'paid_amount' => 0.00,
            'balance_amount' => 8500.00,
            'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
            'payment_method' => 'pending',
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->staff)->post(route('expenses.records.payments.store', $expense), [
            'payment_amount' => 8500.00,
            'payment_date' => '2026-10-21',
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'TRF-BANK-8899',
            'notes' => 'Settled via online banking',
        ]);

        $response->assertSessionHas('success');

        $expense->refresh();
        $this->assertEquals(8500.00, (float) $expense->paid_amount);
        $this->assertEquals(0.00, (float) $expense->balance_amount);
        $this->assertEquals(Expense::PAYMENT_STATUS_PAID, $expense->payment_status);
        $this->assertFalse($expense->hasOutstandingBalance());

        // Verify payment record
        $this->assertDatabaseHas('expense_payments', [
            'expense_id' => $expense->id,
            'amount' => 8500.00,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TRF-BANK-8899',
            'created_by' => $this->staff->id,
        ]);
    }

    /**
     * Test 4: Record a partial payment.
     */
    public function test_can_record_a_partial_payment(): void
    {
        $expense = Expense::create([
            'expense_number' => Expense::generateNextExpenseNumber(),
            'branch_id' => $this->mainBranch->id,
            'category_id' => $this->category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'description' => 'Bay renovation bill',
            'amount' => 50000.00,
            'paid_amount' => 0.00,
            'balance_amount' => 50000.00,
            'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
            'payment_method' => 'pending',
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $this->admin->id,
        ]);

        // Pay partial amount: 20,000 out of 50,000
        $response = $this->actingAs($this->staff)->post(route('expenses.records.payments.store', $expense), [
            'payment_amount' => 20000.00,
            'payment_date' => '2026-10-21',
            'payment_method' => 'card',
            'payment_reference' => 'CARD-AUTH-1234',
            'notes' => '1st advance payment',
        ]);

        $response->assertSessionHas('success');

        $expense->refresh();
        $this->assertEquals(20000.00, (float) $expense->paid_amount);
        $this->assertEquals(30000.00, (float) $expense->balance_amount);
        $this->assertEquals(Expense::PAYMENT_STATUS_PARTIALLY_PAID, $expense->payment_status);
        $this->assertTrue($expense->isPartiallyPaid());
        $this->assertTrue($expense->hasOutstandingBalance());
    }

    /**
     * Test 5: Pay the remaining balance after a partial payment.
     */
    public function test_can_pay_the_remaining_balance_to_complete_payment(): void
    {
        $expense = Expense::create([
            'expense_number' => Expense::generateNextExpenseNumber(),
            'branch_id' => $this->mainBranch->id,
            'category_id' => $this->category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'description' => 'Compound cleaning service',
            'amount' => 10000.00,
            'paid_amount' => 4000.00,
            'balance_amount' => 6000.00,
            'payment_status' => Expense::PAYMENT_STATUS_PARTIALLY_PAID,
            'payment_method' => 'cash',
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $this->admin->id,
        ]);

        ExpensePayment::create([
            'expense_id' => $expense->id,
            'branch_id' => $this->mainBranch->id,
            'payment_number' => ExpensePayment::generatePaymentNumber(),
            'payment_date' => '2026-10-20',
            'amount' => 4000.00,
            'payment_method' => 'cash',
            'created_by' => $this->admin->id,
        ]);

        // Pay remaining 6,000
        $response = $this->actingAs($this->staff)->post(route('expenses.records.payments.store', $expense), [
            'payment_amount' => 6000.00,
            'payment_date' => '2026-10-21',
            'payment_method' => 'cheque',
            'payment_reference' => 'CHQ-556677',
            'notes' => 'Final balance settlement',
        ]);

        $response->assertSessionHas('success');

        $expense->refresh();
        $this->assertEquals(10000.00, (float) $expense->paid_amount);
        $this->assertEquals(0.00, (float) $expense->balance_amount);
        $this->assertEquals(Expense::PAYMENT_STATUS_PAID, $expense->payment_status);
        $this->assertFalse($expense->hasOutstandingBalance());
    }

    /**
     * Test 6: Verify payment history tracking.
     */
    public function test_verifies_payment_history_chronology_and_audit(): void
    {
        $expense = Expense::create([
            'expense_number' => Expense::generateNextExpenseNumber(),
            'branch_id' => $this->mainBranch->id,
            'category_id' => $this->category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'description' => 'Security equipment install',
            'amount' => 30000.00,
            'paid_amount' => 0.00,
            'balance_amount' => 30000.00,
            'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
            'payment_method' => 'pending',
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $this->admin->id,
        ]);

        // Installment 1: 10,000
        $this->actingAs($this->admin)->post(route('expenses.records.payments.store', $expense), [
            'payment_amount' => 10000.00,
            'payment_date' => '2026-10-20',
            'payment_method' => 'cash',
            'payment_reference' => 'INST-1',
            'notes' => 'Deposit payment',
        ]);

        // Installment 2: 20,000
        $this->actingAs($this->staff)->post(route('expenses.records.payments.store', $expense), [
            'payment_amount' => 20000.00,
            'payment_date' => '2026-10-21',
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'INST-2',
            'notes' => 'Settlement payment',
        ]);

        $expense->refresh();
        $this->assertCount(2, $expense->payments);

        $payment1 = $expense->payments->first();
        $payment2 = $expense->payments->last();

        $this->assertEquals(10000.00, (float) $payment1->amount);
        $this->assertEquals('cash', $payment1->payment_method);
        $this->assertEquals($this->admin->id, $payment1->created_by);

        $this->assertEquals(20000.00, (float) $payment2->amount);
        $this->assertEquals('bank_transfer', $payment2->payment_method);
        $this->assertEquals($this->staff->id, $payment2->created_by);

        // Check show page displays payment history
        $showRes = $this->actingAs($this->admin)->get(route('expenses.records.show', $expense));
        $showRes->assertStatus(200);
        $showRes->assertSee($payment1->payment_number);
        $showRes->assertSee($payment2->payment_number);
        $showRes->assertSee('10,000.00');
        $showRes->assertSee('20,000.00');
        $showRes->assertSee('Admin User');
        $showRes->assertSee('Staff User');
    }

    /**
     * Test 7: Verify payment status transitions in UI table.
     */
    public function test_verifies_payment_status_display_and_pay_button_visibility(): void
    {
        // 1. Paid expense
        $paidExp = Expense::create([
            'expense_number' => 'EXP-PAID-01',
            'branch_id' => $this->mainBranch->id,
            'category_id' => $this->category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'amount' => 5000.00,
            'paid_amount' => 5000.00,
            'balance_amount' => 0.00,
            'payment_status' => Expense::PAYMENT_STATUS_PAID,
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $this->admin->id,
        ]);

        // 2. Unpaid expense
        $unpaidExp = Expense::create([
            'expense_number' => 'EXP-UNPAID-01',
            'branch_id' => $this->mainBranch->id,
            'category_id' => $this->category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'amount' => 7000.00,
            'paid_amount' => 0.00,
            'balance_amount' => 7000.00,
            'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $this->admin->id,
        ]);

        $res = $this->actingAs($this->staff)->get(route('expenses.records.index'));
        $res->assertStatus(200);

        // Table shows Paid badge and Unpaid badge
        $res->assertSee('EXP-PAID-01');
        $res->assertSee('EXP-UNPAID-01');
        $res->assertSee('7,000.00');
        $res->assertSee('Paid');
        $res->assertSee('Unpaid');

        // Pay action is present for unpaid expense with balance
        $res->assertSee(route('expenses.records.payments.store', $unpaidExp));
    }

    /**
     * Test 8: Verify cash and bank reports reflect only actual payments and no double counting.
     */
    public function test_verify_cash_and_bank_reports_reflect_actual_outflows(): void
    {
        // Expense 1: Unpaid (8,000) - Should NOT count in cash outflow
        $unpaidExp = Expense::create([
            'expense_number' => 'EXP-UNP-01',
            'branch_id' => $this->mainBranch->id,
            'category_id' => $this->category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'amount' => 8000.00,
            'paid_amount' => 0.00,
            'balance_amount' => 8000.00,
            'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $this->admin->id,
        ]);

        // Expense 2: Paid Cash (3,500) on 2026-10-21
        $paidExp = Expense::create([
            'expense_number' => 'EXP-PD-01',
            'branch_id' => $this->mainBranch->id,
            'category_id' => $this->category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'amount' => 3500.00,
            'paid_amount' => 3500.00,
            'balance_amount' => 0.00,
            'payment_status' => Expense::PAYMENT_STATUS_PAID,
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $this->admin->id,
        ]);
        ExpensePayment::create([
            'expense_id' => $paidExp->id,
            'branch_id' => $this->mainBranch->id,
            'payment_number' => 'EPAY-TEST-001',
            'payment_date' => '2026-10-21',
            'amount' => 3500.00,
            'payment_method' => 'cash',
            'created_by' => $this->admin->id,
        ]);

        // Query Money Book report for 2026-10-21
        $res = $this->actingAs($this->admin)->get(route('reports.money-book.index', [
            'date_from' => '2026-10-21',
            'date_to' => '2026-10-21',
            'branch_id' => $this->mainBranch->id,
        ]));

        $res->assertStatus(200);
        // Only 3,500 cash payment appears as outflow, NOT 8,000 unpaid expense
        $res->assertSee('EPAY-TEST-001');
        $res->assertSee('3,500.00');
        $res->assertDontSee('EPAY-UNP');
    }

    /**
     * Test 9: Verify branch permissions.
     */
    public function test_branch_permissions_prevent_unauthorized_expense_recording_or_payment(): void
    {
        // Staff only has access to Main Branch, NOT Kandy Branch
        // 1. Attempt to create expense for unauthorized branch
        $createRes = $this->actingAs($this->staff)->post(route('expenses.shop.store'), [
            'category_id' => $this->category->id,
            'branch_id' => $this->kandyBranch->id, // unauthorized
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'amount' => 5000.00,
            'payment_status_choice' => 'pay_later',
        ]);
        $createRes->assertStatus(403);

        // 2. Attempt to pay an expense belonging to unauthorized branch
        $kandyExpense = Expense::create([
            'expense_number' => 'EXP-KDY-01',
            'branch_id' => $this->kandyBranch->id,
            'category_id' => $this->category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'amount' => 9000.00,
            'paid_amount' => 0.00,
            'balance_amount' => 9000.00,
            'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $this->admin->id,
        ]);

        $payRes = $this->actingAs($this->staff)->post(route('expenses.records.payments.store', $kandyExpense), [
            'payment_amount' => 9000.00,
            'payment_date' => '2026-10-21',
            'payment_method' => 'cash',
        ]);
        $payRes->assertStatus(403);
    }

    /**
     * Test 10: Verify no duplicate payments or overpayments exceeding balance.
     */
    public function test_prevents_payment_exceeding_outstanding_balance_and_duplicate_submissions(): void
    {
        $expense = Expense::create([
            'expense_number' => Expense::generateNextExpenseNumber(),
            'branch_id' => $this->mainBranch->id,
            'category_id' => $this->category->id,
            'expense_type' => Expense::TYPE_SHOP,
            'expense_date' => '2026-10-21',
            'related_month' => 'October 2026',
            'description' => 'Test bill for balance check',
            'amount' => 5000.00,
            'paid_amount' => 0.00,
            'balance_amount' => 5000.00,
            'payment_status' => Expense::PAYMENT_STATUS_UNPAID,
            'status' => Expense::STATUS_ACTIVE,
            'created_by' => $this->admin->id,
        ]);

        // Attempt overpayment of 6000 on balance of 5000
        $response = $this->actingAs($this->staff)->post(route('expenses.records.payments.store', $expense), [
            'payment_amount' => 6000.00,
            'payment_date' => '2026-10-21',
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHasErrors('payment_amount');
        $expense->refresh();
        $this->assertEquals(0.00, (float) $expense->paid_amount);
        $this->assertEquals(5000.00, (float) $expense->balance_amount);

        // Now pay full 5000
        $payRes = $this->actingAs($this->staff)->post(route('expenses.records.payments.store', $expense), [
            'payment_amount' => 5000.00,
            'payment_date' => '2026-10-21',
            'payment_method' => 'cash',
        ]);
        $payRes->assertSessionHas('success');

        // Attempt another payment on already settled expense
        $secondPayRes = $this->actingAs($this->staff)->post(route('expenses.records.payments.store', $expense), [
            'payment_amount' => 100.00,
            'payment_date' => '2026-10-21',
            'payment_method' => 'cash',
        ]);
        $secondPayRes->assertSessionHas('error');
    }
}
