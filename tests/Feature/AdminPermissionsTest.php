<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_access_staff_management_by_url(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
        ]);

        $response = $this->actingAs($staff)->get(route('admin.staff.index'));

        // Direct URL access must be strictly forbidden (403)
        $response->assertStatus(403);
    }

    public function test_admin_can_access_staff_management(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.staff.index'));

        $response->assertStatus(200);
        $response->assertSee('Staff Accounts Management');
    }

    public function test_admin_can_create_a_staff_account(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'Technician Samantha',
            'email' => 'samantha@autocare.com',
            'phone' => '+94 71 555 4321',
            'role' => 'staff',
            'status' => 'active',
            'password' => 'SecurePass123!',
        ]);

        $response->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'samantha@autocare.com',
            'role' => 'staff',
            'status' => 'active',
        ]);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.staff.toggle-status', $admin));

        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'status' => 'active',
        ]);
    }
}
