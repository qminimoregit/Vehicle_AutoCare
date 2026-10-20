<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeWageRate;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExpensesStage5Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

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

        $this->employee = Employee::create([
            'employee_number' => 'EMP-001',
            'name' => 'Nilantha Kumara',
            'mobile' => '0771234567',
            'address' => 'Colombo 05',
            'daily_wage' => 3000.00,
            'joining_date' => '2026-01-01',
            'position' => 'Senior Mechanic',
            'is_active' => true,
        ]);

        EmployeeWageRate::create([
            'employee_id' => $this->employee->id,
            'daily_wage' => 3000.00,
            'effective_from' => '2026-01-01',
            'is_current' => true,
        ]);
    }

    // ==========================================
    // 1. EXPENSE CATEGORY MANAGEMENT
    // ==========================================

    public function test_admin_can_view_expense_categories_and_defaults_are_seeded(): void
    {
        $response = $this->actingAs($this->admin)->get(route('expenses.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('expenses.categories.index');
        $response->assertSee('Salary Advance');
        $response->assertSee('Electricity');
        $response->assertSee('Water');
        $this->assertDatabaseHas('expense_categories', ['name' => 'Salary Advance']);
        $this->assertDatabaseHas('expense_categories', ['name' => 'Electricity']);
    }

    public function test_admin_can_add_new_expense_category(): void
    {
        $response = $this->actingAs($this->admin)->post(route('expenses.categories.store'), [
            'name' => 'Tools & Equipment',
        ]);

        $response->assertRedirect(route('expenses.categories.index'));
        $this->assertDatabaseHas('expense_categories', [
            'name' => 'Tools & Equipment',
            'is_active' => 1,
        ]);
    }

    public function test_prevents_duplicate_expense_category(): void
    {
        ExpenseCategory::create(['name' => 'Electricity', 'is_active' => true]);

        $response = $this->actingAs($this->admin)->post(route('expenses.categories.store'), [
            'name' => 'Electricity',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_edit_expense_category(): void
    {
        $category = ExpenseCategory::create(['name' => 'Electric Bills', 'is_active' => true]);

        $response = $this->actingAs($this->admin)->put(route('expenses.categories.update', $category), [
            'name' => 'Electricity Utility',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('expenses.categories.index'));
        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'name' => 'Electricity Utility',
        ]);
    }

    public function test_admin_can_toggle_deactivate_and_activate_category(): void
    {
        $category = ExpenseCategory::create(['name' => 'Office Snacks', 'is_active' => true]);

        // Toggle to Inactive
        $this->actingAs($this->admin)->patch(route('expenses.categories.toggle-status', $category));
        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'is_active' => 0,
        ]);

        // Toggle to Active
        $this->actingAs($this->admin)->patch(route('expenses.categories.toggle-status', $category));
        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'is_active' => 1,
        ]);
    }

    public function test_staff_cannot_manage_categories(): void
    {
        $response = $this->actingAs($this->staff)->get(route('expenses.categories.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->staff)->post(route('expenses.categories.store'), [
            'name' => 'Staff Category Attempt',
        ]);
        $response->assertStatus(403);
    }

    // ==========================================
    // 2. STAFF EXPENSES & SALARY ADVANCES
    // ==========================================

    public function test_can_view_staff_expense_create_page(): void
    {
        ExpenseCategory::seedDefaults();

        $response = $this->actingAs($this->admin)->get(route('expenses.staff.create'));
        $response->assertStatus(200);
        $response->assertSee('Add Staff Expense');
        $response->assertSee('Nilantha Kumara');
        $response->assertSee('Salary Advance');
    }

    public function test_can_create_general_staff_expense(): void
    {
        $category = ExpenseCategory::create(['name' => 'Staff Welfare', 'is_active' => true]);

        $response = $this->actingAs($this->admin)->post(route('expenses.staff.store'), [
            'employee_id' => $this->employee->id,
            'category_id' => $category->id,
            'payment_date' => '2026-10-20',
            'related_month' => 'October 2026',
            'amount' => 1500.00,
            'payment_method' => 'cash',
            'description' => 'Staff medical welfare allowance',
        ]);

        $response->assertRedirect(route('expenses.records.index'));
        $this->assertDatabaseHas('expenses', [
            'employee_id' => $this->employee->id,
            'category_id' => $category->id,
            'expense_type' => 'staff',
            'amount' => 1500.00,
            'payment_method' => 'cash',
            'employee_advance_id' => null,
            'status' => 'active',
        ]);
    }

    public function test_salary_advance_staff_expense_automatically_links_to_payroll_advance(): void
    {
        $advanceCategory = ExpenseCategory::create(['name' => 'Salary Advance', 'is_active' => true]);

        $response = $this->actingAs($this->admin)->post(route('expenses.staff.store'), [
            'employee_id' => $this->employee->id,
            'category_id' => $advanceCategory->id,
            'payment_date' => '2026-10-20',
            'related_month' => 'October 2026',
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'description' => 'Mid month salary advance for festival',
        ]);

        $response->assertRedirect(route('expenses.records.index'));

        // 1. Verify advance was created in employee_advances
        $this->assertDatabaseHas('employee_advances', [
            'employee_id' => $this->employee->id,
            'amount' => 5000.00,
            'status' => 'pending',
            'payment_method' => 'cash',
        ]);

        $advance = EmployeeAdvance::where('employee_id', $this->employee->id)->first();
        $this->assertNotNull($advance);

        // 2. Verify expense record is linked to advance
        $this->assertDatabaseHas('expenses', [
            'employee_id' => $this->employee->id,
            'category_id' => $advanceCategory->id,
            'expense_type' => 'staff',
            'amount' => 5000.00,
            'employee_advance_id' => $advance->id,
            'status' => 'active',
        ]);
    }

    public function test_salary_advance_is_deducted_in_payroll_and_does_not_deduct_twice(): void
    {
        $advanceCategory = ExpenseCategory::create(['name' => 'Salary Advance', 'is_active' => true]);

        // Record Salary Advance through Expenses module
        $this->actingAs($this->admin)->post(route('expenses.staff.store'), [
            'employee_id' => $this->employee->id,
            'category_id' => $advanceCategory->id,
            'payment_date' => '2026-10-15',
            'related_month' => 'October 2026',
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'description' => 'Festival advance',
        ]);

        $advance = EmployeeAdvance::where('employee_id', $this->employee->id)->first();
        $this->assertEquals('pending', $advance->status);

        // Generate draft salary for October 2026
        $this->actingAs($this->admin)->get(route('payroll.salary.index', [
            'year' => 2026,
            'month' => 10,
            'generate' => 1,
        ]));

        $period = PayrollPeriod::where('year', 2026)->where('month', 10)->first();
        $this->assertNotNull($period);

        $item = PayrollItem::where('payroll_period_id', $period->id)
            ->where('employee_id', $this->employee->id)
            ->first();

        $this->assertNotNull($item);
        // Advance deduction reflects in payroll item
        $this->assertEquals(5000.00, (float) $item->advances_applied);

        // Approve period
        $this->actingAs($this->admin)->post(route('payroll.salary.approve', $period));

        $advance->refresh();
        $this->assertEquals('deducted', $advance->status);

        // Prevent voiding if advance is already finalized/deducted in approved payroll
        $expense = Expense::where('employee_advance_id', $advance->id)->first();
        $voidResponse = $this->actingAs($this->admin)->post(route('expenses.records.void', $expense), [
            'void_reason' => 'Mistake in advance',
        ]);

        $voidResponse->assertSessionHas('error');
        $expense->refresh();
        $this->assertEquals('active', $expense->status); // Void blocked to preserve approved payroll
    }

    // ==========================================
    // 3. SHOP OPERATING EXPENSES
    // ==========================================

    public function test_can_view_shop_expense_create_page(): void
    {
        ExpenseCategory::seedDefaults();

        $response = $this->actingAs($this->admin)->get(route('expenses.shop.create'));
        $response->assertStatus(200);
        $response->assertSee('Add Shop Expense');
        $response->assertSee('Electricity');
    }

    public function test_can_create_shop_expense_with_bank_transfer_and_reference(): void
    {
        $category = ExpenseCategory::create(['name' => 'Electricity', 'is_active' => true]);

        $response = $this->actingAs($this->admin)->post(route('expenses.shop.store'), [
            'category_id' => $category->id,
            'expense_date' => '2026-10-20',
            'related_month' => 'October 2026',
            'amount' => 15000.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => '2026-10-20',
            'payment_reference' => 'CEB-ACC-987654321',
            'description' => 'Monthly electricity bill for bays and compressors',
        ]);

        $response->assertRedirect(route('expenses.records.index'));
        $this->assertDatabaseHas('expenses', [
            'category_id' => $category->id,
            'expense_type' => 'shop',
            'amount' => 15000.00,
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'CEB-ACC-987654321',
            'status' => 'active',
        ]);
    }

    public function test_can_upload_receipt_and_securely_view_download_it(): void
    {
        $category = ExpenseCategory::create(['name' => 'Chemicals', 'is_active' => true]);
        $fakePdf = UploadedFile::fake()->create('receipt.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('expenses.shop.store'), [
            'category_id' => $category->id,
            'expense_date' => '2026-10-20',
            'related_month' => 'October 2026',
            'amount' => 8400.00,
            'payment_method' => 'cash',
            'description' => 'Car shampoo and tyre shine concentrate',
            'receipt' => $fakePdf,
        ]);

        $response->assertRedirect(route('expenses.records.index'));

        $expense = Expense::where('category_id', $category->id)->first();
        $this->assertNotNull($expense->receipt_path);
        Storage::disk('public')->assertExists($expense->receipt_path);

        // Test downloading receipt
        $downloadResponse = $this->actingAs($this->admin)->get(route('expenses.records.receipt', $expense));
        $downloadResponse->assertStatus(200);
    }

    // ==========================================
    // 4. SEARCH, FILTERS & SUMMARIES
    // ==========================================

    public function test_can_search_and_filter_expenses_and_summaries_are_accurate(): void
    {
        $catShop = ExpenseCategory::create(['name' => 'Repair', 'is_active' => true]);
        $catStaff = ExpenseCategory::create(['name' => 'Staff Welfare', 'is_active' => true]);

        Expense::create([
            'expense_number' => 'EXP-2610-0001',
            'category_id' => $catShop->id,
            'expense_type' => 'shop',
            'expense_date' => '2026-10-05',
            'related_month' => 'October 2026',
            'amount' => 12000.00,
            'payment_method' => 'bank_transfer',
            'description' => 'Hydraulic lift motor repair',
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        Expense::create([
            'expense_number' => 'EXP-2610-0002',
            'category_id' => $catStaff->id,
            'expense_type' => 'staff',
            'employee_id' => $this->employee->id,
            'expense_date' => '2026-10-10',
            'related_month' => 'October 2026',
            'amount' => 3000.00,
            'payment_method' => 'cash',
            'description' => 'Staff lunch allowance',
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        // 1. View listing
        $response = $this->actingAs($this->admin)->get(route('expenses.records.index'));
        $response->assertStatus(200);
        $response->assertSee('15,000.00'); // Total
        $response->assertSee('3,000.00');  // Staff
        $response->assertSee('12,000.00'); // Shop

        // 2. Filter by category
        $responseCat = $this->actingAs($this->admin)->get(route('expenses.records.index', [
            'category_id' => $catShop->id,
        ]));
        $responseCat->assertSee('Hydraulic lift motor repair');
        $responseCat->assertDontSee('Staff lunch allowance');

        // 3. Search query
        $responseSearch = $this->actingAs($this->admin)->get(route('expenses.records.index', [
            'search' => 'Nilantha',
        ]));
        $responseSearch->assertSee('EXP-2610-0002');
        $responseSearch->assertDontSee('EXP-2610-0001');
    }

    // ==========================================
    // 5. VOIDING & CORRECTIONS
    // ==========================================

    public function test_admin_can_void_expense_and_pending_advance_is_cancelled(): void
    {
        $advanceCat = ExpenseCategory::create(['name' => 'Salary Advance', 'is_active' => true]);

        $this->actingAs($this->admin)->post(route('expenses.staff.store'), [
            'employee_id' => $this->employee->id,
            'category_id' => $advanceCat->id,
            'payment_date' => '2026-10-20',
            'related_month' => 'October 2026',
            'amount' => 4000.00,
            'payment_method' => 'cash',
            'description' => 'Wrong entry to be voided',
        ]);

        $expense = Expense::where('amount', 4000.00)->first();
        $advance = $expense->advance;

        $response = $this->actingAs($this->admin)->post(route('expenses.records.void', $expense), [
            'void_reason' => 'Created in error by cashier',
        ]);

        $response->assertRedirect();
        $expense->refresh();
        $advance->refresh();

        $this->assertEquals('voided', $expense->status);
        $this->assertEquals('Created in error by cashier', $expense->void_reason);
        $this->assertEquals('cancelled', $advance->status);
    }

    public function test_admin_can_edit_active_expense(): void
    {
        $cat = ExpenseCategory::create(['name' => 'Maintenance', 'is_active' => true]);

        $expense = Expense::create([
            'expense_number' => 'EXP-2610-0010',
            'category_id' => $cat->id,
            'expense_type' => 'shop',
            'expense_date' => '2026-10-18',
            'related_month' => 'October 2026',
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('expenses.records.update', $expense), [
            'category_id' => $cat->id,
            'expense_date' => '2026-10-18',
            'related_month' => 'October 2026',
            'amount' => 6500.00,
            'payment_method' => 'cash',
            'description' => 'Updated amount with tax',
        ]);

        $response->assertRedirect(route('expenses.records.show', $expense));
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 6500.00,
            'description' => 'Updated amount with tax',
        ]);
    }

    // ==========================================
    // 6. CSV EXPORT & PERMISSIONS
    // ==========================================

    public function test_admin_can_export_expenses_to_csv(): void
    {
        $cat = ExpenseCategory::create(['name' => 'Electricity', 'is_active' => true]);

        Expense::create([
            'expense_number' => 'EXP-2610-0099',
            'category_id' => $cat->id,
            'expense_type' => 'shop',
            'expense_date' => '2026-10-20',
            'related_month' => 'October 2026',
            'amount' => 9999.00,
            'payment_method' => 'bank_transfer',
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('expenses.records.export'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=Expense_Records_' . date('Y-m-d') . '.csv');
    }

    public function test_staff_cannot_edit_or_void_expenses(): void
    {
        $cat = ExpenseCategory::create(['name' => 'Electricity', 'is_active' => true]);

        $expense = Expense::create([
            'expense_number' => 'EXP-2610-0100',
            'category_id' => $cat->id,
            'expense_type' => 'shop',
            'expense_date' => '2026-10-20',
            'related_month' => 'October 2026',
            'amount' => 2000.00,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        $responseEdit = $this->actingAs($this->staff)->get(route('expenses.records.edit', $expense));
        $responseEdit->assertStatus(403);

        $responseVoid = $this->actingAs($this->staff)->post(route('expenses.records.void', $expense), [
            'void_reason' => 'Staff trying to void',
        ]);
        $responseVoid->assertStatus(403);
    }

    public function test_expense_details_page_renders_correctly(): void
    {
        $cat = ExpenseCategory::create(['name' => 'Water', 'is_active' => true]);

        $expense = Expense::create([
            'expense_number' => 'EXP-2610-0200',
            'category_id' => $cat->id,
            'expense_type' => 'shop',
            'expense_date' => '2026-10-20',
            'related_month' => 'October 2026',
            'amount' => 3450.00,
            'payment_method' => 'cash',
            'description' => 'Water supply bill',
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('expenses.records.show', $expense));
        $response->assertStatus(200);
        $response->assertViewIs('expenses.records.show');
        $response->assertSee('EXP-2610-0200');
        $response->assertSee('3,450.00');
        $response->assertSee('Water supply bill');
    }

    public function test_expense_edit_page_renders_correctly_for_admin(): void
    {
        $cat = ExpenseCategory::create(['name' => 'Repair', 'is_active' => true]);

        $expense = Expense::create([
            'expense_number' => 'EXP-2610-0201',
            'category_id' => $cat->id,
            'expense_type' => 'shop',
            'expense_date' => '2026-10-20',
            'related_month' => 'October 2026',
            'amount' => 7500.00,
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('expenses.records.edit', $expense));
        $response->assertStatus(200);
        $response->assertViewIs('expenses.records.edit');
        $response->assertSee('EXP-2610-0201');
        $response->assertSee('7500');
    }

    public function test_empty_state_renders_when_no_expenses_match(): void
    {
        $response = $this->actingAs($this->admin)->get(route('expenses.records.index', [
            'search' => 'NonExistentKeywordXYZ123',
        ]));

        $response->assertStatus(200);
        $response->assertSee('No expense records found');
    }
}
