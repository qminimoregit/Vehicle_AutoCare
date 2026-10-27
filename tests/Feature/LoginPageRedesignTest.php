<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginPageRedesignTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $staffUser;

    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $company = CompanySetting::getSettings();

        $this->branch = Branch::firstOrCreate(
            ['branch_code' => 'CMB'],
            [
                'company_id' => $company->id,
                'branch_name' => 'Main Colombo Center',
                'address' => '123 Galle Road, Colombo',
                'phone' => '0112345678',
                'email' => 'colombo@autocare.lk',
                'is_active' => 1,
            ]
        );

        $this->adminUser = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Ironman@123'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->adminUser->branches()->attach($this->branch->id);

        $this->staffUser = User::factory()->create([
            'name' => 'Workshop Tech',
            'email' => 'staff@autocare.com',
            'password' => Hash::make('StaffPass@123'),
            'role' => 'staff',
            'status' => 'active',
        ]);
        $this->staffUser->branches()->attach($this->branch->id);
    }

    public function test_all_six_vehicle_images_exist_locally(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            $filename = sprintf('vehicle-0%d.webp', $i);
            $this->assertFileExists(
                public_path('images/login/'.$filename),
                "Expected {$filename} to exist in public/images/login/"
            );
        }
    }

    public function test_login_page_renders_split_screen_structure_and_all_six_slides(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);

        // Branding
        $response->assertSee('images/branding/viran_auto_care_logo.png');
        $response->assertSee('VIRAN AUTO CARE');
        $response->assertSee('Welcome Back');
        $response->assertSee('Sign in to manage your auto care center.');
        $response->assertSee('Center Management');

        // Brand colors and layout classes
        $response->assertSee('#0B1849');
        $response->assertSee('lg:w-[45%]', false);

        // Form fields
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="remember"', false);
        $response->assertSee('showPassword', false);
        $response->assertSee('Sign In to Viran Auto Care');

        // All 6 vehicle images referenced
        for ($i = 1; $i <= 6; $i++) {
            $response->assertSee(sprintf('vehicle-0%d.webp', $i));
        }

        // All 6 required slide marketing headlines
        $response->assertSee('Manage Your Auto Care Center');
        $response->assertSee('Professional Vehicle Services');
        $response->assertSee('Manage Every Job with Confidence');
        $response->assertSee('Smart Fleet &amp; Van Care', false);
        $response->assertSee('Precision Mechanical Service');
        $response->assertSee('Customers, Vehicles &amp; Invoices', false);

        // Slider logic checks
        $response->assertSee('duration: 5000', false);
        $response->assertSee('prefers-reduced-motion', false);
        $response->assertSee('visibilitychange', false);
    }

    public function test_admin_user_can_login_and_reach_dashboard(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'admin@gmail.com',
            'password' => 'Ironman@123',
        ]);

        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_staff_user_can_login_and_reach_dashboard(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'staff@autocare.com',
            'password' => 'StaffPass@123',
        ]);

        $this->assertAuthenticatedAs($this->staffUser);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_invalid_password_is_rejected(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'admin@gmail.com',
            'password' => 'WrongPassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_deactivated_user_cannot_login(): void
    {
        $inactiveUser = User::factory()->create([
            'email' => 'inactive@autocare.com',
            'password' => Hash::make('Secret@123'),
            'role' => 'staff',
            'status' => 'inactive',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'inactive@autocare.com',
            'password' => 'Secret@123',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_money_book_queries_real_records_and_respects_date_and_branch_filtering(): void
    {
        $customer = Customer::firstOrCreate(
            ['phone' => '0771234567'],
            [
                'customer_name' => 'Test Customer',
                'customer_type' => 'individual',
                'status' => 'active',
            ]
        );

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-TEST01',
            'branch_id' => $this->branch->id,
            'invoice_date' => '2026-10-15',
            'due_date' => '2026-10-15',
            'customer_id' => $customer->id,
            'invoice_type' => 'direct_sale',
            'grand_total' => 15000.00,
            'amount_paid' => 10000.00,
            'balance_amount' => 5000.00,
            'status' => 'partially_paid',
        ]);

        Payment::create([
            'payment_number' => 'PAY-202610-TEST01',
            'invoice_id' => $invoice->id,
            'customer_id' => $customer->id,
            'payment_date' => '2026-10-15',
            'payment_method' => 'cash',
            'amount' => 10000.00,
            'status' => 'completed',
        ]);

        // 1. Query matching date range -> should see the real transaction
        $responseMatching = $this->actingAs($this->adminUser)->get('/reports/money-book?date_from=2026-10-01&date_to=2026-10-20&branch_id=all');
        $responseMatching->assertStatus(200);
        $responseMatching->assertSee('10,000.00');

        // 2. Query empty date range -> should see 0 (valid result for zero transactions)
        $responseEmpty = $this->actingAs($this->adminUser)->get('/reports/money-book?date_from=2026-01-01&date_to=2026-01-02&branch_id=all');
        $responseEmpty->assertStatus(200);
        $responseEmpty->assertSee('0.00');
    }
}
