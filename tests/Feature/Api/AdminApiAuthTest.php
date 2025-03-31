<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminApiAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test admin can login through API.
     */
    public function test_admin_can_login_through_api(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'admin',
                'token',
            ],
        ]);
        
        $this->assertTrue($response->json('success'));
    }

    /**
     * Test admin cannot login with invalid credentials.
     */
    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'The provided credentials are incorrect.',
        ]);
    }

    /**
     * Test unauthenticated admin cannot access protected API routes.
     */
    public function test_unauthenticated_admin_cannot_access_protected_api_routes(): void
    {
        $response = $this->getJson('/api/admin/user');
        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_login_with_valid_credentials()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'admin' => [
                        'id',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    /** @test */
    public function admin_cannot_login_with_invalid_credentials()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
            ]);
    }

    /** @test */
    public function admin_can_get_their_user_info()
    {
        $admin = Admin::factory()->create();
        
        Sanctum::actingAs($admin, ['admin']);

        $response = $this->getJson('/api/admin/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $admin->id,
                    'email' => $admin->email,
                ],
            ]);
    }

    /** @test */
    public function admin_can_logout()
    {
        $admin = Admin::factory()->create();
        
        Sanctum::actingAs($admin, ['admin']);

        $response = $this->postJson('/api/admin/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully logged out',
            ]);
    }

    /** @test */
    public function non_admin_cannot_access_admin_endpoints()
    {
        // Create a user without admin abilities
        $user = \App\Models\User::factory()->create();
        
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/user');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ]);
    }

    /** @test */
    public function unauthenticated_admin_cannot_access_protected_api_routes()
    {
        $response = $this->getJson('/api/admin/user');

        $response->assertStatus(401);
    }
} 