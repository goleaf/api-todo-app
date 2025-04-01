<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminApiDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Create some data for testing
        User::factory(3)->create();
        User::factory()
            ->has(Task::factory(5))
            ->create();
    }

    /**
     * Test admin can get dashboard chart data.
     */
    public function test_admin_can_get_dashboard_chart_data(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        $response = $this->getJson('/api/admin/dashboard/chart-data');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'tasksByStatus',
                    'tasksByPriority',
                    'tasksByDate',
                    'mostActiveUsers',
                ],
            ]);
    }

    /**
     * Test admin can get dashboard chart data with period parameter.
     */
    public function test_admin_can_get_dashboard_chart_data_with_period(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        // Test with week period
        $response = $this->getJson('/api/admin/dashboard/chart-data?period=week');
        $response->assertStatus(200)->assertJson(['success' => true]);

        // Test with month period
        $response = $this->getJson('/api/admin/dashboard/chart-data?period=month');
        $response->assertStatus(200)->assertJson(['success' => true]);

        // Test with year period
        $response = $this->getJson('/api/admin/dashboard/chart-data?period=year');
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test unauthenticated users cannot access dashboard chart data.
     */
    public function test_unauthenticated_users_cannot_access_dashboard_chart_data(): void
    {
        $response = $this->getJson('/api/admin/dashboard/chart-data');
        $response->assertStatus(401);
    }

    /**
     * Test non-admin users cannot access dashboard chart data.
     */
    public function test_non_admin_users_cannot_access_dashboard_chart_data(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/dashboard/chart-data');
        
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ]);
    }
} 