<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
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
} 