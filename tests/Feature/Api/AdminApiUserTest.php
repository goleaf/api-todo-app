<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminApiUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can view their own user info via API.
     */
    public function test_admin_can_view_user_info(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Authenticate as admin with admin ability
        Sanctum::actingAs($admin, ['admin']);

        $response = $this->getJson('/api/admin/user');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.email', 'admin@example.com');
    }

    /**
     * Test admin API user details endpoint.
     */
    public function test_admin_can_view_user_details_via_api(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Create a test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Authenticate as admin with admin ability
        Sanctum::actingAs($admin, ['admin']);

        $response = $this->getJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', 'Test User')
            ->assertJsonPath('data.email', 'test@example.com');
    }
} 