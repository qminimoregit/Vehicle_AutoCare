<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceVehiclePrice;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MasterDataVehicleTypesTest extends TestCase
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

    public function test_vehicle_types_index_can_be_rendered_by_staff(): void
    {
        VehicleType::create([
            'name' => 'Compact SUV',
            'description' => 'Compact cross-country vehicles.',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->staff)->get(route('master-data.vehicle-types.index'));

        $response->assertStatus(200);
        $response->assertSee('Vehicle Types');
        $response->assertSee('Compact SUV');
        $response->assertSee('Vehicle Image');
        $response->assertSee('Actions');
    }

    public function test_admin_can_view_create_vehicle_type_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('master-data.vehicle-types.create'));

        $response->assertStatus(200);
        $response->assertSee('Add Vehicle Type');
        $response->assertSee('Vehicle Type Name');
        $response->assertSee('Save Vehicle Type');
    }

    public function test_staff_cannot_view_create_vehicle_type_page(): void
    {
        $response = $this->actingAs($this->staff)->get(route('master-data.vehicle-types.create'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_vehicle_type_without_image(): void
    {
        $response = $this->actingAs($this->admin)->post(route('master-data.vehicle-types.store'), [
            'name' => 'Electric Scooter',
            'description' => 'Light two-wheeler electric commuter.',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('master-data.vehicle-types.index'));
        $response->assertSessionHas('success', 'Vehicle type added successfully.');

        $this->assertDatabaseHas('vehicle_types', [
            'name' => 'Electric Scooter',
            'image' => null,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_vehicle_type_with_image(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('jeep.png', 300, 300);

        $response = $this->actingAs($this->admin)->post(route('master-data.vehicle-types.store'), [
            'name' => 'Luxury Jeep',
            'image' => $image,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('master-data.vehicle-types.index'));
        $response->assertSessionHas('success', 'Vehicle type added successfully.');

        $vehicleType = VehicleType::where('name', 'Luxury Jeep')->firstOrFail();
        $this->assertNotNull($vehicleType->image);
        Storage::disk('public')->assertExists($vehicleType->image);
    }

    public function test_duplicate_vehicle_type_name_is_rejected(): void
    {
        VehicleType::create([
            'name' => 'Sedan',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post(route('master-data.vehicle-types.store'), [
            'name' => 'Sedan',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_view_edit_vehicle_type_page(): void
    {
        $type = VehicleType::create([
            'name' => 'Marcopolo Bus',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get(route('master-data.vehicle-types.edit', $type));

        $response->assertStatus(200);
        $response->assertSee('Edit Vehicle Type');
        $response->assertSee('Marcopolo Bus');
        $response->assertSee('Current Image:');
        $response->assertSee('Change Image:');
        $response->assertSee('Save Changes');
    }

    public function test_staff_cannot_view_edit_vehicle_type_page(): void
    {
        $type = VehicleType::create([
            'name' => 'Marcopolo Bus',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->staff)->get(route('master-data.vehicle-types.edit', $type));

        $response->assertStatus(403);
    }

    public function test_admin_can_update_vehicle_type_name_keeping_existing_image(): void
    {
        Storage::fake('public');

        $imagePath = 'vehicle-types/original_bus.png';
        Storage::disk('public')->put($imagePath, 'fake-content');

        $type = VehicleType::create([
            'name' => 'Bus',
            'image' => $imagePath,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->put(route('master-data.vehicle-types.update', $type), [
            'name' => 'Marcopolo Bus',
        ]);

        $response->assertRedirect(route('master-data.vehicle-types.index'));
        $response->assertSessionHas('success', 'Vehicle type updated successfully.');

        $type->refresh();
        $this->assertEquals('Marcopolo Bus', $type->name);
        $this->assertEquals($imagePath, $type->image);
        Storage::disk('public')->assertExists($imagePath);
    }

    public function test_admin_can_replace_image_and_old_image_is_deleted(): void
    {
        Storage::fake('public');

        $oldPath = 'vehicle-types/old_image.png';
        Storage::disk('public')->put($oldPath, 'old-content');

        $type = VehicleType::create([
            'name' => 'Forklift',
            'image' => $oldPath,
            'status' => 'active',
        ]);

        $newImage = UploadedFile::fake()->image('new_forklift.jpg', 200, 200);

        $response = $this->actingAs($this->admin)->put(route('master-data.vehicle-types.update', $type), [
            'name' => 'Small Forklift',
            'image' => $newImage,
        ]);

        $response->assertRedirect(route('master-data.vehicle-types.index'));

        $type->refresh();
        $this->assertEquals('Small Forklift', $type->name);
        $this->assertNotEquals($oldPath, $type->image);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($type->image);
    }

    public function test_vehicle_type_image_validation(): void
    {
        Storage::fake('public');

        // Not an image file (e.g. text file)
        $invalidFile = UploadedFile::fake()->create('document.pdf', 500);

        $response = $this->actingAs($this->admin)->post(route('master-data.vehicle-types.store'), [
            'name' => 'Test Vehicle',
            'image' => $invalidFile,
        ]);

        $response->assertSessionHasErrors('image');

        // Oversized file (> 2048 KB)
        $oversizedFile = UploadedFile::fake()->image('huge.jpg')->size(2500);

        $response = $this->actingAs($this->admin)->post(route('master-data.vehicle-types.store'), [
            'name' => 'Test Vehicle 2',
            'image' => $oversizedFile,
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_vehicle_types_can_be_searched(): void
    {
        VehicleType::create([
            'name' => 'Three Wheeler',
            'description' => 'Tuk-tuk commuter',
            'status' => 'active',
        ]);

        VehicleType::create([
            'name' => 'Heavy Truck',
            'description' => 'Heavy multi-axle freight',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->staff)->get(route('master-data.vehicle-types.index', ['search' => 'Wheeler']));
        $response->assertSee('Three Wheeler');
        $response->assertDontSee('Heavy Truck');
    }

    public function test_vehicle_types_pagination(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            VehicleType::create([
                'name' => sprintf('Vehicle Type %02d', $i),
                'status' => 'active',
            ]);
        }

        $response = $this->actingAs($this->staff)->get(route('master-data.vehicle-types.index'));
        $response->assertStatus(200);

        // Should see page 1 records (first 10 sorted alphabetically: 01 to 10)
        $response->assertSee('Vehicle Type 01');
        $response->assertSee('Vehicle Type 10');
        $response->assertDontSee('Vehicle Type 11');
    }

    public function test_staff_cannot_create_vehicle_type(): void
    {
        $response = $this->actingAs($this->staff)->post(route('master-data.vehicle-types.store'), [
            'name' => 'Unauthorized Type',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }

    public function test_staff_cannot_update_vehicle_type(): void
    {
        $type = VehicleType::create([
            'name' => 'Station Wagon',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->staff)->put(route('master-data.vehicle-types.update', $type), [
            'name' => 'Hacked Type',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }

    public function test_vehicle_types_cannot_be_permanently_deleted(): void
    {
        $type = VehicleType::create([
            'name' => 'Protected Type',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('master-data.vehicle-types.destroy', $type));

        $response->assertRedirect(route('master-data.vehicle-types.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('vehicle_types', [
            'id' => $type->id,
            'name' => 'Protected Type',
        ]);
    }

    public function test_new_vehicle_type_appears_in_service_payments_without_breaking_prices(): void
    {
        $service = Service::create([
            'code' => 'SRV-OIL-01',
            'name' => 'Full Engine Oil Service',
            'status' => 'active',
        ]);

        $car = VehicleType::create([
            'name' => 'Car',
            'status' => 'active',
        ]);

        ServiceVehiclePrice::create([
            'service_id' => $service->id,
            'vehicle_type_id' => $car->id,
            'service_price' => 3500.00,
            'service_cost' => 1000.00,
        ]);

        // Now create a new vehicle type
        $newType = VehicleType::create([
            'name' => 'Luxury Jeep',
            'status' => 'active',
        ]);

        // Visit Service Payments edit price screen for the service
        $response = $this->actingAs($this->admin)->get(route('master-data.service-payments.edit', $service));

        $response->assertStatus(200);
        $response->assertSee('Full Engine Oil Service');
        $response->assertSee('Car');
        $response->assertSee('3500.00');
        $response->assertSee('Luxury Jeep');

        // Existing price for Car remains exactly 3500
        $this->assertDatabaseHas('service_vehicle_prices', [
            'service_id' => $service->id,
            'vehicle_type_id' => $car->id,
            'service_price' => 3500.00,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_vehicle_types(): void
    {
        $response = $this->get(route('master-data.vehicle-types.index'));
        $response->assertRedirect(route('login'));
    }
}
