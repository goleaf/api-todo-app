<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            'message' => 'Invalid credentials',
        ]);
    }

    /**
     * Test unauthenticated admin cannot access protected API routes.
     */
    public function test_unauthenticated_admin_cannot_access_protected_api_routes(): void
    {
        $response = $this->getJson('/api/admin/stats');
        $response->assertStatus(401);
    }
} 