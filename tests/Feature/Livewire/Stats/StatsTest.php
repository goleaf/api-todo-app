<?php

namespace Tests\Feature\Livewire\Stats;

use App\Livewire\Stats\TaskStats;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StatsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_task_statistics()
    {
        $user = User::factory()->create();
        
        // Create a mix of tasks
        Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'completed' => true,
        ]);
        
        Task::factory()->count(5)->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);
        
        // Create tasks due today
        Task::factory()->count(2)->create([
            'user_id' => $user->id,
            'due_date' => now()->format('Y-m-d'),
            'completed' => false,
        ]);
        
        // Create overdue tasks
        Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'due_date' => now()->subDay()->format('Y-m-d'),
            'completed' => false,
        ]);
        
        $this->actingAs($user);
        
        Livewire::test(TaskStats::class)
            ->assertSee('Task Statistics')
            ->assertSee('8 Total Tasks')
            ->assertSee('5 Active')
            ->assertSee('3 Completed')
            ->assertSee('2 Due Today')
            ->assertSee('3 Overdue');
    }
    
    /** @test */
    public function it_shows_task_priority_distribution()
    {
        $user = User::factory()->create();
        
        // Create tasks with different priorities
        Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'priority' => 'high',
        ]);
        
        Task::factory()->count(5)->create([
            'user_id' => $user->id,
            'priority' => 'medium',
        ]);
        
        Task::factory()->count(2)->create([
            'user_id' => $user->id,
            'priority' => 'low',
        ]);
        
        $this->actingAs($user);
        
        Livewire::test(TaskStats::class)
            ->assertSee('Priority Distribution')
            ->assertSee('3 High')
            ->assertSee('5 Medium')
            ->assertSee('2 Low');
    }
    
    /** @test */
    public function it_shows_tasks_by_category()
    {
        $user = User::factory()->create();
        
        // Create categories
        $workCategory = Category::factory()->create([
            'name' => 'Work',
            'user_id' => $user->id,
        ]);
        
        $personalCategory = Category::factory()->create([
            'name' => 'Personal',
            'user_id' => $user->id,
        ]);
        
        // Create tasks with categories
        Task::factory()->count(4)->create([
            'user_id' => $user->id,
            'category_id' => $workCategory->id,
        ]);
        
        Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'category_id' => $personalCategory->id,
        ]);
        
        // Uncategorized tasks
        Task::factory()->count(2)->create([
            'user_id' => $user->id,
            'category_id' => null,
        ]);
        
        $this->actingAs($user);
        
        Livewire::test(TaskStats::class)
            ->assertSee('Tasks by Category')
            ->assertSee('Work (4)')
            ->assertSee('Personal (3)')
            ->assertSee('Uncategorized (2)');
    }
    
    /** @test */
    public function it_shows_completion_rate()
    {
        $user = User::factory()->create();
        
        // Create a mix of completed and incomplete tasks
        Task::factory()->count(6)->create([
            'user_id' => $user->id,
            'completed' => true,
        ]);
        
        Task::factory()->count(4)->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);
        
        $this->actingAs($user);
        
        Livewire::test(TaskStats::class)
            ->assertSee('Completion Rate')
            ->assertSee('60%'); // 6 out of 10 = 60%
    }
    
    /** @test */
    public function it_shows_recent_activity()
    {
        $user = User::factory()->create();
        
        // Create tasks with different creation dates
        $recentTask = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Recent task',
            'created_at' => now()->subHours(1),
        ]);
        
        $olderTask = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Older task',
            'created_at' => now()->subDays(5),
        ]);
        
        $this->actingAs($user);
        
        Livewire::test(TaskStats::class)
            ->assertSee('Recent Activity')
            ->assertSee('Recent task')
            ->assertSee('1 hour ago');
    }
    
    /** @test */
    public function it_shows_tasks_due_soon()
    {
        $user = User::factory()->create();
        
        // Task due today
        $todayTask = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Due today',
            'due_date' => now()->format('Y-m-d'),
            'completed' => false,
        ]);
        
        // Task due tomorrow
        $tomorrowTask = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Due tomorrow',
            'due_date' => now()->addDay()->format('Y-m-d'),
            'completed' => false,
        ]);
        
        // Task due next week
        $nextWeekTask = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Due next week',
            'due_date' => now()->addWeek()->format('Y-m-d'),
            'completed' => false,
        ]);
        
        $this->actingAs($user);
        
        Livewire::test(TaskStats::class)
            ->assertSee('Tasks Due Soon')
            ->assertSee('Due today')
            ->assertSee('Due tomorrow')
            ->assertDontSee('Due next week'); // Should only show tasks due in next few days
    }
    
    /** @test */
    public function it_only_shows_user_tasks()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        
        // Create tasks for main user
        Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);
        
        // Create tasks for other user
        Task::factory()->count(5)->create([
            'user_id' => $otherUser->id,
            'completed' => false,
        ]);
        
        $this->actingAs($user);
        
        Livewire::test(TaskStats::class)
            ->assertSee('3 Total Tasks')
            ->assertDontSee('8 Total Tasks');
    }
} 