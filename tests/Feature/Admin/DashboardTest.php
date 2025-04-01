<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use App\Enums\UserRole;

class DashboardTest extends TestCase
{
    use WithFaker;

    protected User $adminUser;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Refresh database for SQLite compatibility
        Artisan::call('migrate:fresh');
        
        // Create admin user
        $this->adminUser = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);
    }

    /**
     * Test dashboard page loads with stats.
     */
    public function test_dashboard_displays_statistics(): void
    {
        // Create test data
        User::factory(3)->create();
        $user = User::factory()->create();
        Category::factory(2)->create(['user_id' => $user->id]);
        Tag::factory(5)->create(['user_id' => $user->id]);
        Task::factory(10)->create(['user_id' => $user->id]);
        
        // Complete some tasks
        Task::query()->limit(4)->update(['completed' => true]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        
        // Test that the view has the stats data
        $response->assertViewHas('stats');
        
        // Check that the stats contain the expected keys
        $stats = $response->viewData('stats');
        
        $this->assertArrayHasKey('users_count', $stats);
        $this->assertArrayHasKey('tasks_count', $stats);
        $this->assertArrayHasKey('categories_count', $stats);
        $this->assertArrayHasKey('tags_count', $stats);
        $this->assertArrayHasKey('completed_tasks_count', $stats);
        $this->assertArrayHasKey('incomplete_tasks_count', $stats);
        
        // Verify the counts match our test data
        $this->assertEquals(4, $stats['users_count']);
        $this->assertEquals(10, $stats['tasks_count']);
        $this->assertEquals(2, $stats['categories_count']);
        $this->assertEquals(5, $stats['tags_count']);
        $this->assertEquals(4, $stats['completed_tasks_count']);
        $this->assertEquals(6, $stats['incomplete_tasks_count']);
    }

    /**
     * Test dashboard contains recent tasks.
     */
    public function test_dashboard_displays_recent_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory(7)->create(['user_id' => $user->id]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        
        // Test that the stats include recent tasks
        $stats = $response->viewData('stats');
        $this->assertArrayHasKey('recent_tasks', $stats);
        
        // Check that we have the expected number of recent tasks (should be 5 or fewer)
        $this->assertLessThanOrEqual(5, count($stats['recent_tasks']));
    }

    /**
     * Test dashboard displays active users.
     */
    public function test_dashboard_displays_active_users(): void
    {
        // Create users and tasks
        $users = User::factory(3)->create();
        
        foreach ($users as $i => $user) {
            // Create different number of tasks for each user
            Task::factory($i + 1)->create(['user_id' => $user->id]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        
        // Test that the stats include active users
        $stats = $response->viewData('stats');
        $this->assertArrayHasKey('active_users', $stats);
        
        // Check that we have the expected number of active users
        $this->assertCount(3, $stats['active_users']);
        
        // Verify that users are ordered by task count (most tasks first)
        $this->assertEquals($users[2]->id, $stats['active_users'][0]->id);
    }
} 