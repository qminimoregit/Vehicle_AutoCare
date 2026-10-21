<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CarpetCategory;
use App\Models\Customer;
use App\Models\CustomerCreditAccount;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleAutoFillAndCarpetDetailsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Branch $branch;
    protected VehicleType $carType;
    protected VehicleType $bikeType;
    protected CarpetCategory $frontCarpet;
    protected CarpetCategory $rearCarpet;
    protected CarpetCategory $bootCarpet;

    protected function setUp(): void
    {
        parent::setUp();

        $company = \App\Models\CompanySetting::getSettings();

        $this->branch = Branch::create([
            'company_id' => $company->id,
            'branch_code' => 'BR-MAIN',
            'branch_name' => 'Main Workshop',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();
        $this->staff->branches()->attach($this->branch->id);

        // Carpet categories
        $this->frontCarpet = CarpetCategory::firstOrCreate(['name' => 'Front Carpet'], ['status' => 'active']);
        $this->rearCarpet = CarpetCategory::firstOrCreate(['name' => 'Rear Carpet'], ['status' => 'active']);
        $this->bootCarpet = CarpetCategory::firstOrCreate(['name' => 'Boot Carpet'], ['status' => 'active']);

        // Vehicle types
        $this->carType = VehicleType::create([
            'name' => 'Car',
            'status' => 'active',
            'enable_carpets' => true,
        ]);
        $this->carType->carpetCategories()->sync([
            $this->frontCarpet->id,
            $this->rearCarpet->id,
            $this->bootCarpet->id,
        ]);

        $this->bikeType = VehicleType::create([
            'name' => 'Motorbike',
            'status' => 'active',
            'enable_carpets' => false,
        ]);
    }

    /**
     * 1. Search an existing vehicle with formatting variants (CAA-1234, CAA 1234, caa1234).
     * 2. Verify customer details auto-fill.
     */
    public function test_existing_vehicle_search_supports_all_formats_and_returns_expected_message(): void
    {
        $customer = Customer::create([
            'customer_name' => 'Adeesh Induja',
            'phone' => '0771234567',
            'email' => 'adeesh@example.com',
            'customer_type' => 'individual',
            'status' => 'active',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_type_id' => $this->carType->id,
            'vehicle_number' => 'CAA-1234',
            'vehicle_number_normalized' => 'CAA1234',
            'make_model' => 'Toyota Prius',
            'current_mileage' => 45000,
            'next_service_mileage' => 50000,
        ]);

        // Test with exact hyphen format
        $resHyphen = $this->actingAs($this->staff)->getJson(route('operations.vehicles.search', ['number' => 'CAA-1234']));
        $resHyphen->assertOk()
            ->assertJsonPath('exists', true)
            ->assertJsonPath('message', 'Existing Vehicle Found – Customer Details Loaded')
            ->assertJsonPath('customer.customer_name', 'Adeesh Induja')
            ->assertJsonPath('customer.phone', '0771234567')
            ->assertJsonPath('customer.email', 'adeesh@example.com')
            ->assertJsonPath('vehicle.vehicle_type_id', $this->carType->id)
            ->assertJsonPath('vehicle.make_model', 'Toyota Prius')
            ->assertJsonPath('vehicle.current_mileage', 45000)
            ->assertJsonPath('vehicle.next_service_mileage', 50000)
            ->assertJsonPath('vehicle.enable_carpets', true);

        // Test with space format: "CAA 1234"
        $resSpace = $this->actingAs($this->staff)->getJson(route('operations.vehicles.search', ['number' => 'CAA 1234']));
        $resSpace->assertOk()
            ->assertJsonPath('exists', true)
            ->assertJsonPath('message', 'Existing Vehicle Found – Customer Details Loaded')
            ->assertJsonPath('customer.customer_name', 'Adeesh Induja');

        // Test with lowercase no spaces format: "caa1234"
        $resLower = $this->actingAs($this->staff)->getJson(route('operations.vehicles.search', ['number' => 'caa1234']));
        $resLower->assertOk()
            ->assertJsonPath('exists', true)
            ->assertJsonPath('message', 'Existing Vehicle Found – Customer Details Loaded')
            ->assertJsonPath('customer.customer_name', 'Adeesh Induja');
    }

    /**
     * Search a new vehicle: verify message "New Vehicle – Enter Customer Details" and empty fields.
     */
    public function test_new_vehicle_search_returns_new_vehicle_message_and_empty_state(): void
    {
        $response = $this->actingAs($this->staff)->getJson(route('operations.vehicles.search', ['number' => 'WP-CAD-9999']));

        $response->assertOk()
            ->assertJsonPath('exists', false)
            ->assertJsonPath('message', 'New Vehicle – Enter Customer Details');
    }

    /**
     * Edit existing customer details and save:
     * - Customer is updated
     * - Does not create duplicate vehicle record
     * - Historical invoice details remain untouched
     */
    public function test_edit_customer_details_updates_customer_without_creating_duplicate_vehicle_or_altering_invoices(): void
    {
        $customer = Customer::create([
            'customer_name' => 'Original Customer',
            'phone' => '0711111111',
            'email' => 'orig@example.com',
            'customer_type' => 'individual',
            'status' => 'active',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_type_id' => $this->carType->id,
            'vehicle_number' => 'CAA-1234',
            'vehicle_number_normalized' => 'CAA1234',
            'make_model' => 'Honda Fit',
            'current_mileage' => 30000,
        ]);

        // Historical invoice
        $oldInvoice = Invoice::create([
            'invoice_number' => 'INV-202610-0001',
            'invoice_date' => '2026-09-01',
            'customer_id' => $customer->id,
            'branch_id' => $this->branch->id,
            'subtotal' => 15000.00,
            'grand_total' => 15000.00,
            'amount_paid' => 15000.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
        ]);

        $vehiclesCountBefore = Vehicle::count();

        // Staff updates contact details and starts new job
        $response = $this->actingAs($this->staff)->post(route('operations.vehicles.store'), [
            'vehicle_number' => 'CAA-1234',
            'customer_name' => 'Updated Customer Name',
            'phone' => '0779998888',
            'email' => 'updated@example.com',
            'vehicle_type_id' => $this->carType->id,
            'current_mileage' => 35000,
            'next_service_mileage' => 40000,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals($vehiclesCountBefore, Vehicle::count(), 'No duplicate vehicle should be created');

        // Customer contact info updated
        $customer->refresh();
        $this->assertEquals('Updated Customer Name', $customer->customer_name);
        $this->assertEquals('0779998888', $customer->phone);
        $this->assertEquals('updated@example.com', $customer->email);

        // Historical invoice untouched
        $oldInvoice->refresh();
        $this->assertEquals(15000.00, (float) $oldInvoice->grand_total);
        $this->assertEquals('paid', $oldInvoice->status);
    }

    /**
     * Fleet Company Vehicle:
     * - Load company relationship
     * - Preserve company credit account ownership
     */
    public function test_company_vehicle_search_and_update_preserves_company_credit_account(): void
    {
        $companyCustomer = Customer::create([
            'customer_name' => 'Driver Nimal',
            'company_name' => 'ABC Logistics (Pvt) Ltd',
            'phone' => '0112345678',
            'email' => 'fleet@abclogistics.lk',
            'customer_type' => 'company',
            'status' => 'active',
        ]);

        $creditAccount = CustomerCreditAccount::create([
            'customer_id' => $companyCustomer->id,
            'credit_limit' => 250000.00,
            'payment_terms_days' => 30,
            'opening_balance' => 0.00,
            'status' => 'active',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $companyCustomer->id,
            'vehicle_type_id' => $this->carType->id,
            'vehicle_number' => 'CAB-5555',
            'vehicle_number_normalized' => 'CAB5555',
            'make_model' => 'Isuzu D-Max',
            'current_mileage' => 80000,
        ]);

        // Search vehicle
        $response = $this->actingAs($this->staff)->getJson(route('operations.vehicles.search', ['number' => 'CAB-5555']));
        $response->assertOk()
            ->assertJsonPath('exists', true)
            ->assertJsonPath('customer.company_name', 'ABC Logistics (Pvt) Ltd')
            ->assertJsonPath('customer.is_company', true)
            ->assertJsonPath('customer.has_credit_account', true)
            ->assertJsonPath('customer.available_credit', 250000);

        // Staff updates driver name and phone on visit
        $this->actingAs($this->staff)->post(route('operations.vehicles.store'), [
            'vehicle_number' => 'CAB-5555',
            'customer_name' => 'Driver Sunil',
            'phone' => '0775554433',
            'email' => 'fleet@abclogistics.lk',
            'vehicle_type_id' => $this->carType->id,
            'customer_type' => 'company',
            'company_name' => 'ABC Logistics (Pvt) Ltd',
            'current_mileage' => 85000,
        ]);

        $companyCustomer->refresh();
        $this->assertEquals('Driver Sunil', $companyCustomer->customer_name);
        $this->assertEquals('ABC Logistics (Pvt) Ltd', $companyCustomer->company_name);
        $this->assertTrue($companyCustomer->hasCreditAccount());
        $this->assertEquals(250000.00, (float) $companyCustomer->creditAccount->credit_limit);
    }

    /**
     * Master Data: Admin can configure carpet details on vehicle types.
     */
    public function test_admin_can_configure_carpet_settings_and_categories_on_vehicle_types(): void
    {
        // Admin edits Motorbike to enable carpets with Front Carpet
        $response = $this->actingAs($this->admin)->put(route('master-data.vehicle-types.update', $this->bikeType), [
            'name' => 'Motorbike',
            'status' => 'active',
            'enable_carpets' => '1',
            'carpet_categories' => [$this->frontCarpet->id],
        ]);

        $response->assertRedirect(route('master-data.vehicle-types.index'));
        $this->bikeType->refresh();
        $this->assertTrue($this->bikeType->enable_carpets);
        $this->assertTrue($this->bikeType->carpetCategories->contains($this->frontCarpet));

        // Add new category inline via AJAX endpoint
        $ajaxRes = $this->actingAs($this->admin)->postJson(route('master-data.vehicle-types.carpet-categories.store'), [
            'name' => 'Trunk Mat',
        ]);
        $ajaxRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('category.name', 'Trunk Mat');

        $this->assertDatabaseHas('carpet_categories', ['name' => 'Trunk Mat']);
    }

    /**
     * Job Card Carpet Details:
     * - Car with Front Carpet: 2 and Rear Carpet: 1
     * - Saved against Job Card
     * - Displays on show page
     * - Prints on 80mm thermal printout
     * - Prints on PDF
     * - Does not alter billing or deduct stock
     */
    public function test_job_card_saves_and_displays_carpet_details_and_prints_correctly(): void
    {
        $response = $this->actingAs($this->staff)->post(route('operations.vehicles.store'), [
            'vehicle_number' => 'CAD-7777',
            'customer_name' => 'Kamal Silva',
            'phone' => '0770001122',
            'vehicle_type_id' => $this->carType->id,
            'current_mileage' => 20000,
            'carpets' => [
                ['category_id' => $this->frontCarpet->id, 'quantity' => 2],
                ['category_id' => $this->rearCarpet->id, 'quantity' => 1],
            ],
        ]);

        $response->assertSessionHas('success');

        $jobCard = JobCard::whereHas('vehicle', fn ($q) => $q->where('vehicle_number', 'CAD-7777'))->firstOrFail();

        // Verify saved carpets
        $this->assertCount(2, $jobCard->carpets);
        $front = $jobCard->carpets->firstWhere('carpet_category_id', $this->frontCarpet->id);
        $this->assertNotNull($front);
        $this->assertEquals(2, $front->quantity);
        $this->assertEquals('Front Carpet', $front->category_name);

        $rear = $jobCard->carpets->firstWhere('carpet_category_id', $this->rearCarpet->id);
        $this->assertNotNull($rear);
        $this->assertEquals(1, $rear->quantity);

        // Verify billing is completely unchanged (0.00)
        $this->assertEquals(0.00, (float) $jobCard->grand_total);

        // Verify show view displays CARPET DETAILS
        $showRes = $this->actingAs($this->staff)->get(route('operations.job-cards.show', $jobCard));
        $showRes->assertOk();
        $showRes->assertSee('CARPET DETAILS:');
        $showRes->assertSee('Front Carpet:');
        $showRes->assertSee('Rear Carpet:');

        // Verify 80mm thermal print view contains CARPET DETAILS
        $printRes = $this->actingAs($this->staff)->get(route('operations.job-cards.print', $jobCard));
        $printRes->assertOk();
        $printRes->assertSee('CARPET DETAILS');
        $printRes->assertSee('Front Carpet');
        $printRes->assertSee('Rear Carpet');

        // Verify PDF generation works cleanly
        $pdfRes = $this->actingAs($this->staff)->get(route('operations.job-cards.pdf', $jobCard));
        $pdfRes->assertOk();
        $this->assertEquals('application/pdf', $pdfRes->headers->get('content-type'));
    }

    /**
     * Motorbike registration: carpets section must not save even if payload is sent.
     * Motorbike Job Card must not display an empty carpet section.
     */
    public function test_motorbike_ignores_carpet_payload_and_does_not_display_empty_carpet_section(): void
    {
        $response = $this->actingAs($this->staff)->post(route('operations.vehicles.store'), [
            'vehicle_number' => 'BIKE-1010',
            'customer_name' => 'Rider Kasun',
            'phone' => '0772223344',
            'vehicle_type_id' => $this->bikeType->id,
            'current_mileage' => 5000,
            'carpets' => [
                ['category_id' => $this->frontCarpet->id, 'quantity' => 2],
            ],
        ]);

        $response->assertSessionHas('success');
        $jobCard = JobCard::whereHas('vehicle', fn ($q) => $q->where('vehicle_number', 'BIKE-1010'))->firstOrFail();

        // Carpets should be 0 because Motorbike has enable_carpets = false
        $this->assertCount(0, $jobCard->carpets);

        // Show page must NOT display any carpet details or carpet prompt for Motorbike
        $showRes = $this->actingAs($this->staff)->get(route('operations.job-cards.show', $jobCard));
        $showRes->assertOk();
        $showRes->assertDontSee('CARPET DETAILS:');
        $showRes->assertDontSee('+ Add Carpet Details');
    }

    /**
     * Authorized staff can edit carpet details on an editable Job Card.
     */
    public function test_staff_can_update_carpet_details_on_editable_job_card(): void
    {
        $customer = Customer::create([
            'customer_name' => 'Editable Customer',
            'phone' => '0779991111',
            'status' => 'active',
        ]);
        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-9999',
            'branch_id' => $this->branch->id,
            'date' => now()->toDateString(),
            'customer_id' => $customer->id,
            'vehicle_id' => Vehicle::create([
                'customer_id' => $customer->id,
                'vehicle_type_id' => $this->carType->id,
                'vehicle_number' => 'CAB-9999',
            ])->id,
            'vehicle_type_id' => $this->carType->id,
            'status' => 'pending',
            'created_by' => $this->staff->id,
        ]);

        $jobCard->carpets()->create([
            'carpet_category_id' => $this->frontCarpet->id,
            'category_name' => 'Front Carpet',
            'quantity' => 2,
        ]);

        // Staff updates carpets: Front Carpet -> 1, Boot Carpet -> 1
        $response = $this->actingAs($this->staff)->patch(route('operations.job-cards.carpets.update', $jobCard), [
            'carpets' => [
                ['category_id' => $this->frontCarpet->id, 'quantity' => 1],
                ['category_id' => $this->bootCarpet->id, 'quantity' => 1],
            ],
        ]);

        $response->assertSessionHas('success');
        $jobCard->refresh();

        $this->assertCount(2, $jobCard->carpets);
        $front = $jobCard->carpets->firstWhere('carpet_category_id', $this->frontCarpet->id);
        $this->assertEquals(1, $front->quantity);

        $boot = $jobCard->carpets->firstWhere('carpet_category_id', $this->bootCarpet->id);
        $this->assertNotNull($boot);
        $this->assertEquals(1, $boot->quantity);
    }

    /**
     * Selective Printing:
     * - Include carpets when carpets=1
     * - Exclude carpets when carpets is not passed
     */
    public function test_selective_printing_honors_carpet_inclusion_flag(): void
    {
        $customer = Customer::create([
            'customer_name' => 'Print Customer',
            'phone' => '0778882222',
            'status' => 'active',
        ]);
        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'vehicle_type_id' => $this->carType->id,
            'vehicle_number' => 'CAL-8888',
        ]);

        $jobCard = JobCard::create([
            'job_number' => 'JOB-202610-8888',
            'branch_id' => $this->branch->id,
            'date' => now()->toDateString(),
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $this->carType->id,
            'status' => 'pending',
            'created_by' => $this->staff->id,
        ]);

        $jobCard->carpets()->create([
            'carpet_category_id' => $this->frontCarpet->id,
            'category_name' => 'Front Carpet',
            'quantity' => 2,
        ]);

        // Selective print WITH carpets=1
        $resWithCarpets = $this->actingAs($this->staff)->get(route('operations.job-cards.print', [
            'jobCard' => $jobCard->id,
            'selected' => '1',
            'carpets' => '1',
        ]));
        $resWithCarpets->assertOk();
        $resWithCarpets->assertSee('CARPET DETAILS');
        $resWithCarpets->assertSee('Front Carpet');

        // Selective print WITHOUT carpets (only another selection or empty carpets)
        $resWithoutCarpets = $this->actingAs($this->staff)->get(route('operations.job-cards.print', [
            'jobCard' => $jobCard->id,
            'selected' => '1',
            'services' => [999999], // non-existent service id
        ]));
        $resWithoutCarpets->assertRedirect(); // error: please select at least one item
    }
}
