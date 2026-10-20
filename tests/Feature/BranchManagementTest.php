<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected CompanySetting $company;

    protected Branch $mainBranch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = CompanySetting::getSettings();
        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();

        // Retrieve the default main branch created by migration or create one
        $this->mainBranch = Branch::firstOrCreate(
            ['company_id' => $this->company->id, 'branch_code' => 'BR-001'],
            [
                'branch_name' => 'AutoCare Service Center - Main Branch',
                'address' => 'No. 124, High Level Road, Colombo',
                'phone' => '+94 11 234 5678',
                'email' => 'info@autocare.local',
                'is_main_branch' => true,
                'is_active' => true,
            ]
        );

        // Assign staff to main branch
        $this->mainBranch->users()->syncWithoutDetaching([$this->staff->id]);
    }

    public function test_company_details_page_renders_both_tabs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('master-data.company-details.edit'));

        $response->assertStatus(200);
        $response->assertSee('Company Details');
        $response->assertSee('Company Information');
        $response->assertSee('Branches');
        $response->assertSee($this->mainBranch->branch_name);
        $response->assertSee('Main Branch');
    }

    public function test_admin_can_create_new_branch(): void
    {
        $response = $this->actingAs($this->admin)->post(route('master-data.branches.store'), [
            'branch_code' => 'BR-KANDY',
            'branch_name' => 'Kandy Branch',
            'address' => '45 Peradeniya Road, Kandy',
            'phone' => '+94 81 223 3445',
            'email' => 'kandy@autocare.local',
            'manager_id' => $this->admin->id,
            'is_active' => '1',
            'is_main_branch' => '0',
            'assigned_staff' => [$this->staff->id],
        ]);

        $response->assertRedirect(route('master-data.company-details.edit', ['tab' => 'branches']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('branches', [
            'branch_code' => 'BR-KANDY',
            'branch_name' => 'Kandy Branch',
            'company_id' => $this->company->id,
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        $newBranch = Branch::where('branch_code', 'BR-KANDY')->first();
        $this->assertTrue($newBranch->users->contains($this->staff->id));
    }

    public function test_branch_code_must_be_unique_within_company(): void
    {
        $response = $this->actingAs($this->admin)->post(route('master-data.branches.store'), [
            'branch_code' => $this->mainBranch->branch_code, // Duplicate
            'branch_name' => 'Duplicate Bay',
        ]);

        $response->assertSessionHasErrors('branch_code');
    }

    public function test_admin_can_edit_branch(): void
    {
        $branch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-GALLE',
            'branch_name' => 'Galle Workshop',
            'address' => 'Old Road, Galle',
            'phone' => '+94 91 222 3344',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('master-data.branches.update', $branch), [
            'branch_code' => 'BR-GALLE-01',
            'branch_name' => 'Galle Coastal Hub',
            'address' => 'New Marine Drive, Galle',
            'phone' => '+94 91 999 8877',
            'is_active' => '1',
            'is_main_branch' => '0',
        ]);

        $response->assertRedirect(route('master-data.company-details.edit', ['tab' => 'branches']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'branch_code' => 'BR-GALLE-01',
            'branch_name' => 'Galle Coastal Hub',
        ]);
    }

    public function test_admin_can_set_a_branch_as_main_branch(): void
    {
        $branch2 = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-002',
            'branch_name' => 'Negombo Branch',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        $this->assertTrue($this->mainBranch->fresh()->is_main_branch);
        $this->assertFalse($branch2->fresh()->is_main_branch);

        $response = $this->actingAs($this->admin)->patch(route('master-data.branches.set-main', $branch2));

        $response->assertRedirect(route('master-data.company-details.edit', ['tab' => 'branches']));
        $response->assertSessionHas('success');

        // Verify exclusivity: only one main branch exists
        $this->assertTrue($branch2->fresh()->is_main_branch);
        $this->assertFalse($this->mainBranch->fresh()->is_main_branch);
        $this->assertEquals(1, Branch::where('company_id', $this->company->id)->where('is_main_branch', true)->count());
    }

    public function test_admin_cannot_deactivate_active_main_branch(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('master-data.branches.toggle-status', $this->mainBranch));

        $response->assertRedirect(route('master-data.company-details.edit', ['tab' => 'branches']));
        $response->assertSessionHas('error', 'The main branch cannot be deactivated.');
        $this->assertTrue($this->mainBranch->fresh()->is_active);
    }

    public function test_admin_can_toggle_non_main_branch_status(): void
    {
        $branch = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-003',
            'branch_name' => 'Matara Branch',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('master-data.branches.toggle-status', $branch));

        $response->assertRedirect(route('master-data.company-details.edit', ['tab' => 'branches']));
        $response->assertSessionHas('success');
        $this->assertFalse($branch->fresh()->is_active);

        // Toggle back to active
        $this->actingAs($this->admin)->patch(route('master-data.branches.toggle-status', $branch));
        $this->assertTrue($branch->fresh()->is_active);
    }

    public function test_staff_cannot_create_or_manage_branches(): void
    {
        // Attempt to create
        $response = $this->actingAs($this->staff)->post(route('master-data.branches.store'), [
            'branch_code' => 'BR-HACK',
            'branch_name' => 'Unauthorized Branch',
        ]);
        $response->assertStatus(403);

        // Attempt to toggle status
        $response = $this->actingAs($this->staff)->patch(route('master-data.branches.toggle-status', $this->mainBranch));
        $response->assertStatus(403);

        // Attempt to set main
        $response = $this->actingAs($this->staff)->patch(route('master-data.branches.set-main', $this->mainBranch));
        $response->assertStatus(403);
    }

    public function test_branch_selector_does_not_appear_when_single_branch(): void
    {
        // Only 1 branch exists ($this->mainBranch)
        $this->assertEquals(1, Branch::count());

        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);
        // Selector dropdown button should not be rendered
        $response->assertDontSee('Select Active Branch');
        $response->assertDontSee('Switch Active Branch');
    }

    public function test_branch_selector_appears_and_switches_when_multiple_branches_exist(): void
    {
        $branch2 = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-002',
            'branch_name' => 'Kandy Branch',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Select Active Branch');
        $response->assertSee('Kandy Branch');

        // Switch to Kandy Branch
        $switchResponse = $this->actingAs($this->admin)->post(route('branches.switch'), [
            'branch_id' => $branch2->id,
        ]);

        $switchResponse->assertSessionHas('success');
        $this->assertEquals($branch2->id, session('active_branch_id'));
    }

    public function test_staff_can_only_switch_to_assigned_branches(): void
    {
        $branch2 = Branch::create([
            'company_id' => $this->company->id,
            'branch_code' => 'BR-UNASSIGNED',
            'branch_name' => 'Unassigned Branch',
            'is_main_branch' => false,
            'is_active' => true,
        ]);

        // Staff is assigned only to $this->mainBranch, not to $branch2
        $this->assertFalse($this->staff->canAccessBranch($branch2->id));

        $response = $this->actingAs($this->staff)->post(route('branches.switch'), [
            'branch_id' => $branch2->id,
        ]);

        $response->assertSessionHas('error', 'You do not have access to the selected branch.');
        $this->assertNotEquals($branch2->id, session('active_branch_id'));
    }

    public function test_branch_show_json_endpoint(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('master-data.branches.show', $this->mainBranch));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->mainBranch->id,
            'branch_code' => 'BR-001',
            'branch_name' => $this->mainBranch->branch_name,
            'is_main_branch' => true,
            'is_active' => true,
        ]);
    }
}
