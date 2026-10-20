<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\GoodsReceivedNote;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\JobIssue;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Models\UnitOfMeasurement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobCardImprovementsAndAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;
    protected CompanySetting $company;
    protected VehicleType $vehicleType;
    protected Customer $customer;
    protected Vehicle $vehicle;
    protected Service $service;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = CompanySetting::getSettings();

        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-01',
            'branch_name' => 'Main AutoCare Center',
            'address' => 'Colombo',
            'phone' => '0112345678',
            'is_active' => true,
            'is_main_branch' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@autocare.lk',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
            'branch_id' => $this->branch->id,
        ]);

        $this->vehicleType = VehicleType::create([
            'code' => 'SEDAN',
            'name' => 'Sedan',
            'status' => 'active',
        ]);

        $this->customer = Customer::create([
            'customer_name' => 'Adeesh Perera',
            'phone' => '0771234567',
            'customer_type' => 'individual',
            'status' => 'active',
        ]);

        $this->vehicle = Vehicle::create([
            'customer_id' => $this->customer->id,
            'vehicle_number' => 'BFH-4242',
            'vehicle_number_normalized' => 'BFH4242',
            'vehicle_type_id' => $this->vehicleType->id,
            'current_mileage' => 45000,
            'status' => 'active',
        ]);

        $this->service = Service::create([
            'code' => 'SRV-OIL',
            'name' => 'Full Oil Service',
            'status' => 'active',
        ]);

        ServiceVehiclePrice::create([
            'service_id' => $this->service->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'service_price' => 5000.00,
            'service_cost' => 3000.00,
        ]);

        $uom = UnitOfMeasurement::create([
            'name' => 'Liters',
            'code' => 'LTR',
            'abbreviation' => 'ltr',
            'unit_type' => 'liquid',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Synthetic Motor Oil 5W30',
            'sku' => 'OIL-001',
            'barcode' => '8901234567891',
            'uom_id' => $uom->id,
            'selling_price' => 2500.00,
            'is_active' => true,
        ]);

        // Add initial stock batch
        $supplier = Supplier::create([
            'name' => 'Mobil Sri Lanka',
            'contact_person' => 'Sunil',
            'mobile' => '0777000111',
            'is_active' => true,
        ]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-TEST-001',
            'supplier_id' => $supplier->id,
            'received_date' => now()->toDateString(),
            'status' => 'confirmed',
            'subtotal' => 100000,
            'grand_total' => 100000,
            'created_by' => $this->admin->id,
        ]);

        $grnItem = $grn->items()->create([
            'product_id' => $this->product->id,
            'purchase_quantity' => 50,
            'free_quantity' => 0,
            'purchase_unit_price' => 1800.00,
            'selling_price_snapshot' => $this->product->selling_price,
            'line_total' => 90000.00,
        ]);

        StockBatch::create([
            'batch_number' => 'BATCH-TEST-001',
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem->id,
            'product_id' => $this->product->id,
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'received_quantity' => 50,
            'remaining_quantity' => 50,
            'original_purchase_unit_price' => 1800.00,
            'current_cost_per_unit' => 1800.00,
            'inventory_unit_cost' => 1800.00,
            'status' => 'active',
        ]);
    }

    /**
     * PART 1: Notification deduplication test
     */
    public function test_single_notification_is_rendered_and_no_duplicate_alert_components_in_job_card_view(): void
    {
        $this->actingAs($this->admin);

        $jobCard = JobCard::create([
            'job_number' => JobCard::generateJobNumber(),
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'pending',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->withSession(['success' => 'Job Card created successfully.'])
            ->get(route('operations.job-cards.show', $jobCard));

        $response->assertStatus(200);

        // Verify the message appears in the response exactly once
        $content = $response->getContent();
        $this->assertEquals(1, substr_count($content, 'Job Card created successfully.'));
    }

    /**
     * PART 2: Record Vehicle Issue on Pending, In-Progress, and Completed Job Cards
     */
    public function test_can_record_vehicle_issue_on_pending_job_card(): void
    {
        $this->actingAs($this->admin);

        $jobCard = JobCard::create([
            'job_number' => JobCard::generateJobNumber(),
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'pending',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->post(route('operations.job-cards.issues.store', $jobCard), [
            'description' => 'Front brake pads worn out',
            'recommended_repair' => 'Replace front brake pads',
            'estimated_cost' => 12000.00,
            'remarks' => 'Found during inspection',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('job_issues', [
            'job_card_id' => $jobCard->id,
            'vehicle_id' => $this->vehicle->id,
            'description' => 'Front brake pads worn out',
            'recommended_repair' => 'Replace front brake pads',
            'status' => 'pending_approval',
        ]);
    }

    public function test_can_record_customer_approval_and_rejection(): void
    {
        $this->actingAs($this->admin);

        $jobCard = JobCard::create([
            'job_number' => JobCard::generateJobNumber(),
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'in_progress',
            'created_by' => $this->admin->id,
        ]);

        $issue = JobIssue::create([
            'job_card_id' => $jobCard->id,
            'vehicle_id' => $this->vehicle->id,
            'description' => 'Brake fluid leaking',
            'recommended_repair' => 'Replace brake line seal',
            'estimated_cost' => 4500,
            'status' => 'pending_approval',
            'recorded_by' => $this->admin->id,
        ]);

        // Record approval
        $response = $this->patch(route('operations.job-issues.approval', $issue), [
            'customer_decision' => 'approved',
            'approval_method' => 'phone',
            'approval_date' => now()->toDateString(),
            'approval_contact' => '0771234567',
            'approval_note' => 'Customer authorized via phone call',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $issue->refresh();
        $this->assertEquals('approved', $issue->status);
        $this->assertEquals('phone', $issue->approval_method);
        $this->assertEquals($this->admin->id, $issue->approved_by);

        // Record rejection / decline
        $responseDecline = $this->patch(route('operations.job-issues.approval', $issue), [
            'customer_decision' => 'declined',
            'approval_note' => 'Customer declined additional cost',
        ]);

        $responseDecline->assertRedirect();
        $issue->refresh();
        $this->assertEquals('declined', $issue->status);
    }

    /**
     * Scenario A: Additional repair on completed job before invoice is generated
     */
    public function test_scenario_a_additional_repair_on_completed_unbilled_job(): void
    {
        $this->actingAs($this->admin);

        $jobCard = JobCard::create([
            'job_number' => JobCard::generateJobNumber(),
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        // Add original service
        $jobCard->services()->create([
            'service_id' => $this->service->id,
            'service_code' => $this->service->code,
            'service_name' => $this->service->name,
            'unit_price' => 5000,
            'quantity' => 1,
            'line_total' => 5000,
            'is_price_custom' => false,
            'is_additional' => false,
        ]);
        $jobCard->recalculateTotals();

        // Customer approves an issue
        $issue = JobIssue::create([
            'job_card_id' => $jobCard->id,
            'vehicle_id' => $this->vehicle->id,
            'description' => 'Worn brake pads',
            'recommended_repair' => 'Replace brake pads',
            'status' => 'approved',
            'recorded_by' => $this->admin->id,
            'approved_by' => $this->admin->id,
        ]);

        // 1. Start Additional Work
        $responseStart = $this->post(route('operations.job-cards.additional-work.start', $jobCard));
        $responseStart->assertRedirect();
        $jobCard->refresh();
        $this->assertEquals('additional_work', $jobCard->status);

        // 2. Add product / part with FIFO deduction
        $responseAddProd = $this->post(route('operations.job-cards.products.add', $jobCard), [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 2500,
            'job_issue_id' => $issue->id,
        ]);
        $responseAddProd->assertRedirect();
        $responseAddProd->assertSessionHas('success');

        $jobCard->refresh();
        $this->assertEquals(10000, $jobCard->grand_total); // 5000 service + 5000 parts
        $this->assertEquals(48, $this->product->stockBatches()->first()->remaining_quantity); // Deducted 2

        // 3. Complete Additional Work
        $responseComplete = $this->post(route('operations.job-cards.additional-work.complete', $jobCard));
        $responseComplete->assertRedirect();
        $jobCard->refresh();
        $this->assertEquals('completed', $jobCard->status);

        $issue->refresh();
        $this->assertEquals('fixed', $issue->status);

        // 4. Generate Single Final Invoice for all billable work
        $responseInvoice = $this->post(route('operations.job-cards.generate-invoice', $jobCard));
        $responseInvoice->assertRedirect();

        $jobCard->refresh();
        $this->assertNotNull($jobCard->invoice_id);
        $this->assertEquals(10000, $jobCard->invoice->grand_total);
        $this->assertEquals(2, $jobCard->invoice->items()->count());
    }

    /**
     * Scenario B: Additional repair on completed job when invoice was already issued
     */
    public function test_scenario_b_additional_repair_with_supplementary_invoice(): void
    {
        $this->actingAs($this->admin);

        $jobCard = JobCard::create([
            'job_number' => JobCard::generateJobNumber(),
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'completed',
            'created_by' => $this->admin->id,
        ]);

        $originalService = $jobCard->services()->create([
            'service_id' => $this->service->id,
            'service_code' => $this->service->code,
            'service_name' => $this->service->name,
            'unit_price' => 5000,
            'quantity' => 1,
            'line_total' => 5000,
            'is_price_custom' => false,
            'is_additional' => false,
        ]);
        $jobCard->recalculateTotals();

        // Generate initial invoice
        $this->post(route('operations.job-cards.generate-invoice', $jobCard));
        $jobCard->refresh();
        $originalInvoice = $jobCard->invoice;
        $this->assertNotNull($originalInvoice);
        $this->assertEquals(5000, $originalInvoice->grand_total);

        // Now customer approves an additional repair
        $issue = JobIssue::create([
            'job_card_id' => $jobCard->id,
            'vehicle_id' => $this->vehicle->id,
            'description' => 'Replace wiper blades',
            'recommended_repair' => 'New wiper blades',
            'status' => 'approved',
            'recorded_by' => $this->admin->id,
        ]);

        // Start Additional Work
        $this->post(route('operations.job-cards.additional-work.start', $jobCard));
        $jobCard->refresh();
        $this->assertEquals('additional_work', $jobCard->status);

        // Add additional parts
        $this->post(route('operations.job-cards.products.add', $jobCard), [
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 2500,
        ]);

        // Complete additional work
        $this->post(route('operations.job-cards.additional-work.complete', $jobCard));
        $jobCard->refresh();
        $this->assertEquals('completed', $jobCard->status);

        // Generate Supplementary Invoice
        $responseSup = $this->post(route('operations.job-cards.supplementary-invoice', $jobCard));
        $responseSup->assertRedirect();

        // Verify original invoice is completely untouched
        $originalInvoice->refresh();
        $this->assertEquals(5000, $originalInvoice->grand_total);
        $this->assertEquals(1, $originalInvoice->items()->count());

        // Verify supplementary invoice created
        $supplementaryInvoices = $jobCard->supplementaryInvoices;
        $this->assertCount(1, $supplementaryInvoices);
        $supInvoice = $supplementaryInvoices->first();
        $this->assertEquals(2500, $supInvoice->grand_total);
        $this->assertEquals("{$jobCard->job_number}-SUP", $supInvoice->reference);
    }

    /**
     * Follow-up Job Card for delivered vehicle
     */
    public function test_can_create_follow_up_job_card_for_delivered_vehicle(): void
    {
        $this->actingAs($this->admin);

        $jobCard = JobCard::create([
            'job_number' => JobCard::generateJobNumber(),
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => 'delivered',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->post(route('operations.job-cards.follow-up', $jobCard));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('job_cards', [
            'vehicle_id' => $this->vehicle->id,
            'customer_id' => $this->customer->id,
            'status' => 'pending',
            'service_notes' => "Follow-up visit for previous Job #{$jobCard->job_number}",
        ]);
    }

    /**
     * PART 3: Appointment Booking, Conflict Check, Arrive, and Convert to Job Card
     */
    public function test_can_schedule_appointment_and_auto_link_existing_vehicle(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('appointments.store'), [
            'vehicle_number' => 'BFH-4242',
            'customer_name' => 'Adeesh Perera',
            'mobile_number' => '0771234567',
            'vehicle_type_id' => $this->vehicleType->id,
            'service_id' => $this->service->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'appointment_time' => '10:00 AM',
            'notes' => 'Customer requested synthetic oil',
        ]);

        $response->assertRedirect(route('appointments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'vehicle_id' => $this->vehicle->id,
            'customer_id' => $this->customer->id,
            'vehicle_number' => 'BFH-4242',
            'appointment_time' => '10:00 AM',
            'status' => 'scheduled',
        ]);
    }

    public function test_appointment_calendar_events_json_feed(): void
    {
        $this->actingAs($this->admin);

        $appointment = Appointment::create([
            'appointment_number' => Appointment::generateAppointmentNumber(),
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'customer_name' => 'Adeesh Perera',
            'mobile_number' => '0771234567',
            'vehicle_number' => 'BFH-4242',
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '09:00 AM',
            'status' => 'scheduled',
        ]);

        $response = $this->getJson(route('appointments.events'));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'appointment_number' => $appointment->appointment_number,
            'vehicle_number' => 'BFH-4242',
            'status' => 'scheduled',
        ]);
    }

    public function test_appointment_to_job_card_conversion(): void
    {
        $this->actingAs($this->admin);

        $appointment = Appointment::create([
            'appointment_number' => Appointment::generateAppointmentNumber(),
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'service_id' => $this->service->id,
            'customer_name' => 'Adeesh Perera',
            'mobile_number' => '0771234567',
            'vehicle_number' => 'BFH-4242',
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '09:00 AM',
            'status' => 'scheduled',
        ]);

        // Customer arrived
        $responseArrived = $this->patch(route('appointments.arrive', $appointment));
        $responseArrived->assertRedirect();
        $appointment->refresh();
        $this->assertEquals('arrived', $appointment->status);

        // Create Job Card
        $responseJob = $this->post(route('appointments.create-job-card', $appointment));
        $responseJob->assertRedirect();

        $appointment->refresh();
        $this->assertNotNull($appointment->job_card_id);
        $this->assertEquals('in_service', $appointment->status);

        $jobCard = $appointment->jobCard;
        $this->assertNotNull($jobCard);
        $this->assertEquals($this->vehicle->id, $jobCard->vehicle_id);
        $this->assertEquals($appointment->id, $jobCard->appointment_id);
        $this->assertEquals(1, $jobCard->services()->count()); // Auto-added service
        $this->assertEquals(5000, $jobCard->grand_total);

        // Attempting to create duplicate job card redirects to existing
        $responseDup = $this->post(route('appointments.create-job-card', $appointment));
        $responseDup->assertRedirect(route('operations.job-cards.show', $jobCard));
    }

    /**
     * PART 4: Exact Sidebar Order Test
     */
    public function test_sidebar_has_exact_required_sections_and_order(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Check exact order of parent sections
        $posDashboard = strpos($content, 'Dashboard</span>');
        $posAppointments = strpos($content, 'Appointments</span>');
        $posOperations = strpos($content, 'Operations</span>');
        $posInventory = strpos($content, 'Suppliers & Inventory</span>');
        $posBilling = strpos($content, 'Sales & Billing</span>');
        $posAdjustments = strpos($content, 'Invoice Adjustments</span>');
        $posPayroll = strpos($content, 'Employees & Payroll</span>');
        $posExpenses = strpos($content, 'Expenses</span>');
        $posReports = strpos($content, 'Reports & Credit</span>');
        $posMasterData = strpos($content, 'Master Data</span>');

        $this->assertNotFalse($posDashboard);
        $this->assertNotFalse($posAppointments);
        $this->assertNotFalse($posOperations);
        $this->assertNotFalse($posInventory);
        $this->assertNotFalse($posBilling);
        $this->assertNotFalse($posAdjustments);
        $this->assertNotFalse($posPayroll);
        $this->assertNotFalse($posExpenses);
        $this->assertNotFalse($posReports);
        $this->assertNotFalse($posMasterData);

        // Check required exact sequence:
        // 1. Dashboard < 2. Appointments < 3. Operations < 4. Suppliers & Inventory < 5. Sales & Billing < 6. Invoice Adjustments < 7. Employees & Payroll < 8. Expenses < 9. Reports & Credit < 10. Master Data
        $this->assertTrue($posDashboard < $posAppointments, 'Dashboard must appear before Appointments');
        $this->assertTrue($posAppointments < $posOperations, 'Appointments must appear before Operations');
        $this->assertTrue($posOperations < $posInventory, 'Operations must appear before Suppliers & Inventory');
        $this->assertTrue($posInventory < $posBilling, 'Suppliers & Inventory must appear before Sales & Billing');
        $this->assertTrue($posBilling < $posAdjustments, 'Sales & Billing must appear before Invoice Adjustments');
        $this->assertTrue($posAdjustments < $posPayroll, 'Invoice Adjustments must appear before Employees & Payroll');
        $this->assertTrue($posPayroll < $posExpenses, 'Employees & Payroll must appear before Expenses');
        $this->assertTrue($posExpenses < $posReports, 'Expenses must appear before Reports & Credit');
        $this->assertTrue($posReports < $posMasterData, 'Reports & Credit must appear before Master Data');

        // Check Administration appears below Master Data
        $posStaff = strpos($content, 'Staff Accounts</span>');
        $posLogs = strpos($content, 'Activity Logs</span>');
        $this->assertNotFalse($posStaff);
        $this->assertNotFalse($posLogs);
        $this->assertTrue($posMasterData < $posStaff, 'Master Data must appear before Staff Accounts');
    }

    /**
     * Regression Test: Ensure Branch::where('is_active', 1)->orderBy('name')->get()
     * executes without error and correctly filters only active branches.
     */
    public function test_appointment_index_loads_successfully_with_active_branches_query(): void
    {
        $this->actingAs($this->admin);

        // Create a second active branch and an inactive branch
        $activeBranch2 = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-02',
            'branch_name' => 'Kandy Branch',
            'address' => 'Kandy',
            'phone' => '0812345678',
            'is_active' => true,
            'is_main_branch' => false,
        ]);

        $inactiveBranch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-03',
            'branch_name' => 'Galle Branch (Closed)',
            'address' => 'Galle',
            'phone' => '0912345678',
            'is_active' => false,
            'is_main_branch' => false,
        ]);

        // Explicitly test the required query requested by the user
        $branches = Branch::where('is_active', 1)->orderBy('name')->get();
        $this->assertTrue($branches->contains('id', $this->branch->id));
        $this->assertTrue($branches->contains('id', $activeBranch2->id));
        $this->assertFalse($branches->contains('id', $inactiveBranch->id));

        // Test GET /appointments loads successfully with HTTP 200
        $response = $this->get(route('appointments.index'));
        $response->assertStatus(200);
        $response->assertViewHas('branches', function ($viewBranches) use ($inactiveBranch) {
            return ! $viewBranches->contains('id', $inactiveBranch->id);
        });
        $response->assertSee('Main AutoCare Center');
        $response->assertSee('Kandy Branch');
        $response->assertDontSee('Galle Branch (Closed)');
    }

    /**
     * Test scheduling appointment with a specific branch selected by Admin.
     */
    public function test_can_schedule_appointment_with_selected_branch(): void
    {
        $this->actingAs($this->admin);

        $branch2 = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-02',
            'branch_name' => 'Kandy Branch',
            'is_active' => true,
            'is_main_branch' => false,
        ]);

        $response = $this->post(route('appointments.store'), [
            'vehicle_number' => 'BFH-4242',
            'customer_name' => 'Adeesh Perera',
            'mobile_number' => '0771234567',
            'vehicle_type_id' => $this->vehicleType->id,
            'service_id' => $this->service->id,
            'appointment_date' => now()->addDays(3)->toDateString(),
            'appointment_time' => '11:00 AM',
            'branch_id' => $branch2->id,
        ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', [
            'vehicle_number' => 'BFH-4242',
            'branch_id' => $branch2->id,
            'status' => 'scheduled',
        ]);
    }

    /**
     * Test layout order: Appointment Header -> Appointment List -> Appointment Calendar
     */
    public function test_appointment_page_layout_order_list_above_calendar(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('appointments.index'));
        $response->assertStatus(200);

        $content = $response->getContent();
        $posHeader = strpos($content, 'Appointment Management');
        $posList = strpos($content, '<!-- 1. APPOINTMENT LIST & QUICK FILTERS -->');
        $posCalendar = strpos($content, '<!-- 2. APPOINTMENT CALENDAR CARD -->');

        $this->assertNotFalse($posHeader, 'Appointment Management header must be present');
        $this->assertNotFalse($posList, 'Appointment List section must be present');
        $this->assertNotFalse($posCalendar, 'Appointment Calendar section must be present');

        $this->assertTrue($posHeader < $posList, 'Header must appear before Appointment List');
        $this->assertTrue($posList < $posCalendar, 'Appointment List must appear ABOVE the Calendar');
    }
}
