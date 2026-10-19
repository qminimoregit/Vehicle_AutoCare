<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataVehicleTypesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
        ]);
    }

    public function test_vehicle_types_index_can_be_rendered(): void
    {
        VehicleType::create([
            'name' => 'Compact SUV',
            'description' => 'Compact cross-country vehicles.',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->get(route('master-data.vehicle-types.index'));

        $response->assertStatus(200);
        $response->assertSee('Vehicle Types');
        $response->assertSee('Compact SUV');
    }

    public function test_vehicle_type_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('master-data.vehicle-types.store'), [
            'name' => 'Electric Scooter',
            'description' => 'Light two-wheeler electric commuter.',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('master-data.vehicle-types.index'));

        $this->assertDatabaseHas('vehicle_types', [
            'name' => 'Electric Scooter',
            'status' => 'active',
        ]);
    }

    public function test_duplicate_vehicle_type_name_is_rejected(): void
    {
        VehicleType::create([
            'name' => 'Sedan',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->post(route('master-data.vehicle-types.store'), [
            'name' => 'Sedan',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_vehicle_type_can_be_updated(): void
    {
        $type = VehicleType::create([
            'name' => 'Minivan',
            'description' => '7-seater minivan',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->put(route('master-data.vehicle-types.update', $type), [
            'name' => 'Passenger Minivan',
            'description' => 'Updated 7-9 seater passenger minivan',
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('master-data.vehicle-types.index'));

        $this->assertDatabaseHas('vehicle_types', [
            'id' => $type->id,
            'name' => 'Passenger Minivan',
            'status' => 'inactive',
        ]);
    }

    public function test_vehicle_type_status_can_be_toggled(): void
    {
        $type = VehicleType::create([
            'name' => 'Commercial Van',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->patch(route('master-data.vehicle-types.toggle-status', $type));

        $response->assertStatus(302);
        $this->assertDatabaseHas('vehicle_types', [
            'id' => $type->id,
            'status' => 'inactive',
        ]);
    }
}
