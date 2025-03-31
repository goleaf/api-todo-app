<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_unauthenticated_users_cannot_access_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));
        
        $response->assertRedirect(route('admin.login'));
    }
    
    public function test_authenticated_admin_can_access_dashboard()
    {
        $admin = Admin::factory()->create();
        
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }
    
    public function test_dashboard_displays_correct_statistics()
    {
        // Create test data
        $users = User::factory(3)->create();
        $categories = [];
        
        foreach ($users as $user) {
            $categories[] = Category::factory()->create(['user_id' => $user->id]);
        }
        
        // Create some tasks, with some completed
        Task::factory(5)->create([
            'user_id' => $users[0]->id,
            'category_id' => $categories[0]->id,
            'completed' => true,
        ]);
        
        Task::factory(7)->create([
            'user_id' => $users[1]->id,
            'category_id' => $categories[1]->id,
            'completed' => false,
        ]);
        
        $admin = Admin::factory()->create();
        
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response->viewData('stats');
        
        $this->assertEquals(3, $stats['users_count']);
        $this->assertEquals(12, $stats['tasks_count']);
        $this->assertEquals(3, $stats['categories_count']);
        $this->assertEquals(5, $stats['completed_tasks_count']);
        $this->assertEquals(7, $stats['incomplete_tasks_count']);
    }
} 