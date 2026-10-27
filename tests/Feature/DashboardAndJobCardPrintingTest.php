<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\GoodsReceivedNote;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\BranchContext;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndJobCardPrintingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected Branch $branchColombo;

    protected Branch $branchKandy;

    protected CompanySetting $company;

    protected VehicleType $vehicleType;

    protected Customer $customer;

    protected Vehicle $vehicle;

    protected Service $service1;

    protected Service $service2;

    protected Product $product1;

    protected Product $product2;

    protected JobCard $jobCard;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = CompanySetting::getSettings();

        // 1. Branches
        $this->branchColombo = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-COL',
            'branch_name' => 'Colombo Center',
            'address' => 'Colombo 03',
            'phone' => '0112345678',
            'is_active' => true,
            'is_main_branch' => true,
        ]);

        $this->branchKandy = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-KAN',
            'branch_name' => 'Kandy Branch',
            'address' => 'Peradeniya Rd, Kandy',
            'phone' => '0812345678',
            'is_active' => true,
            'is_main_branch' => false,
        ]);

        // 2. Users
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@autocare.lk',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
            'branch_id' => $this->branchColombo->id,
        ]);

        $this->staff = User::create([
            'name' => 'Staff Technician',
            'email' => 'staff@autocare.lk',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
            'branch_id' => $this->branchColombo->id,
        ]);

        // 3. Master Data
        $this->vehicleType = VehicleType::create([
            'name' => 'Sedan',
            'code' => 'VT-SEDAN',
            'status' => 'active',
        ]);

        $this->customer = Customer::create([
            'customer_name' => 'Kamal Silva',
            'phone' => '0771234567',
            'email' => 'kamal@silva.lk',
            'status' => 'active',
        ]);

        $this->vehicle = Vehicle::create([
            'vehicle_number' => 'CAB-1234',
            'vehicle_number_normalized' => 'CAB1234',
            'customer_id' => $this->customer->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'make_model' => 'Toyota Prius',
            'current_mileage' => 45000,
        ]);

        $this->service1 = Service::create([
            'name' => 'Brake Service & Pad Replacement',
            'code' => 'SRV-BRK',
            'status' => 'active',
        ]);

        $this->service2 = Service::create([
            'name' => 'Full Synthetic Engine Oil Change',
            'code' => 'SRV-OIL',
            'status' => 'active',
        ]);

        ServiceVehiclePrice::create([
            'service_id' => $this->service1->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'service_price' => 3500.00,
        ]);

        ServiceVehiclePrice::create([
            'service_id' => $this->service2->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'service_price' => 4500.00,
        ]);

        $uom = UnitOfMeasurement::create(['name' => 'Piece', 'code' => 'PCS', 'allows_decimal' => false]);
        $supplier = Supplier::create([
            'name' => 'Toyota Lanka',
            'phone' => '0119876543',
            'status' => 'active',
        ]);

        $this->product1 = Product::create([
            'name' => 'Front Brake Pads (Genuine)',
            'sku' => 'BP-GEN-01',
            'uom_id' => $uom->id,
            'selling_price' => 12500.00,
            'is_active' => true,
        ]);

        $this->product2 = Product::create([
            'name' => 'Oil Filter (Genuine)',
            'sku' => 'OF-GEN-01',
            'uom_id' => $uom->id,
            'selling_price' => 2800.00,
            'is_active' => true,
        ]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-TEST-001',
            'supplier_id' => $supplier->id,
            'received_date' => now()->toDateString(),
            'status' => 'confirmed',
            'subtotal' => 202000.00,
            'grand_total' => 202000.00,
            'created_by' => $this->admin->id,
        ]);

        $grnItem1 = $grn->items()->create([
            'product_id' => $this->product1->id,
            'purchase_quantity' => 20,
            'free_quantity' => 0,
            'purchase_unit_price' => 8500.00,
            'selling_price_snapshot' => $this->product1->selling_price,
            'line_total' => 170000.00,
        ]);

        $grnItem2 = $grn->items()->create([
            'product_id' => $this->product2->id,
            'purchase_quantity' => 20,
            'free_quantity' => 0,
            'purchase_unit_price' => 1600.00,
            'selling_price_snapshot' => $this->product2->selling_price,
            'line_total' => 32000.00,
        ]);

        // Stock batch with cost price (cost price must NEVER appear on printouts)
        StockBatch::create([
            'batch_number' => 'BATCH-BP-001',
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem1->id,
            'product_id' => $this->product1->id,
            'supplier_id' => $supplier->id,
            'branch_id' => $this->branchColombo->id,
            'purchase_date' => now()->toDateString(),
            'received_quantity' => 20,
            'remaining_quantity' => 20,
            'original_purchase_unit_price' => 8500.00,
            'inventory_unit_cost' => 8500.00,
            'status' => 'active',
        ]);

        StockBatch::create([
            'batch_number' => 'BATCH-OF-001',
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem2->id,
            'product_id' => $this->product2->id,
            'supplier_id' => $supplier->id,
            'branch_id' => $this->branchColombo->id,
            'purchase_date' => now()->toDateString(),
            'received_quantity' => 20,
            'remaining_quantity' => 20,
            'original_purchase_unit_price' => 1600.00,
            'inventory_unit_cost' => 1600.00,
            'status' => 'active',
        ]);

        // 4. Job Card
        $this->jobCard = JobCard::create([
            'job_number' => 'JOB-202610-0001',
            'branch_id' => $this->branchColombo->id,
            'date' => Carbon::today()->toDateString(),
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'current_mileage' => 45000,
            'next_service_mileage' => 50000,
            'status' => 'in_progress',
            'assigned_to' => $this->staff->id,
            'created_by' => $this->admin->id,
        ]);

        $this->jobCard->services()->create([
            'service_id' => $this->service1->id,
            'service_code' => $this->service1->code,
            'service_name' => $this->service1->name,
            'unit_price' => 3500.00,
            'quantity' => 1,
            'line_total' => 3500.00,
            'technician_name' => 'Staff Technician',
        ]);

        $this->jobCard->services()->create([
            'service_id' => $this->service2->id,
            'service_code' => $this->service2->code,
            'service_name' => $this->service2->name,
            'unit_price' => 4500.00,
            'quantity' => 1,
            'line_total' => 4500.00,
            'technician_name' => 'Staff Technician',
        ]);

        $this->jobCard->products()->create([
            'product_id' => $this->product1->id,
            'product_sku' => $this->product1->sku,
            'product_name' => $this->product1->product_name,
            'quantity' => 1,
            'unit_price' => 12500.00,
            'unit_cost' => 8500.00, // FIFO purchase cost
            'line_total' => 12500.00,
            'is_stock_deducted' => true,
        ]);

        $this->jobCard->products()->create([
            'product_id' => $this->product2->id,
            'product_sku' => $this->product2->sku,
            'product_name' => $this->product2->product_name,
            'quantity' => 1,
            'unit_price' => 2800.00,
            'unit_cost' => 1600.00,
            'line_total' => 2800.00,
            'is_stock_deducted' => true,
        ]);

        $this->jobCard->recalculateTotals();
    }

    public function test_dashboard_quick_actions_are_functional_and_no_longer_coming_soon(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Phase 2 Modules Coming Soon');
        $response->assertDontSee('Coming Soon');

        // Verify active buttons have valid routes
        $response->assertSee(route('operations.vehicles.index'));
        $response->assertSee(route('operations.customers.create'));
        $response->assertSee(route('billing.quotations.create'));
        $response->assertSee(route('billing.invoices.create'));
    }

    public function test_new_job_quick_action_opens_vehicle_workflow(): void
    {
        $response = $this->actingAs($this->admin)->get(route('operations.vehicles.index'));
        $response->assertStatus(200);
        $response->assertSee('Vehicle Registration');
        $response->assertSee('Job Cards');
    }

    public function test_new_customer_quick_action_displays_form_and_registers_customer(): void
    {
        $formResponse = $this->actingAs($this->admin)->get(route('operations.customers.create'));
        $formResponse->assertStatus(200);
        $formResponse->assertSee('New Customer Registration');

        // Store new customer
        $storeResponse = $this->actingAs($this->admin)->post(route('operations.customers.store'), [
            'customer_name' => 'Nimal Jayawardena',
            'phone' => '0719988776',
            'email' => 'nimal@example.com',
            'address' => 'No. 45, Galle Road, Colombo',
            'customer_type' => 'individual',
        ]);

        $storeResponse->assertRedirect(route('operations.vehicles.index', ['customer_id' => Customer::where('phone', '0719988776')->first()->id]));
        $this->assertDatabaseHas('customers', [
            'customer_name' => 'Nimal Jayawardena',
            'phone' => '0719988776',
        ]);
    }

    public function test_customer_registration_prevents_duplicate_phone_number(): void
    {
        // Try registering a customer with Kamal's existing phone (0771234567)
        $storeResponse = $this->actingAs($this->admin)->post(route('operations.customers.store'), [
            'customer_name' => 'Duplicate Kamal',
            'phone' => '0771234567',
        ]);

        $storeResponse->assertRedirect(route('operations.vehicles.index', ['search' => '0771234567']));
        $storeResponse->assertSessionHas('warning');

        // Ensure duplicate record wasn't created
        $this->assertEquals(1, Customer::where('phone', '0771234567')->count());
    }

    public function test_new_quotation_quick_action_opens_quotation_builder(): void
    {
        $response = $this->actingAs($this->admin)->get(route('billing.quotations.create'));
        $response->assertStatus(200);
        $response->assertSee('New Quotation');
    }

    public function test_new_invoice_quick_action_respects_admin_and_staff_permissions(): void
    {
        // Admin gets access to manual invoice
        $adminResponse = $this->actingAs($this->admin)->get(route('billing.invoices.create'));
        $adminResponse->assertStatus(200);

        // Staff is forbidden from direct manual invoice creation
        $staffResponse = $this->actingAs($this->staff)->get(route('billing.invoices.create'));
        $staffResponse->assertStatus(403);

        // On Dashboard, Staff sees invoice workflow modal options
        $dashboardStaff = $this->actingAs($this->staff)->get(route('dashboard'));
        $dashboardStaff->assertStatus(200);
        $dashboardStaff->assertSee('showInvoiceWorkflowModal');
        $dashboardStaff->assertSee(route('operations.job-cards.index', ['status' => 'completed']));
        $dashboardStaff->assertSee(route('operations.direct-sales.index'));
        $dashboardStaff->assertSee(route('billing.quotations.index', ['status' => 'accepted']));
    }

    public function test_dashboard_statistics_use_real_database_records_and_lkr_currency(): void
    {
        // Create an active invoice for today
        Invoice::create([
            'invoice_number' => 'INV-202610-0001',
            'branch_id' => $this->branchColombo->id,
            'invoice_date' => Carbon::today()->toDateString(),
            'customer_id' => $this->customer->id,
            'job_card_id' => $this->jobCard->id,
            'invoice_type' => 'job_card',
            'subtotal' => 23300.00,
            'grand_total' => 23300.00,
            'amount_paid' => 10000.00,
            'balance_amount' => 13300.00,
            'status' => 'partially_paid',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);
        // Verify LKR formatting instead of USD
        $response->assertDontSee('$0.00');
        $response->assertSee('LKR 23,300.00'); // Today's Sales
        $response->assertSee('LKR 13,300.00'); // Pending Payments
        $response->assertSee('1'); // Today's Jobs & Pending Jobs
    }

    public function test_dashboard_statistics_scoped_to_active_branch(): void
    {
        // Switch to Kandy branch
        BranchContext::setActiveBranchId($this->branchKandy->id, $this->admin);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertStatus(200);

        // In Kandy, there are 0 jobs and LKR 0.00 sales
        $response->assertSee('LKR 0.00');
        $this->assertEquals($this->branchKandy->id, BranchContext::getActiveBranchId());
    }

    public function test_print_full_job_card_view_and_pdf_download(): void
    {
        // View printable HTML
        $response = $this->actingAs($this->admin)->get(route('operations.job-cards.print', $this->jobCard));

        $response->assertStatus(200);
        $response->assertSee($this->jobCard->job_number);
        $response->assertSee('CAB-1234');
        $response->assertSee('Kamal Silva');
        $response->assertSee('Brake Service & Pad Replacement');
        $response->assertSee('Front Brake Pads (Genuine)');
        $response->assertSee('JOB CARD');
        $response->assertSee('SERVICES');
        $response->assertSee('PRODUCTS / PARTS');
        $response->assertSee('Back to Job Card');

        // Verify obsolete hardcoded text is removed
        $response->assertDontSee('Work Order • Not a Tax Invoice');
        $response->assertDontSee('AutoCare Workshop Service');
        $response->assertDontSee('Authorized Job Card • Quality Assured');

        // Crucial security requirement: FIFO purchase costs (8500.00) must NEVER be exposed
        $response->assertDontSee('8500.00');

        // Test PDF download stream
        $pdfResponse = $this->actingAs($this->admin)->get(route('operations.job-cards.pdf', $this->jobCard));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('content-type', 'application/pdf');
    }

    public function test_print_selected_items_only_includes_selected_services_and_products(): void
    {
        $service1 = $this->jobCard->services()->first();
        $product2 = $this->jobCard->products()->where('product_id', $this->product2->id)->first();

        // Print ONLY Service 1 and Product 2
        $response = $this->actingAs($this->admin)->get(route('operations.job-cards.print', [
            'jobCard' => $this->jobCard,
            'selected' => '1',
            'services' => [$service1->id],
            'products' => [$product2->id],
        ]));

        $response->assertStatus(200);
        $response->assertSee('JOB CARD');
        $response->assertSee($service1->service_name);
        $response->assertSee($product2->product_name);

        // Excluded items must NOT appear in the selection document
        $response->assertDontSee($this->service2->name);
        $response->assertDontSee($this->product1->product_name);
    }

    public function test_print_selected_items_includes_selected_vehicle_issue(): void
    {
        $issue = $this->jobCard->issues()->create([
            'vehicle_id' => $this->vehicle->id,
            'description' => 'Coolant leak near radiator hose',
            'recommended_repair' => 'Replace top radiator hose and refill coolant',
            'estimated_cost' => 4500.00,
            'status' => 'pending_approval',
            'recorded_by' => $this->staff->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('operations.job-cards.print', [
            'jobCard' => $this->jobCard,
            'selected' => '1',
            'issues' => [$issue->id],
        ]));

        $response->assertStatus(200);
        $response->assertSee('Coolant leak near radiator hose');
        $response->assertSee('Replace top radiator hose and refill coolant');
    }

    public function test_print_selected_items_redirects_with_error_if_nothing_selected(): void
    {
        $response = $this->actingAs($this->admin)->get(route('operations.job-cards.print', [
            'jobCard' => $this->jobCard,
            'selected' => '1',
            'services' => [],
            'products' => [],
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Please select at least one item to print.');
    }

    public function test_newly_added_service_and_product_are_flashed_for_immediate_printing(): void
    {
        $newService = Service::create([
            'name' => 'Wiper Blade Replacement',
            'code' => 'SRV-WPR',
            'status' => 'active',
        ]);
        ServiceVehiclePrice::create([
            'service_id' => $newService->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'service_price' => 1200.00,
        ]);

        $serviceResponse = $this->actingAs($this->admin)->post(route('operations.job-cards.services.add', $this->jobCard), [
            'service_id' => $newService->id,
            'quantity' => 1,
        ]);

        $serviceResponse->assertRedirect();
        $serviceResponse->assertSessionHas('newly_added_service_id');
        $newServiceId = session('newly_added_service_id');
        $this->assertNotNull($newServiceId);

        // Immediate printing of newly added item works
        $printResponse = $this->actingAs($this->admin)->get(route('operations.job-cards.print', [
            'jobCard' => $this->jobCard,
            'selected' => '1',
            'services' => [$newServiceId],
        ]));

        $printResponse->assertStatus(200);
        $printResponse->assertSee('Wiper Blade Replacement');
    }

    public function test_printing_is_strictly_read_only(): void
    {
        $originalStatus = $this->jobCard->status;
        $originalStock = $this->product1->fresh()->available_stock;

        // Perform full print
        $this->actingAs($this->admin)->get(route('operations.job-cards.print', $this->jobCard));

        // Perform selective print
        $this->actingAs($this->admin)->get(route('operations.job-cards.print', [
            'jobCard' => $this->jobCard,
            'selected' => '1',
            'products' => [$this->jobCard->products->first()->id],
        ]));

        // Ensure no status change, no invoice creation, no stock change
        $this->assertEquals($originalStatus, $this->jobCard->fresh()->status);
        $this->assertEquals($originalStock, $this->product1->fresh()->available_stock);
        $this->assertNull($this->jobCard->fresh()->invoice_id);
    }

    public function test_branch_permissions_prevent_unauthorized_cross_branch_printing(): void
    {
        // Another branch's job card
        $kandyJob = JobCard::create([
            'job_number' => 'JOB-KAN-0001',
            'branch_id' => $this->branchKandy->id,
            'date' => now()->toDateString(),
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status' => 'pending',
        ]);

        // Staff assigned to Colombo cannot print Kandy job card
        $response = $this->actingAs($this->staff)->get(route('operations.job-cards.print', $kandyJob));
        $response->assertStatus(403);
    }

    public function test_cancelled_job_printing_authorization_and_watermark(): void
    {
        $this->jobCard->update([
            'status' => 'cancelled',
            'cancellation_reason' => 'Customer postponed repair work.',
            'cancelled_by' => $this->admin->id,
            'cancelled_at' => now(),
        ]);

        // Authorized admin can print historical record
        $response = $this->actingAs($this->admin)->get(route('operations.job-cards.print', $this->jobCard));
        $response->assertStatus(200);
        $response->assertSee('CANCELLED');

        // Staff without cancel permission is blocked
        $staffResponse = $this->actingAs($this->staff)->get(route('operations.job-cards.print', $this->jobCard));
        $staffResponse->assertStatus(403);
    }
}
