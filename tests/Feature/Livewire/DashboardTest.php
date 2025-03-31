<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Work',
            'color' => 'blue',
        ]);
    }

    /** @test */
    public function dashboard_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertStatus(200)
            ->assertSee('Recent Tasks')
            ->assertSee('Upcoming Tasks')
            ->assertSee('Completion Rate')
            ->assertSee('Tasks by Category');
    }

    /** @test */
    public function dashboard_shows_recent_tasks()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Recent Task',
            'created_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('Recent Task');
    }

    /** @test */
    public function dashboard_shows_upcoming_tasks()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Upcoming Task',
            'due_date' => now()->addDays(2),
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('Upcoming Task');
    }

    /** @test */
    public function dashboard_only_shows_user_tasks()
    {
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        
        $userTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'My Task',
        ]);
        
        $otherUserTask = Task::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
            'title' => 'Other User Task',
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('My Task')
            ->assertDontSee('Other User Task');
    }

    /** @test */
    public function dashboard_shows_task_statistics()
    {
        // Create completed, pending, and overdue tasks
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => true,
        ]);
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => false,
        ]);
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => false,
            'due_date' => now()->subDay(),
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(Dashboard::class);
            
        $component->assertSeeInOrder(['Total Tasks', '3'])
            ->assertSeeInOrder(['Completed', '1'])
            ->assertSeeInOrder(['Pending', '2'])
            ->assertSeeInOrder(['Overdue', '1']);
            
        // Assert completion rate is 33%
        $this->assertEquals(33, $component->get('completionRate'));
    }

    /** @test */
    public function dashboard_shows_tasks_by_category()
    {
        // Create tasks in different categories
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);
        
        $personalCategory = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Personal',
            'color' => 'purple',
        ]);
        
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $personalCategory->id,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(Dashboard::class);
            
        // Assert we have category data
        $categoryStats = $component->get('categoryStats');
        $this->assertCount(2, $categoryStats);
        
        // Assert Work category has 3 tasks
        $this->assertEquals('Work', $categoryStats[0]['name']);
        $this->assertEquals(3, $categoryStats[0]['task_count']);
        
        // Assert Personal category has 2 tasks
        $this->assertEquals('Personal', $categoryStats[1]['name']);
        $this->assertEquals(2, $categoryStats[1]['task_count']);
    }

    /** @test */
    public function dashboard_shows_recent_activity()
    {
        // Create a task
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Activity Test Task',
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);
        
        // Update the task
        $task->update([
            'title' => 'Updated Task Title',
            'updated_at' => now()
        ]);
        
        // Complete the task
        $task->update([
            'completed' => true,
            'completed_at' => now(),
            'updated_at' => now()
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(Dashboard::class);
            
        // Assert recent activity shows created and completed events
        $this->assertNotEmpty($component->get('recentActivity'));
        $component->assertSee('Created task')
            ->assertSee('Completed task')
            ->assertSee('Updated Task Title');
    }

    /** @test */
    public function can_toggle_task_status_from_dashboard()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Toggle Me',
            'completed' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->call('toggleComplete', $task->id);
            
        $this->assertTrue($task->fresh()->completed);
        
        // Toggle back to incomplete
        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->call('toggleComplete', $task->id);
            
        $this->assertFalse($task->fresh()->completed);
    }
} 