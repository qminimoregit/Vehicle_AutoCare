<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DirectSale;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsStage3Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected VehicleType $carType;

    protected VehicleType $suvType;

    protected Service $oilService;

    protected Service $washService;

    protected Product $oilFilterProduct;

    protected UnitOfMeasurement $uom;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->carType = VehicleType::create([
            'name' => 'Car',
            'status' => 'active',
        ]);

        $this->suvType = VehicleType::create([
            'name' => 'Luxury SUV',
            'status' => 'active',
        ]);

        $this->oilService = Service::create([
            'name' => 'Full Engine Oil Service',
            'code' => 'SRV-001',
            'status' => 'active',
        ]);

        $this->washService = Service::create([
            'name' => 'Full Body Wash',
            'code' => 'SRV-002',
            'status' => 'active',
        ]);

        // Configure price for Car: LKR 4,500
        ServiceVehiclePrice::create([
            'service_id' => $this->oilService->id,
            'vehicle_type_id' => $this->carType->id,
            'service_price' => 4500.00,
            'service_cost' => 1500.00,
        ]);

        $this->uom = UnitOfMeasurement::create([
            'name' => 'Piece',
            'code' => 'PCS',
            'abbreviation' => 'pcs',
            'unit_type' => 'count',
            'is_active' => true,
        ]);

        $this->oilFilterProduct = Product::create([
            'name' => 'Toyota Genuine Oil Filter',
            'sku' => 'FLT-TOY-001',
            'barcode' => '8901234567890',
            'uom_id' => $this->uom->id,
            'selling_price' => 2800.00,
            'is_active' => true,
        ]);
    }

    protected function createStockBatch(Product $product, float $quantity, float $cost = 1000.00, ?string $batchNo = null, ?string $purchaseDate = null): StockBatch
    {
        $supplier = Supplier::firstOrCreate(
            ['name' => 'Auto Parts Supplier'],
            ['mobile' => '0770001122', 'is_active' => true]
        );

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-'.uniqid(),
            'supplier_id' => $supplier->id,
            'received_date' => $purchaseDate ?? now()->toDateString(),
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
            'batch_number' => $batchNo ?? ('BATCH-'.uniqid()),
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem->id,
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'purchase_date' => $purchaseDate ?? now()->toDateString(),
            'received_quantity' => $quantity,
            'remaining_quantity' => $quantity,
            'original_purchase_unit_price' => $cost,
            'inventory_unit_cost' => $cost,
            'status' => 'active',
        ]);
    }

    public function test_vehicle_registration_normalization_matches_variations(): void
    {
        $this->assertEquals('CAA1234', Vehicle::normalizeNumber('CAA-1234'));
        $this->assertEquals('CAA1234', Vehicle::normalizeNumber('caa1234'));
        $this->assertEquals('CAA1234', Vehicle::normalizeNumber('CAA 1234'));
        $this->assertEquals('WPCAA1234', Vehicle::normalizeNumber('WP CAA - 1234'));
    }

    public function test_new_vehicle_search_returns_no_previous_history_state(): void
    {
        $response = $this->actingAs($this->staff)->getJson(route('operations.vehicles.search', ['number' => 'WP-CAD-9999']));

        $response->assertOk();
        $response->assertJson([
            'exists' => false,
            'normalized' => 'WPCAD9999',
            'message' => 'This is a new vehicle. Enter the details to start its first job.',
        ]);
    }

    public function test_register_new_vehicle_and_start_job_atomically(): void
    {
        $payload = [
            'vehicle_number' => 'WP CAA-5544',
            'customer_name' => 'Kamal Perera',
            'phone' => '0771234567',
            'vehicle_type_id' => $this->carType->id,
            'current_mileage' => 45000,
            'next_service_mileage' => 50000,
            'customer_notes' => 'First service at this center',
        ];

        $response = $this->actingAs($this->staff)->post(route('operations.vehicles.store'), $payload);

        $this->assertDatabaseHas('customers', [
            'customer_name' => 'Kamal Perera',
            'phone' => '0771234567',
        ]);

        $this->assertDatabaseHas('vehicles', [
            'vehicle_number' => 'WP CAA-5544',
            'vehicle_number_normalized' => 'WPCAA5544',
            'current_mileage' => 45000,
        ]);

        $this->assertDatabaseHas('job_cards', [
            'current_mileage' => 45000,
            'next_service_mileage' => 50000,
            'status' => 'pending',
        ]);

        $job = JobCard::first();
        $this->assertNotNull($job);
        $response->assertRedirect(route('operations.job-cards.show', $job));
    }

    public function test_existing_vehicle_search_returns_real_history_and_ongoing_jobs(): void
    {
        $customer = Customer::create([
            'customer_name' => 'Sunil Silva',
            'phone' => '0719876543',
            'status' => 'active',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_type_id' => $this->carType->id,
            'vehicle_number' => 'CAR-7788',
            'vehicle_number_normalized' => 'CAR7788',
            'current_mileage' => 60000,
        ]);

        // Past completed job
        $pastJob = JobCard::create([
            'job_number' => 'JOB-202601-0001',
            'date' => '2026-01-15',
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $vehicle->vehicle_type_id,
            'current_mileage' => 55000,
            'status' => 'completed',
        ]);

        // Ongoing pending job
        $activeJob = JobCard::create([
            'job_number' => 'JOB-202602-0002',
            'date' => '2026-02-10',
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $vehicle->vehicle_type_id,
            'current_mileage' => 60000,
            'status' => 'pending',
        ]);

        // Search with space: 'car 7788'
        $response = $this->actingAs($this->staff)->getJson(route('operations.vehicles.search', ['number' => 'car 7788']));

        $response->assertOk();
        $response->assertJson([
            'exists' => true,
            'vehicle' => [
                'vehicle_number' => 'CAR-7788',
                'vehicle_number_normalized' => 'CAR7788',
            ],
            'customer' => [
                'customer_name' => 'Sunil Silva',
                'phone' => '0719876543',
            ],
        ]);

        $data = $response->json();
        $this->assertCount(1, $data['ongoing_jobs']);
        $this->assertEquals('JOB-202602-0002', $data['ongoing_jobs'][0]['job_number']);
        $this->assertCount(1, $data['service_history']);
        $this->assertEquals('JOB-202601-0001', $data['service_history'][0]['job_number']);
    }

    public function test_vehicle_registration_prevents_accidental_duplicate_active_jobs(): void
    {
        $customer = Customer::create(['customer_name' => 'Test', 'phone' => '0770000000']);
        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_type_id' => $this->carType->id,
            'vehicle_number' => 'CAB-1111',
            'vehicle_number_normalized' => 'CAB1111',
        ]);

        $activeJob = JobCard::create([
            'job_number' => 'JOB-202601-0099',
            'date' => now()->toDateString(),
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $vehicle->vehicle_type_id,
            'status' => 'pending',
        ]);

        // Attempt to create another job without ignoring active job
        $response = $this->actingAs($this->staff)->post(route('operations.vehicles.store'), [
            'vehicle_number' => 'cab 1111',
            'customer_name' => 'Test',
            'phone' => '0770000000',
            'vehicle_type_id' => $this->carType->id,
        ]);

        // Should redirect to the existing active job instead of duplicating
        $response->assertRedirect(route('operations.job-cards.show', $activeJob));
        $this->assertEquals(1, JobCard::where('vehicle_id', $vehicle->id)->count());
    }

    public function test_job_card_service_pricing_and_price_override(): void
    {
        $customer = Customer::create(['customer_name' => 'Perera', 'phone' => '0771112233']);
        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_type_id' => $this->carType->id,
            'vehicle_number' => 'CAR-1000',
            'vehicle_number_normalized' => 'CAR1000',
        ]);

        $job = JobCard::create([
            'job_number' => 'JOB-202601-0100',
            'date' => now()->toDateString(),
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $vehicle->vehicle_type_id,
            'status' => 'pending',
        ]);

        // 1. Add service with configured price (Car price is 4,500)
        $this->actingAs($this->staff)->post(route('operations.job-cards.services.add', $job), [
            'service_id' => $this->oilService->id,
            'quantity' => 1,
        ]);

        $job->refresh();
        $this->assertEquals(4500.00, (float) $job->service_total);
        $this->assertEquals(4500.00, (float) $job->grand_total);

        // 2. Add unconfigured service (washService has no price for Car) as staff should fail
        $res = $this->actingAs($this->staff)->post(route('operations.job-cards.services.add', $job), [
            'service_id' => $this->washService->id,
            'quantity' => 1,
        ]);
        $res->assertSessionHas('error');

        // 3. Admin can override unconfigured service price
        $this->actingAs($this->admin)->post(route('operations.job-cards.services.add', $job), [
            'service_id' => $this->washService->id,
            'custom_price' => 1800.00,
            'quantity' => 1,
        ]);

        $job->refresh();
        $this->assertEquals(6300.00, (float) $job->service_total);
        $this->assertEquals(6300.00, (float) $job->grand_total);
    }

    public function test_job_card_product_issue_and_reversal_with_fifo(): void
    {
        $customer = Customer::create(['customer_name' => 'Nimal', 'phone' => '0772223344']);
        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_type_id' => $this->carType->id,
            'vehicle_number' => 'CAR-2000',
            'vehicle_number_normalized' => 'CAR2000',
        ]);

        $job = JobCard::create([
            'job_number' => 'JOB-202601-0200',
            'date' => now()->toDateString(),
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $vehicle->vehicle_type_id,
            'status' => 'pending',
        ]);

        // Create Stock Batch: 10 units at cost 1,200
        $batch = $this->createStockBatch($this->oilFilterProduct, 10, 1200.00, 'B26-001');

        // 1. Issue 3 filters to job
        $this->actingAs($this->staff)->post(route('operations.job-cards.products.add', $job), [
            'product_id' => $this->oilFilterProduct->id,
            'quantity' => 3,
            'unit_price' => 2800.00,
        ]);

        $job->refresh();
        $batch->refresh();

        $this->assertEquals(7, (float) $batch->remaining_quantity);
        $this->assertEquals(8400.00, (float) $job->product_total);
        $this->assertEquals(8400.00, (float) $job->grand_total);

        // 2. Remove issued product from job -> stock should be reversed back to 10
        $productItem = $job->products()->first();
        $this->actingAs($this->staff)->delete(route('operations.job-cards.products.remove', [$job, $productItem]));

        $batch->refresh();
        $job->refresh();

        $this->assertEquals(10, (float) $batch->remaining_quantity);
        $this->assertEquals(0, (float) $job->product_total);
    }

    public function test_job_card_workflow_status_and_invoice_generation(): void
    {
        $customer = Customer::create(['customer_name' => 'Ruwan', 'phone' => '0773334455']);
        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_type_id' => $this->carType->id,
            'vehicle_number' => 'CAR-3000',
            'vehicle_number_normalized' => 'CAR3000',
        ]);

        $job = JobCard::create([
            'job_number' => 'JOB-202601-0300',
            'date' => now()->toDateString(),
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $vehicle->vehicle_type_id,
            'status' => 'pending',
        ]);

        // Add Service
        $this->actingAs($this->staff)->post(route('operations.job-cards.services.add', $job), [
            'service_id' => $this->oilService->id,
            'quantity' => 1,
        ]);

        // 1. Start Work -> in_progress
        $this->actingAs($this->staff)->patch(route('operations.job-cards.status', $job), [
            'action' => 'start_work',
        ]);
        $job->refresh();
        $this->assertEquals('in_progress', $job->status);

        // 2. Complete Job -> completed
        $this->actingAs($this->staff)->patch(route('operations.job-cards.status', $job), [
            'action' => 'complete_job',
        ]);
        $job->refresh();
        $this->assertEquals('completed', $job->status);

        // 3. Generate Invoice
        $this->actingAs($this->staff)->post(route('operations.job-cards.generate-invoice', $job));
        $job->refresh();

        $this->assertNotNull($job->invoice_id);
        $invoice = $job->invoice;
        $this->assertEquals(4500.00, (float) $invoice->grand_total);
        $this->assertEquals('job_card', $invoice->invoice_type);
        $this->assertCount(1, $invoice->items);
    }

    public function test_job_card_cancellation_reverses_stock_safely(): void
    {
        $customer = Customer::create(['customer_name' => 'Cancel Test', 'phone' => '0774445566']);
        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_type_id' => $this->carType->id,
            'vehicle_number' => 'CAR-4000',
            'vehicle_number_normalized' => 'CAR4000',
        ]);

        $job = JobCard::create([
            'job_number' => 'JOB-202601-0400',
            'date' => now()->toDateString(),
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $vehicle->vehicle_type_id,
            'status' => 'pending',
        ]);

        $batch = $this->createStockBatch($this->oilFilterProduct, 5, 1200.00, 'B26-002');

        // Issue 2 units to job
        $this->actingAs($this->admin)->post(route('operations.job-cards.products.add', $job), [
            'product_id' => $this->oilFilterProduct->id,
            'quantity' => 2,
        ]);

        $batch->refresh();
        $this->assertEquals(3, (float) $batch->remaining_quantity);

        // Cancel Job as Admin
        $this->actingAs($this->admin)->post(route('operations.job-cards.cancel', $job), [
            'cancellation_reason' => 'Customer changed mind',
        ]);

        $job->refresh();
        $batch->refresh();

        $this->assertEquals('cancelled', $job->status);
        $this->assertEquals('Customer changed mind', $job->cancellation_reason);
        // Stock must be restored to 5!
        $this->assertEquals(5, (float) $batch->remaining_quantity);
    }

    public function test_direct_sale_pos_checkout_with_fifo_and_invoice(): void
    {
        // Batch 1 (older): 2 units at cost 1000
        $batch1 = $this->createStockBatch($this->oilFilterProduct, 2, 1000.00, 'B26-OLD', '2026-01-01');

        // Batch 2 (newer): 5 units at cost 1100
        $batch2 = $this->createStockBatch($this->oilFilterProduct, 5, 1100.00, 'B26-NEW', '2026-01-10');

        // Total available stock is 7
        // Purchase 3 units -> should exhaust Batch 1 (2 units) and take 1 unit from Batch 2
        $payload = [
            'items' => [
                [
                    'product_id' => $this->oilFilterProduct->id,
                    'quantity' => 3,
                    'unit_price' => 2800.00,
                ],
            ],
            'customer_name' => 'Walkin Customer',
            'phone' => '0779998877',
            'payment_method' => 'cash',
            'amount_paid' => 10000.00,
        ];

        $response = $this->actingAs($this->staff)->postJson(route('operations.direct-sales.store'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $batch1->refresh();
        $batch2->refresh();

        // FIFO verification
        $this->assertEquals(0, (float) $batch1->remaining_quantity);
        $this->assertEquals('exhausted', $batch1->status);
        $this->assertEquals(4, (float) $batch2->remaining_quantity);
        $this->assertEquals('active', $batch2->status);

        // Direct Sale & Invoice records
        $sale = DirectSale::first();
        $this->assertNotNull($sale);
        $this->assertEquals(8400.00, (float) $sale->grand_total);
        $this->assertEquals(1600.00, (float) $sale->change_amount);

        $invoice = Invoice::where('invoice_type', 'direct_sale')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(8400.00, (float) $invoice->amount_paid);
    }

    public function test_direct_sale_prevents_overselling(): void
    {
        // 1 unit in stock
        $this->createStockBatch($this->oilFilterProduct, 1, 1000.00, 'B26-ONLY-ONE');

        // Attempt to buy 2 units
        $payload = [
            'items' => [
                [
                    'product_id' => $this->oilFilterProduct->id,
                    'quantity' => 2,
                    'unit_price' => 2800.00,
                ],
            ],
            'payment_method' => 'cash',
            'amount_paid' => 5600.00,
        ];

        $response = $this->actingAs($this->staff)->postJson(route('operations.direct-sales.store'), $payload);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_topbar_shortcuts_are_displayed_and_navigate_correctly(): void
    {
        // 1. Visit Vehicle Registration
        $response = $this->actingAs($this->staff)->get(route('operations.vehicles.index'));
        $response->assertOk();
        $response->assertSee(route('operations.vehicles.index'));
        $response->assertSee(route('operations.direct-sales.index'));
        $response->assertSee(route('operations.job-cards.index'));
        $response->assertSee('Vehicle Registration');
        $response->assertSee('Direct Sale');
        $response->assertSee('Job Cards');

        // 2. Visit Direct Sale
        $response = $this->actingAs($this->staff)->get(route('operations.direct-sales.index'));
        $response->assertOk();
        $response->assertSee('Direct Sale (POS)');

        // 3. Visit Job Cards
        $response = $this->actingAs($this->staff)->get(route('operations.job-cards.index'));
        $response->assertOk();
        $response->assertSee('Job Cards');
    }
}
