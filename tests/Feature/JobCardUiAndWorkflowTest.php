<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\GoodsReceivedNote;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\JobCardProduct;
use App\Models\JobCardService;
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

class JobCardUiAndWorkflowTest extends TestCase
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

        $this->vehicleType = VehicleType::create([
            'name' => 'Sedan',
            'category' => 'car',
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'customer_name' => 'John Doe',
            'phone' => '0771234567',
            'email' => 'john@example.com',
            'is_active' => true,
        ]);

        $this->vehicle = Vehicle::create([
            'vehicle_number' => 'CAB-1234',
            'customer_id' => $this->customer->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'make_model' => 'Toyota Premio',
            'current_mileage' => 45000,
            'next_service_mileage' => 50000,
        ]);

        $this->service = Service::create([
            'code' => 'SRV-OIL',
            'name' => 'Engine Oil Change Service',
            'category' => 'mechanical',
            'status' => 'active',
        ]);

        ServiceVehiclePrice::create([
            'service_id' => $this->service->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'service_price' => 3500.00,
            'service_cost' => 1500.00,
        ]);

        $uom = UnitOfMeasurement::create([
            'name' => 'Units',
            'code' => 'UNT',
            'allows_decimal' => false,
        ]);

        $supplier = Supplier::create([
            'name' => 'Main Supplier',
            'contact_person' => 'Jane',
            'phone' => '0779998877',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Full Synthetic Engine Oil 4L',
            'sku' => 'OIL-FS-001',
            'uom_id' => $uom->id,
            'selling_price' => 12000.00,
            'is_active' => true,
        ]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-202610-0001',
            'branch_id' => $this->branch->id,
            'supplier_id' => $supplier->id,
            'received_date' => now()->toDateString(),
            'status' => 'confirmed',
            'subtotal' => 80000.00,
            'grand_total' => 80000.00,
            'created_by' => $this->admin->id,
        ]);

        $grnItem = $grn->items()->create([
            'product_id' => $this->product->id,
            'purchase_quantity' => 10,
            'free_quantity' => 0,
            'purchase_unit_price' => 8000.00,
            'selling_price_snapshot' => $this->product->selling_price,
            'line_total' => 80000.00,
        ]);

        $this->batch = StockBatch::create([
            'batch_number' => 'BATCH-202610-0001',
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem->id,
            'product_id' => $this->product->id,
            'branch_id' => $this->branch->id,
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'received_quantity' => 10,
            'remaining_quantity' => 10,
            'original_purchase_unit_price' => 8000.00,
            'current_cost_per_unit' => 8000.00,
            'inventory_unit_cost' => 8000.00,
            'status' => 'active',
        ]);
    }

    protected function createTestJobCard(string $status = 'pending'): JobCard
    {
        return JobCard::create([
            'job_number' => 'JOB-202610-TEST',
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'date' => now()->toDateString(),
            'status' => $status,
            'current_mileage' => 45000,
            'next_service_mileage' => 50000,
            'created_by' => $this->admin->id,
        ]);
    }

    /**
     * Requirement 1 & 8: No raw JavaScript/HTML attribute fragments displayed on the page.
     */
    public function test_job_card_page_loads_without_raw_javascript_text(): void
    {
        $jobCard = $this->createTestJobCard('in_progress');

        $response = $this->actingAs($this->admin)->get(route('operations.job-cards.show', $jobCard));

        $response->assertStatus(200);

        // Verify that x-data="jobCardController" is used cleanly
        $response->assertSee('x-data="jobCardController(', false);
        $response->assertSee('window.jobCardController = function(config)', false);

        $content = $response->getContent();

        // Check that the broken JavaScript fragments are NOT in raw text outside of the script block
        $this->assertStringNotContainsString(
            "params.append('services[]', id)); this.selectedProducts.forEach",
            strip_tags($content)
        );

        // Ensure class="max-w-6xl mx-auto space-y-6" is not rendered as visible text
        $this->assertStringNotContainsString(
            'class="max-w-6xl mx-auto space-y-6"',
            strip_tags($content)
        );
    }

    /**
     * Requirement 2: Add Service works and updates totals.
     */
    public function test_add_service_to_job_card(): void
    {
        $jobCard = $this->createTestJobCard('in_progress');

        $response = $this->actingAs($this->admin)->post(
            route('operations.job-cards.services.add', $jobCard),
            [
                'service_id' => $this->service->id,
                'quantity' => 1,
                'technician_name' => 'Kamal Perera',
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('job_card_services', [
            'job_card_id' => $jobCard->id,
            'service_id' => $this->service->id,
            'unit_price' => 3500.00,
            'quantity' => 1.0,
            'line_total' => 3500.00,
            'technician_name' => 'Kamal Perera',
        ]);

        $jobCard->refresh();
        $this->assertEquals(3500.00, (float) $jobCard->service_total);
        $this->assertEquals(3500.00, (float) $jobCard->grand_total);
    }

    /**
     * Requirement 3: Issue Product deducts inventory via FIFO and updates totals.
     */
    public function test_issue_product_deducts_fifo_batch_and_updates_totals(): void
    {
        $jobCard = $this->createTestJobCard('in_progress');

        $response = $this->actingAs($this->admin)->post(
            route('operations.job-cards.products.add', $jobCard),
            [
                'product_id' => $this->product->id,
                'quantity' => 2,
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check product item created on job card
        $this->assertDatabaseHas('job_card_products', [
            'job_card_id' => $jobCard->id,
            'product_id' => $this->product->id,
            'quantity' => 2.0,
            'unit_price' => 12000.00,
            'line_total' => 24000.00,
            'is_stock_deducted' => true,
        ]);

        // Check FIFO batch deducted
        $this->batch->refresh();
        $this->assertEquals(8.0, (float) $this->batch->remaining_quantity);

        // Check Job Card totals
        $jobCard->refresh();
        $this->assertEquals(24000.00, (float) $jobCard->product_total);
        $this->assertEquals(24000.00, (float) $jobCard->grand_total);
    }

    /**
     * Requirement 3 (Safety): Cannot issue more product than available stock.
     */
    public function test_cannot_issue_product_exceeding_available_stock(): void
    {
        $jobCard = $this->createTestJobCard('in_progress');

        $response = $this->actingAs($this->admin)->post(
            route('operations.job-cards.products.add', $jobCard),
            [
                'product_id' => $this->product->id,
                'quantity' => 50, // Available is only 10
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Batch remains unchanged
        $this->batch->refresh();
        $this->assertEquals(10.0, (float) $this->batch->remaining_quantity);
    }

    /**
     * Requirement 4: Add Issue stores issue against job card and vehicle.
     */
    public function test_add_vehicle_issue_and_record_customer_decision(): void
    {
        $jobCard = $this->createTestJobCard('in_progress');

        // 1. Add issue
        $response = $this->actingAs($this->admin)->post(
            route('operations.job-cards.issues.store', $jobCard),
            [
                'description' => 'Brake pads worn below safety limit',
                'recommended_repair' => 'Replace front ceramic brake pads',
                'estimated_cost' => 8500.00,
                'remarks' => 'Found during routine inspection',
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $issue = JobIssue::where('job_card_id', $jobCard->id)->first();
        $this->assertNotNull($issue);
        $this->assertEquals('pending_approval', $issue->status);
        $this->assertEquals($jobCard->vehicle_id, $issue->vehicle_id);

        // 2. Customer approval
        $approvalResponse = $this->actingAs($this->admin)->patch(
            route('operations.job-issues.approval', $issue),
            [
                'customer_decision' => 'approved',
                'approval_method' => 'phone',
                'approval_date' => now()->toDateString(),
                'approval_contact' => 'John Doe (0771234567)',
                'approval_note' => 'Customer approved over the phone',
            ]
        );

        $approvalResponse->assertRedirect();
        $approvalResponse->assertSessionHas('success');

        $issue->refresh();
        $this->assertEquals('approved', $issue->status);
        $this->assertEquals('phone', $issue->approval_method);
    }

    /**
     * Requirement 5: Edit Info updates job details without creating duplicate job card.
     */
    public function test_edit_job_info_updates_record_cleanly(): void
    {
        $jobCard = $this->createTestJobCard('in_progress');
        $initialCount = JobCard::count();

        $response = $this->actingAs($this->admin)->put(
            route('operations.job-cards.update', $jobCard),
            [
                'assigned_to' => $this->admin->id,
                'current_mileage' => 46200,
                'next_service_mileage' => 51200,
                'customer_notes' => 'Please check AC cooling',
                'service_notes' => 'Tested suspension; all good',
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify no duplicate job card created
        $this->assertEquals($initialCount, JobCard::count());

        $jobCard->refresh();
        $this->assertEquals($this->admin->id, $jobCard->assigned_to);
        $this->assertEquals(46200, $jobCard->current_mileage);
        $this->assertEquals(51200, $jobCard->next_service_mileage);
        $this->assertEquals('Please check AC cooling', $jobCard->customer_notes);
        $this->assertEquals('Tested suspension; all good', $jobCard->service_notes);

        // Vehicle mileage updated as well
        $this->vehicle->refresh();
        $this->assertEquals(46200, $this->vehicle->current_mileage);
    }

    /**
     * Requirement 6: Print Full Job Card returns valid print view.
     */
    public function test_print_full_job_card(): void
    {
        $jobCard = $this->createTestJobCard('in_progress');

        // Add service and product
        $jobCard->services()->create([
            'service_id' => $this->service->id,
            'service_code' => $this->service->code,
            'service_name' => $this->service->name,
            'unit_price' => 3500.00,
            'quantity' => 1,
            'line_total' => 3500.00,
        ]);

        $jobCard->recalculateTotals();

        $response = $this->actingAs($this->admin)->get(route('operations.job-cards.print', $jobCard));

        $response->assertStatus(200);
        $response->assertSee($jobCard->job_number);
        $response->assertSee('Toyota Premio');
        $response->assertSee('Engine Oil Change Service');
        $response->assertSee('3,500.00');
    }

    /**
     * Requirement 7: Print Selected Items prints only checked items.
     */
    public function test_print_selected_items_filters_services_and_products(): void
    {
        $jobCard = $this->createTestJobCard('in_progress');

        $srv1 = $jobCard->services()->create([
            'service_id' => $this->service->id,
            'service_code' => 'SRV-1',
            'service_name' => 'Selected Service',
            'unit_price' => 2000.00,
            'quantity' => 1,
            'line_total' => 2000.00,
        ]);

        $srv2 = $jobCard->services()->create([
            'service_id' => $this->service->id,
            'service_code' => 'SRV-2',
            'service_name' => 'Unselected Service',
            'unit_price' => 5000.00,
            'quantity' => 1,
            'line_total' => 5000.00,
        ]);

        $prod1 = $jobCard->products()->create([
            'product_id' => $this->product->id,
            'product_sku' => 'SKU-SEL',
            'product_name' => 'Selected Oil Filter',
            'quantity' => 1,
            'unit_price' => 1500.00,
            'line_total' => 1500.00,
        ]);

        $jobCard->recalculateTotals();

        // Selective print with srv1 and prod1
        $response = $this->actingAs($this->admin)->get(route('operations.job-cards.print', [
            'jobCard' => $jobCard,
            'selected' => 1,
            'services' => [$srv1->id],
            'products' => [$prod1->id],
        ]));

        $response->assertStatus(200);
        $response->assertSee('Selected Service');
        $response->assertSee('Selected Oil Filter');
        $response->assertDontSee('Unselected Service');

        // Grand total should be 2000 + 1500 = 3500, not the full 8500
        $response->assertSee('3,500.00');
    }

    /**
     * Requirement 7 (Validation): Empty selection is rejected with error.
     */
    public function test_print_selected_items_with_empty_selection_is_rejected(): void
    {
        $jobCard = $this->createTestJobCard('in_progress');

        $response = $this->actingAs($this->admin)->get(route('operations.job-cards.print', [
            'jobCard' => $jobCard,
            'selected' => 1,
            'services' => [],
            'products' => [],
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Please select at least one service or product to print.');
    }

    /**
     * Requirement 10: Complete Workflow from Pending -> Start Work -> Complete Job -> Additional Work -> Invoice.
     */
    public function test_full_job_card_workflow_status_transitions(): void
    {
        // 1. Pending status: Start work
        $jobCard = $this->createTestJobCard('pending');
        $this->assertEquals('pending', $jobCard->status);

        $startResponse = $this->actingAs($this->admin)->patch(
            route('operations.job-cards.status', $jobCard),
            ['action' => 'start_work']
        );
        $startResponse->assertRedirect();
        $jobCard->refresh();
        $this->assertEquals('in_progress', $jobCard->status);

        // 2. Add work and complete job
        $jobCard->services()->create([
            'service_id' => $this->service->id,
            'service_code' => $this->service->code,
            'service_name' => $this->service->name,
            'unit_price' => 3500.00,
            'quantity' => 1,
            'line_total' => 3500.00,
        ]);
        $jobCard->recalculateTotals();

        $completeResponse = $this->actingAs($this->admin)->patch(
            route('operations.job-cards.status', $jobCard),
            ['action' => 'complete_job']
        );
        $completeResponse->assertRedirect();
        $jobCard->refresh();
        $this->assertEquals('completed', $jobCard->status);

        // 3. Record issue and approve it for additional work
        $issue = $jobCard->issues()->create([
            'vehicle_id' => $jobCard->vehicle_id,
            'description' => 'Rear shock leak',
            'recommended_repair' => 'Replace rear shock absorbers',
            'status' => 'approved',
            'recorded_by' => $this->admin->id,
        ]);

        $additionalWorkResponse = $this->actingAs($this->admin)->post(
            route('operations.job-cards.additional-work.start', $jobCard)
        );
        $additionalWorkResponse->assertRedirect();
        $jobCard->refresh();
        $this->assertEquals('additional_work', $jobCard->status);

        // Complete additional work
        $completeAdditionalResponse = $this->actingAs($this->admin)->post(
            route('operations.job-cards.additional-work.complete', $jobCard)
        );
        $completeAdditionalResponse->assertRedirect();
        $jobCard->refresh();
        $this->assertEquals('completed', $jobCard->status);

        // 4. Generate Invoice
        $invoiceResponse = $this->actingAs($this->admin)->post(
            route('operations.job-cards.generate-invoice', $jobCard)
        );
        $invoiceResponse->assertRedirect();
        $jobCard->refresh();
        $this->assertNotNull($jobCard->invoice_id);
    }
}
