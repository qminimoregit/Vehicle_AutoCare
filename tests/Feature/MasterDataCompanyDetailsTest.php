<?php

namespace Tests\Feature;

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MasterDataCompanyDetailsTest extends TestCase
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

    public function test_company_details_page_can_be_viewed_by_staff(): void
    {
        $response = $this->actingAs($this->staff)->get(route('master-data.company-details.edit'));

        $response->assertStatus(200);
        $response->assertSee('Company Details');
        $response->assertSee('Company Information');
    }

    public function test_admin_can_update_company_details(): void
    {
        $response = $this->actingAs($this->admin)->put(route('master-data.company-details.update'), [
            'company_name' => 'AutoCare Supreme Center',
            'registration_number' => 'PV-99999',
            'phone' => '+94 11 987 6543',
            'email' => 'supreme@autocare.local',
            'address' => '789 Marine Drive, Colombo',
            'tax_number' => 'VAT-777-888',
        ]);

        $response->assertRedirect(route('master-data.company-details.edit'));
        $response->assertSessionHas('success', 'Company details saved successfully.');

        $this->assertDatabaseHas('company_settings', [
            'company_name' => 'AutoCare Supreme Center',
            'registration_number' => 'PV-99999',
            'email' => 'supreme@autocare.local',
        ]);
    }

    public function test_admin_can_upload_company_logo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('autocare_logo.png', 200, 200);

        $response = $this->actingAs($this->admin)->put(route('master-data.company-details.update'), [
            'company_name' => 'AutoCare Logo Center',
            'logo' => $file,
        ]);

        $response->assertRedirect(route('master-data.company-details.edit'));
        $response->assertSessionHas('success');

        $company = CompanySetting::getSettings();
        $this->assertNotNull($company->logo_path);
        Storage::disk('public')->assertExists($company->logo_path);
    }

    public function test_company_name_is_required(): void
    {
        $response = $this->actingAs($this->admin)->put(route('master-data.company-details.update'), [
            'company_name' => '',
        ]);

        $response->assertSessionHasErrors('company_name');
    }

    public function test_staff_cannot_update_company_details(): void
    {
        $response = $this->actingAs($this->staff)->put(route('master-data.company-details.update'), [
            'company_name' => 'Hacked Company Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_company_details(): void
    {
        $response = $this->get(route('master-data.company-details.edit'));
        $response->assertRedirect(route('login'));
    }
}
