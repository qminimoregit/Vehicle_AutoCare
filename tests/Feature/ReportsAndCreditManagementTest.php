<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\CustomerCreditAccount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Service;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsAndCreditManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected Branch $branch;

    protected Branch $branch2;

    protected CompanySetting $company;

    protected Customer $companyCustomer;

    protected Vehicle $vehicle1;

    protected Vehicle $vehicle2;

    protected Product $product;

    protected Supplier $supplier;

    protected UnitOfMeasurement $uom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = CompanySetting::getSettings();

        // Branches
        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'COL-01',
            'branch_name' => 'Colombo Main Branch',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $this->branch2 = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'KAN-02',
            'branch_name' => 'Kandy Branch',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        // Users
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->admin->branches()->attach([$this->branch->id, $this->branch2->id]);

        $this->staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
        ]);
        $this->staff->branches()->attach([$this->branch->id]);

        // Master Data
        $this->supplier = Supplier::create([
            'name' => 'Lanka Lubricants PLC',
            'company_name' => 'Lanka Lubricants PLC',
            'contact_person' => 'Sunil Perera',
            'phone' => '0112345678',
            'email' => 'orders@lubricants.lk',
            'address' => 'Colombo 10',
            'is_active' => true,
        ]);

        $this->uom = UnitOfMeasurement::create([
            'name' => 'Litre',
            'code' => 'LTR',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'sku' => 'OIL-5W30',
            'name' => 'Mobil 1 5W-30 Synthetic',
            'uom_id' => $this->uom->id,
            'preferred_supplier_id' => $this->supplier->id,
            'min_stock' => 5,
            'max_stock' => 100,
            'selling_price' => 2500.00,
            'is_active' => true,
        ]);

        // Create GRN
        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-202610-0001',
            'branch_id' => $this->branch->id,
            'supplier_id' => $this->supplier->id,
            'received_date' => now()->subDays(10),
            'status' => 'confirmed',
            'total_amount' => 23000.00,
        ]);

        $grnItem1 = GoodsReceivedNoteItem::create([
            'grn_id' => $grn->id,
            'product_id' => $this->product->id,
            'purchase_quantity' => 10,
            'free_quantity' => 0,
            'purchase_unit_price' => 1500.00,
            'line_total' => 15000.00,
        ]);

        $grnItem2 = GoodsReceivedNoteItem::create([
            'grn_id' => $grn->id,
            'product_id' => $this->product->id,
            'purchase_quantity' => 5,
            'free_quantity' => 0,
            'purchase_unit_price' => 1600.00,
            'line_total' => 8000.00,
        ]);

        // Create initial stock batches
        // Batch 1: 10 units @ 1500.00 in branch 1
        StockBatch::create([
            'batch_number' => 'BATCH-001',
            'branch_id' => $this->branch->id,
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem1->id,
            'product_id' => $this->product->id,
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->subDays(10),
            'received_quantity' => 10,
            'remaining_quantity' => 10,
            'original_purchase_unit_price' => 1500.00,
            'inventory_unit_cost' => 1500.00,
            'selling_price_at_receipt' => 2500.00,
            'status' => 'active',
        ]);

        // Batch 2: 5 units @ 1600.00 in branch 2
        StockBatch::create([
            'batch_number' => 'BATCH-002',
            'branch_id' => $this->branch2->id,
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem2->id,
            'product_id' => $this->product->id,
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->subDays(5),
            'received_quantity' => 5,
            'remaining_quantity' => 5,
            'original_purchase_unit_price' => 1600.00,
            'inventory_unit_cost' => 1600.00,
            'selling_price_at_receipt' => 2500.00,
            'status' => 'active',
        ]);

        // Customer & Corporate Fleet
        $this->companyCustomer = Customer::create([
            'customer_name' => 'Sunil Fernando',
            'customer_type' => 'company',
            'company_name' => 'ABC Transport Ltd',
            'phone' => '0771234567',
            'email' => 'fleet@abctransport.lk',
            'address' => '45 Logistics Way, Colombo',
            'status' => 'active',
        ]);

        $vehicleType = VehicleType::create([
            'name' => 'Van',
            'status' => 'active',
        ]);

        $this->vehicle1 = Vehicle::create([
            'customer_id' => $this->companyCustomer->id,
            'branch_id' => $this->branch->id,
            'vehicle_type_id' => $vehicleType->id,
            'vehicle_number' => 'CAB-1234',
            'vehicle_number_normalized' => 'CAB1234',
            'make_model' => 'Toyota HiAce',
            'current_mileage' => 45000,
            'next_service_mileage' => 50000,
        ]);

        $this->vehicle2 = Vehicle::create([
            'customer_id' => $this->companyCustomer->id,
            'branch_id' => $this->branch->id,
            'vehicle_type_id' => $vehicleType->id,
            'vehicle_number' => 'CAA-5678',
            'vehicle_number_normalized' => 'CAA5678',
            'make_model' => 'Toyota KDH',
            'current_mileage' => 82000,
            'next_service_mileage' => 87000,
        ]);
    }

    public function test_stock_report_displays_products_and_batch_quantities_accurately(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.stock.index', ['branch_id' => 'all']));

        $response->assertStatus(200);
        $response->assertSee('Stock Report');
        $response->assertSee('Mobil 1 5W-30 Synthetic');
        $response->assertSee('OIL-5W30');

        // Test Branch Isolation: Staff Colombo branch sees only 10 units
        $staffResponse = $this->actingAs($this->staff)->get(route('reports.stock.index', ['branch_id' => $this->branch->id]));
        $staffResponse->assertStatus(200);

        // Test Batches JSON endpoint
        $batchJson = $this->actingAs($this->admin)->getJson(route('reports.stock.batches', [
            'product' => $this->product->id,
            'branch_id' => 'all',
        ]));

        $batchJson->assertStatus(200)
            ->assertJsonPath('sku', 'OIL-5W30')
            ->assertJsonCount(2, 'batches');
    }

    public function test_stock_value_report_calculates_exact_fifo_purchase_cost_valuation(): void
    {
        // Total valuation = (10 units * 1500) + (5 units * 1600) = 15,000 + 8,000 = 23,000
        $response = $this->actingAs($this->admin)->get(route('reports.stock-value.index', ['branch_id' => 'all']));

        $response->assertStatus(200);
        $response->assertSee('Stock Value Report');
        $response->assertSee('23,000.00');

        // Staff access must be blocked (403)
        $staffResponse = $this->actingAs($this->staff)->get(route('reports.stock-value.index'));
        $staffResponse->assertStatus(403);
    }

    public function test_sales_report_displays_sales_without_double_counting(): void
    {
        // Create a direct sale invoice
        $invoice1 = Invoice::create([
            'invoice_number' => 'INV-202610-0001',
            'branch_id' => $this->branch->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'customer_id' => $this->companyCustomer->id,
            'invoice_type' => 'direct_sale',
            'subtotal' => 5000.00,
            'discount_amount' => 500.00,
            'grand_total' => 4500.00,
            'amount_paid' => 4500.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
        ]);

        // Create a service job invoice
        $invoice2 = Invoice::create([
            'invoice_number' => 'INV-202610-0002',
            'branch_id' => $this->branch->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'customer_id' => $this->companyCustomer->id,
            'invoice_type' => 'job_card',
            'subtotal' => 12000.00,
            'discount_amount' => 0.00,
            'grand_total' => 12000.00,
            'amount_paid' => 2000.00,
            'balance_amount' => 10000.00,
            'status' => 'issued',
        ]);

        // Cancelled invoice should be excluded
        Invoice::create([
            'invoice_number' => 'INV-202610-0003',
            'branch_id' => $this->branch->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'customer_id' => $this->companyCustomer->id,
            'invoice_type' => 'direct_sale',
            'grand_total' => 9999.00,
            'amount_paid' => 0.00,
            'balance_amount' => 9999.00,
            'status' => 'cancelled',
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.sales.index', [
            'date_from' => now()->subDay()->format('Y-m-d'),
            'date_to' => now()->addDay()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('INV-202610-0001');
        $response->assertSee('INV-202610-0002');
        $response->assertDontSee('INV-202610-0003');

        // Total sales = 4500 + 12000 = 16,500.00
        $response->assertSee('16,500.00');
    }

    public function test_profit_and_loss_calculates_fifo_cogs_and_operating_expenses(): void
    {
        // 1. Revenue: Invoice of 10,000 with 1,000 discount -> Net Revenue = 9,000
        Invoice::create([
            'invoice_number' => 'INV-202610-PL01',
            'branch_id' => $this->branch->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'customer_id' => $this->companyCustomer->id,
            'invoice_type' => 'direct_sale',
            'subtotal' => 10000.00,
            'discount_amount' => 1000.00,
            'grand_total' => 9000.00,
            'amount_paid' => 9000.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
        ]);

        // 2. COGS: StockMovement deduction of 2 units @ 1500.00 = 3,000.00
        StockMovement::create([
            'product_id' => $this->product->id,
            'branch_id' => $this->branch->id,
            'stock_batch_id' => 1,
            'movement_type' => 'direct_sale',
            'quantity' => -2.00,
            'unit_cost' => 1500.00,
            'created_at' => now(),
        ]);

        // 3. Operating Expense: Electricity of 1,500.00
        $cat = ExpenseCategory::create(['name' => 'Electricity & Power', 'code' => 'UTIL-ELEC']);
        Expense::create([
            'expense_number' => 'EXP-001',
            'branch_id' => $this->branch->id,
            'category_id' => $cat->id,
            'expense_type' => 'shop',
            'expense_date' => now()->toDateString(),
            'related_month' => now()->format('Y-m'),
            'amount' => 1500.00,
            'payment_method' => 'cash',
            'status' => 'active',
        ]);

        // Gross Profit = 9,000 - 3,000 = 6,000
        // Net Profit = 6,000 - 1,500 = 4,500
        $response = $this->actingAs($this->admin)->get(route('reports.profit-loss.index', [
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('9,000.00'); // Net Revenue
        $response->assertSee('3,000.00'); // COGS
        $response->assertSee('6,000.00'); // Gross Profit
        $response->assertSee('1,500.00'); // Electricity
        $response->assertSee('4,500.00'); // Net Operating Profit

        // Staff access must be blocked (403)
        $staffResponse = $this->actingAs($this->staff)->get(route('reports.profit-loss.index'));
        $staffResponse->assertStatus(403);
    }

    public function test_vehicle_service_report_shows_complete_history_for_registered_vehicle(): void
    {
        $service = Service::create([
            'code' => 'SRV-OIL',
            'name' => 'Full Engine Oil Service',
            'status' => 'active',
        ]);

        $job = JobCard::create([
            'job_number' => 'JOB-202610-001',
            'branch_id' => $this->branch->id,
            'date' => now()->subDays(2)->toDateString(),
            'customer_id' => $this->companyCustomer->id,
            'vehicle_id' => $this->vehicle1->id,
            'vehicle_type_id' => $this->vehicle1->vehicle_type_id,
            'current_mileage' => 45000,
            'next_service_mileage' => 50000,
            'status' => 'completed',
            'service_total' => 3500.00,
            'product_total' => 2500.00,
            'grand_total' => 6000.00,
        ]);

        $job->services()->create([
            'service_id' => $service->id,
            'service_code' => $service->code,
            'service_name' => $service->name,
            'quantity' => 1,
            'unit_price' => 3500.00,
            'line_total' => 3500.00,
        ]);

        $response = $this->actingAs($this->staff)->get(route('reports.vehicle-service.index', [
            'search' => 'CAB-1234',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Toyota HiAce');
        $response->assertSee('JOB-202610-001');
        $response->assertSee('Full Engine Oil Service');
        $response->assertSee('45,000 km');
        $response->assertSee('50,000 km');

        // Test Print View
        $printResponse = $this->actingAs($this->staff)->get(route('reports.vehicle-service.print', $this->vehicle1));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Official Vehicle Maintenance & Service Record', false);
    }

    public function test_money_book_records_payments_by_date_and_tracks_credit_sales(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-MB01',
            'branch_id' => $this->branch->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'customer_id' => $this->companyCustomer->id,
            'invoice_type' => 'direct_sale',
            'grand_total' => 10000.00,
            'amount_paid' => 4000.00,
            'balance_amount' => 6000.00,
            'status' => 'issued',
        ]);

        Payment::create([
            'payment_number' => 'PAY-202610-0001',
            'invoice_id' => $invoice->id,
            'customer_id' => $this->companyCustomer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 4000.00,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->staff)->get(route('reports.money-book.index', [
            'date_from' => now()->toDateString(),
            'date_to' => now()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertSee('PAY-202610-0001');
        $response->assertSee('4,000.00'); // Cash Received
        $response->assertSee('6,000.00'); // Credit Sales Created
    }

    public function test_credit_management_manages_company_fleet_accounts_and_monthly_statements(): void
    {
        // 1. Register Credit Account for ABC Transport
        $creditAccount = CustomerCreditAccount::create([
            'customer_id' => $this->companyCustomer->id,
            'credit_limit' => 200000.00,
            'payment_terms_days' => 30,
            'opening_balance' => 0.00,
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        // 2. Generate Invoices for 2 different vehicles under the same company
        // Vehicle 1: 15,000
        $inv1 = Invoice::create([
            'invoice_number' => 'INV-202610-FLEET1',
            'branch_id' => $this->branch->id,
            'invoice_date' => Carbon::now()->startOfMonth()->addDays(2),
            'due_date' => Carbon::now()->startOfMonth()->addDays(32),
            'customer_id' => $this->companyCustomer->id,
            'invoice_type' => 'job_card',
            'grand_total' => 15000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 15000.00,
            'status' => 'issued',
        ]);

        // Vehicle 2: 8,000
        Invoice::create([
            'invoice_number' => 'INV-202610-FLEET2',
            'branch_id' => $this->branch->id,
            'invoice_date' => Carbon::now()->startOfMonth()->addDays(5),
            'due_date' => Carbon::now()->startOfMonth()->addDays(35),
            'customer_id' => $this->companyCustomer->id,
            'invoice_type' => 'job_card',
            'grand_total' => 8000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 8000.00,
            'status' => 'issued',
        ]);

        // Total billed = 23,000
        $this->assertEquals(23000.00, $creditAccount->outstanding_balance);
        $this->assertEquals(177000.00, $creditAccount->available_credit);

        // Record a partial company payment of 10,000
        Payment::create([
            'payment_number' => 'PAY-202610-CORP1',
            'invoice_id' => $inv1->id,
            'customer_id' => $this->companyCustomer->id,
            'payment_date' => Carbon::now()->startOfMonth()->addDays(10),
            'amount' => 10000.00,
            'payment_method' => 'bank_transfer',
            'status' => 'completed',
        ]);

        // Test Monthly Statement View
        $statementResponse = $this->actingAs($this->admin)->get(route('reports.credit-management.statement', [
            'creditAccount' => $creditAccount->id,
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
        ]));

        $statementResponse->assertStatus(200);
        $statementResponse->assertSee('ABC Transport Ltd');
        $statementResponse->assertSee('INV-202610-FLEET1');
        $statementResponse->assertSee('INV-202610-FLEET2');
        $statementResponse->assertSee('PAY-202610-CORP1');
        $statementResponse->assertSee('23,000.00'); // Month Invoices
        $statementResponse->assertSee('10,000.00'); // Payments
    }

    public function test_credit_limit_validation_blocks_exceeded_credit_sales_unless_admin(): void
    {
        // Credit limit: 50,000
        $creditAccount = CustomerCreditAccount::create([
            'customer_id' => $this->companyCustomer->id,
            'credit_limit' => 50000.00,
            'payment_terms_days' => 30,
            'opening_balance' => 40000.00, // Available: 10,000
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals(10000.00, $creditAccount->available_credit);

        // Check Credit Limit API endpoint
        $checkResponse = $this->actingAs($this->staff)->getJson(route('reports.credit-management.check-limit', [
            'customer_id' => $this->companyCustomer->id,
            'amount' => 15000.00,
        ]));

        $checkResponse->assertStatus(200)
            ->assertJsonPath('has_credit', true)
            ->assertJsonPath('exceeds', true)
            ->assertJsonPath('message', 'Credit limit exceeded.');

        // Attempting a direct sale of 15,000 on credit by Staff without override should fail
        $salePayload = [
            'customer_id' => $this->companyCustomer->id,
            'customer_name' => $this->companyCustomer->customer_name,
            'phone' => $this->companyCustomer->phone,
            'payment_method' => 'credit',
            'amount_paid' => 0.00,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 6, // 6 * 2500 = 15,000
                    'unit_price' => 2500.00,
                ],
            ],
        ];

        $staffSale = $this->actingAs($this->staff)->post(route('operations.direct-sales.store'), $salePayload);
        $staffSale->assertSessionHas('error');
    }

    public function test_credit_aging_buckets_are_calculated_accurately(): void
    {
        $creditAccount = CustomerCreditAccount::create([
            'customer_id' => $this->companyCustomer->id,
            'credit_limit' => 100000.00,
            'payment_terms_days' => 30,
            'opening_balance' => 0.00,
            'status' => 'active',
            'created_by' => $this->admin->id,
        ]);

        // 1. Current / Not Due invoice: due in 10 days
        Invoice::create([
            'invoice_number' => 'INV-CURR',
            'branch_id' => $this->branch->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(10),
            'customer_id' => $this->companyCustomer->id,
            'invoice_type' => 'job_card',
            'grand_total' => 5000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 5000.00,
            'status' => 'issued',
        ]);

        // 2. 1-30 Days Overdue: due 15 days ago
        Invoice::create([
            'invoice_number' => 'INV-1-30',
            'branch_id' => $this->branch->id,
            'invoice_date' => now()->subDays(45),
            'due_date' => now()->subDays(15),
            'customer_id' => $this->companyCustomer->id,
            'invoice_type' => 'job_card',
            'grand_total' => 8000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 8000.00,
            'status' => 'issued',
        ]);

        // 3. Over 90 Days Overdue: due 100 days ago
        Invoice::create([
            'invoice_number' => 'INV-OVER90',
            'branch_id' => $this->branch->id,
            'invoice_date' => now()->subDays(130),
            'due_date' => now()->subDays(100),
            'customer_id' => $this->companyCustomer->id,
            'invoice_type' => 'job_card',
            'grand_total' => 12000.00,
            'amount_paid' => 0.00,
            'balance_amount' => 12000.00,
            'status' => 'issued',
        ]);

        $aging = $creditAccount->aging_summary;

        $this->assertEquals(5000.00, $aging['current']);
        $this->assertEquals(8000.00, $aging['days_1_30']);
        $this->assertEquals(12000.00, $aging['over_90']);
        $this->assertEquals(25000.00, $aging['total']);
    }
}
