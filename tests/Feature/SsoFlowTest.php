<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\SsoClient;
use App\Models\SsoApplicationRole;
use App\Models\SsoUserApplicationAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SsoFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['jwt.secret' => 'testing-sso-secret-key-32-characters', 'sso.issuer' => 'http://localhost']);
    }

    public function test_authorization_code_flow_returns_userinfo_and_code_is_single_use(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'full_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'mobile' => '082233445566',
            'address' => 'Jl. Mawar No. 10',
            'gender' => 'M',
            'employee_id' => 'EMP001',
        ]);
        $client = $this->createClient();
        $role = SsoApplicationRole::create([
            'sso_client_id' => $client->id,
            'code' => 'staff',
            'name' => 'Staff',
            'is_active' => true,
        ]);
        SsoUserApplicationAccess::create([
            'user_id' => $user->id,
            'sso_client_id' => $client->id,
            'sso_application_role_id' => $role->id,
            'is_active' => true,
        ]);

        $authorizationResponse = $this->actingAs($user)->get('/oauth/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $client->client_id,
            'redirect_uri' => 'https://client.test/callback',
            'scope' => 'openid profile email',
            'state' => 'state-123',
        ]));

        $authorizationResponse->assertRedirect();
        parse_str(parse_url($authorizationResponse->headers->get('Location'), PHP_URL_QUERY), $query);
        $this->assertSame('state-123', $query['state']);

        $tokenResponse = $this->postJson('/api/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $query['code'],
            'client_id' => $client->client_id,
            'client_secret' => 'client-secret',
            'redirect_uri' => 'https://client.test/callback',
        ]);

        $tokenResponse->assertOk()->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
        $accessToken = $tokenResponse->json('access_token');

        $this->withHeader('Authorization', 'Bearer ' . $accessToken)
            ->getJson('/api/oauth/userinfo')
            ->assertOk()
            ->assertJsonPath('sub', (string) $user->id)
            ->assertJsonPath('name', $user->name)
            ->assertJsonPath('email', $user->email)
            ->assertJsonPath('phone', $employee->phone)
            ->assertJsonPath('mobile', $employee->mobile)
            ->assertJsonPath('employee_id', $employee->employee_id)
            ->assertJsonPath('application_role', 'staff');

        $this->postJson('/api/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $query['code'],
            'client_id' => $client->client_id,
            'client_secret' => 'client-secret',
            'redirect_uri' => 'https://client.test/callback',
        ])->assertStatus(400)->assertJson(['error' => 'invalid_grant']);
    }

    public function test_authorization_rejects_unregistered_redirect_uri(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $client = $this->createClient();

        $this->actingAs($user)
            ->get('/oauth/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $client->client_id,
                'redirect_uri' => 'https://attacker.test/callback',
            ]))
            ->assertStatus(400);
    }

    public function test_user_without_application_access_cannot_authorize(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $client = $this->createClient();

        $this->actingAs($user)
            ->get('/oauth/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $client->client_id,
                'redirect_uri' => 'https://client.test/callback',
            ]))
            ->assertStatus(403);
    }

    public function test_client_can_register_and_read_its_application_roles(): void
    {
        $client = $this->createClient();

        $this->postJson('/api/oauth/roles', [
            'client_id' => $client->client_id,
            'client_secret' => 'client-secret',
            'roles' => [
                ['code' => 'approver', 'name' => 'Approver'],
                ['code' => 'staff', 'name' => 'Staff'],
            ],
        ])->assertOk()->assertJsonCount(2, 'roles');

        $this->getJson('/api/oauth/roles?' . http_build_query([
            'client_id' => $client->client_id,
            'client_secret' => 'client-secret',
        ]))->assertOk()->assertJsonPath('roles.0.code', 'approver');
    }

    private function createClient(): SsoClient
    {
        return SsoClient::create([
            'client_id' => 'client-' . fake()->unique()->lexify('????????'),
            'name' => 'Test Client',
            'client_secret' => Hash::make('client-secret'),
            'redirect_uris' => ['https://client.test/callback'],
            'is_active' => true,
        ]);
    }
}
