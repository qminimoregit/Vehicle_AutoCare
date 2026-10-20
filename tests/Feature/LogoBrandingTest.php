<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoBrandingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $company = CompanySetting::getSettings();

        $this->branch = Branch::create([
            'company_id' => $company->id,
            'branch_code' => 'BR-TEST',
            'branch_name' => 'Main Center',
            'status' => 'active',
            'is_main_branch' => true,
            'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'user@autocare.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
            'branch_id' => $this->branch->id,
        ]);
    }

    public function test_branding_asset_files_exist_in_public_directory(): void
    {
        $this->assertFileExists(public_path('images/branding/autocare-logo-transparent.png'));
        $this->assertFileExists(public_path('images/branding/autocare-icon.png'));
        $this->assertFileExists(public_path('images/branding/autocare-favicon.ico'));
        $this->assertFileExists(public_path('favicon.ico'));
    }

    public function test_login_page_renders_transparent_branding_logo_and_favicon(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('images/branding/autocare-logo-transparent.png');
        $response->assertSee('images/branding/autocare-favicon.ico');
        $response->assertSee('AutoCare System Logo');
        $response->assertSee('Center Management Portal');
    }

    public function test_authenticated_topbar_renders_autocare_icon_in_light_container_and_links_to_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('images/branding/autocare-icon.png');
        $response->assertSee('images/branding/autocare-favicon.ico');
        $response->assertSee('AutoCare System Icon');
        $response->assertSee('Center Management');
        $response->assertSee(route('dashboard'));
    }
}
