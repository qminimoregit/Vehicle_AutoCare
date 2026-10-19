<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataServicesTest extends TestCase
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

    public function test_service_list_page_can_be_rendered(): void
    {
        Service::create([
            'code' => 'SRV-TEST',
            'name' => 'Test Brake Flush',
            'description' => 'Flushing brake fluid lines.',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->get(route('master-data.services.index'));

        $response->assertStatus(200);
        $response->assertSee('Service Lists');
        $response->assertSee('Test Brake Flush');
        $response->assertSee('SRV-TEST');
    }

    public function test_service_can_be_created_with_valid_data(): void
    {
        $response = $this->actingAs($this->user)->post(route('master-data.services.store'), [
            'code' => 'SRV-OIL-01',
            'name' => 'Premium Oil Service',
            'description' => 'Full synthetic motor oil with filter renewal.',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('master-data.services.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('services', [
            'code' => 'SRV-OIL-01',
            'name' => 'Premium Oil Service',
            'status' => 'active',
        ]);
    }

    public function test_duplicate_service_code_is_rejected(): void
    {
        Service::create([
            'code' => 'SRV-DUP',
            'name' => 'Existing Service',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->post(route('master-data.services.store'), [
            'code' => 'SRV-DUP',
            'name' => 'Another Service Name',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_service_can_be_updated(): void
    {
        $service = Service::create([
            'code' => 'SRV-UPDATE',
            'name' => 'Old Service Name',
            'description' => 'Old description',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->put(route('master-data.services.update', $service), [
            'code' => 'SRV-UPDATE',
            'name' => 'Updated Service Name',
            'description' => 'Brand new description',
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('master-data.services.index'));

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Service Name',
            'status' => 'inactive',
        ]);
    }

    public function test_service_status_can_be_toggled(): void
    {
        $service = Service::create([
            'code' => 'SRV-TOGGLE',
            'name' => 'Toggle Service',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->patch(route('master-data.services.toggle-status', $service));

        $response->assertStatus(302);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'status' => 'inactive',
        ]);

        $this->actingAs($this->user)->patch(route('master-data.services.toggle-status', $service));
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'status' => 'active',
        ]);
    }

    public function test_services_can_be_searched_by_name_or_code(): void
    {
        Service::create([
            'code' => 'SRV-ALPHA',
            'name' => 'Wheel Alignment Test',
            'status' => 'active',
        ]);

        Service::create([
            'code' => 'SRV-BETA',
            'name' => 'Battery Diagnostics',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->get(route('master-data.services.index', ['search' => 'Wheel']));
        $response->assertSee('Wheel Alignment Test');
        $response->assertDontSee('Battery Diagnostics');
    }
}
