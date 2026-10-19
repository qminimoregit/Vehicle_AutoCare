<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataClientSmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staff = User::factory()->staff()->create();
    }

    public function test_client_sms_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->staff)->get(route('master-data.client-sms.index'));

        $response->assertStatus(200);
        $response->assertSee('Search Client SMS');
        $response->assertSee('Customer Name / Phone Number');
        $response->assertSee('Selected Customers:');
        $response->assertSee('No customer records found.');
    }

    public function test_client_sms_search_query_is_preserved(): void
    {
        $response = $this->actingAs($this->staff)->get(route('master-data.client-sms.index', [
            'search' => '0771234567',
        ]));

        $response->assertStatus(200);
        $response->assertSee('0771234567');
        $response->assertSee('Reset');
    }

    public function test_unauthenticated_user_cannot_access_client_sms(): void
    {
        $response = $this->get(route('master-data.client-sms.index'));
        $response->assertRedirect(route('login'));
    }
}
