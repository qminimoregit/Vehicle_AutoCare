<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataServicesTest extends TestCase
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

    public function test_service_list_page_can_be_rendered_by_staff(): void
    {
        Service::create([
            'code' => 'SRV-TEST',
            'name' => 'Test Brake Flush',
            'description' => 'Flushing brake fluid lines.',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->staff)->get(route('master-data.services.index'));

        $response->assertStatus(200);
        $response->assertSee('Service Lists');
        $response->assertSee('Test Brake Flush');
        $response->assertSee('SRV-TEST');
    }

    public function test_admin_can_create_service_with_valid_data(): void
    {
        $response = $this->actingAs($this->admin)->post(route('master-data.services.store'), [
            'code' => 'SRV-OIL-01',
            'name' => 'Premium Oil Service',
            'description' => 'Full synthetic motor oil with filter renewal.',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('master-data.services.index'));
        $response->assertSessionHas('success', 'Service added successfully.');

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

        $response = $this->actingAs($this->admin)->post(route('master-data.services.store'), [
            'code' => 'SRV-DUP',
            'name' => 'Another Service Name',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_admin_can_update_service(): void
    {
        $service = Service::create([
            'code' => 'SRV-UPDATE',
            'name' => 'Old Service Name',
            'description' => 'Old description',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->put(route('master-data.services.update', $service), [
            'code' => 'SRV-UPDATE',
            'name' => 'Updated Service Name',
            'description' => 'Brand new description',
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('master-data.services.index'));
        $response->assertSessionHas('success', 'Service updated successfully.');

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Service Name',
            'status' => 'inactive',
        ]);
    }

    public function test_admin_can_toggle_service_status(): void
    {
        $service = Service::create([
            'code' => 'SRV-TOGGLE',
            'name' => 'Toggle Service',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('master-data.services.toggle-status', $service));

        $response->assertStatus(302);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'status' => 'inactive',
        ]);

        $this->actingAs($this->admin)->patch(route('master-data.services.toggle-status', $service));
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

        $response = $this->actingAs($this->staff)->get(route('master-data.services.index', ['search' => 'Wheel']));
        $response->assertSee('Wheel Alignment Test');
        $response->assertDontSee('Battery Diagnostics');
    }

    public function test_staff_cannot_create_service(): void
    {
        $response = $this->actingAs($this->staff)->post(route('master-data.services.store'), [
            'code' => 'SRV-UNAUTH',
            'name' => 'Unauthorized Service',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }

    public function test_staff_cannot_update_service(): void
    {
        $service = Service::create([
            'code' => 'SRV-STAFF-EDIT',
            'name' => 'Original Service',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->staff)->put(route('master-data.services.update', $service), [
            'code' => 'SRV-STAFF-EDIT',
            'name' => 'Attempted Edit by Staff',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }

    public function test_staff_cannot_toggle_service_status(): void
    {
        $service = Service::create([
            'code' => 'SRV-STAFF-TOGGLE',
            'name' => 'Staff Toggle Service',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->staff)->patch(route('master-data.services.toggle-status', $service));
        $response->assertStatus(403);
    }

    public function test_services_cannot_be_permanently_deleted(): void
    {
        $service = Service::create([
            'code' => 'SRV-DEL-TEST',
            'name' => 'No Delete Service',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('master-data.services.destroy', $service));

        $response->assertRedirect(route('master-data.services.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'code' => 'SRV-DEL-TEST',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_services(): void
    {
        $response = $this->get(route('master-data.services.index'));
        $response->assertRedirect(route('login'));
    }
}
