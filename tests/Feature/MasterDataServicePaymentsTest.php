<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataServicePaymentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();
    }

    public function test_service_payments_index_can_be_rendered(): void
    {
        $service = Service::create([
            'code' => 'SRV-PAY-1',
            'name' => 'Transmission Flush',
            'status' => 'active',
        ]);

        $vehicleType = VehicleType::create([
            'name' => 'Car',
            'status' => 'active',
        ]);

        ServiceVehiclePrice::create([
            'service_id' => $service->id,
            'vehicle_type_id' => $vehicleType->id,
            'service_price' => 3500.00,
            'service_cost' => 1000.00,
        ]);

        $response = $this->actingAs($this->staff)->get(route('master-data.service-payments.index'));

        $response->assertStatus(200);
        $response->assertSee('Manage Service Payment');
        $response->assertSee('Transmission Flush');
        $response->assertSee('1 / 1 Types Configured');
        $response->assertSee('Set Prices');
    }

    public function test_service_payments_can_be_filtered_by_search(): void
    {
        Service::create([
            'code' => 'SRV-TIRE',
            'name' => 'Tire Rotation Service',
            'status' => 'active',
        ]);

        Service::create([
            'code' => 'SRV-AIR',
            'name' => 'Cabin Air Filter',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->staff)->get(route('master-data.service-payments.index', [
            'search' => 'Tire',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tire Rotation Service');
        $response->assertDontSee('Cabin Air Filter');
    }

    public function test_admin_can_view_service_price_management_page(): void
    {
        $service = Service::create([
            'code' => 'SRV-OIL',
            'name' => 'Full Synthetic Oil Service',
            'status' => 'active',
        ]);

        VehicleType::create([
            'name' => 'SUV',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get(route('master-data.service-payments.edit', $service));

        $response->assertStatus(200);
        $response->assertSee('Full Synthetic Oil Service - Vehicle Prices');
        $response->assertSee('SUV');
        $response->assertSee('Save Changes');
    }

    public function test_admin_can_save_service_prices_and_costs(): void
    {
        $service = Service::create([
            'code' => 'SRV-BRAKE',
            'name' => 'Brake Pad Replacement',
            'status' => 'active',
        ]);

        $car = VehicleType::create(['name' => 'Car', 'status' => 'active']);
        $van = VehicleType::create(['name' => 'Van', 'status' => 'active']);

        $response = $this->actingAs($this->admin)->put(route('master-data.service-payments.update', $service), [
            'prices' => [
                $car->id => [
                    'price' => '3500.00',
                    'cost' => '1000.00',
                ],
                $van->id => [
                    'price' => '5000.00',
                    'cost' => '1500.00',
                ],
            ],
        ]);

        $response->assertRedirect(route('master-data.service-payments.edit', $service));
        $response->assertSessionHas('success', 'Service prices and costs updated successfully.');

        $this->assertDatabaseHas('service_vehicle_prices', [
            'service_id' => $service->id,
            'vehicle_type_id' => $car->id,
            'service_price' => 3500.00,
            'service_cost' => 1000.00,
        ]);

        $this->assertDatabaseHas('service_vehicle_prices', [
            'service_id' => $service->id,
            'vehicle_type_id' => $van->id,
            'service_price' => 5000.00,
            'service_cost' => 1500.00,
        ]);
    }

    public function test_negative_service_price_or_cost_is_rejected(): void
    {
        $service = Service::create([
            'code' => 'SRV-WASH',
            'name' => 'Underbody Detailing',
            'status' => 'active',
        ]);

        $car = VehicleType::create(['name' => 'Car', 'status' => 'active']);

        $response = $this->actingAs($this->admin)->put(route('master-data.service-payments.update', $service), [
            'prices' => [
                $car->id => [
                    'price' => '-500.00',
                    'cost' => '100.00',
                ],
            ],
        ]);

        $response->assertSessionHasErrors('prices.'.$car->id.'.price');
    }

    public function test_staff_cannot_update_service_prices(): void
    {
        $service = Service::create([
            'code' => 'SRV-EXP',
            'name' => 'Express Wash',
            'status' => 'active',
        ]);

        $car = VehicleType::create(['name' => 'Car', 'status' => 'active']);

        $response = $this->actingAs($this->staff)->put(route('master-data.service-payments.update', $service), [
            'prices' => [
                $car->id => [
                    'price' => '1500.00',
                    'cost' => '500.00',
                ],
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_service_payments(): void
    {
        $response = $this->get(route('master-data.service-payments.index'));
        $response->assertRedirect(route('login'));
    }
}
