<?php

namespace Tests\Feature\Livewire\Dashboard;

use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Dashboard\TaskStats;
use App\Livewire\Tasks\TaskList;
use App\Models\Task;
use Livewire\Livewire;
use Tests\Feature\Livewire\LivewireTestCase;

class DashboardTest extends LivewireTestCase
{
    /** @test */
    public function dashboard_contains_correct_components()
    {
        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSeeLivewire(TaskStats::class)
            ->assertSeeLivewire(TaskList::class);
    }

    /** @test */
    public function dashboard_shows_correct_welcome_message()
    {
        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee("Welcome, {$this->user->name}");
    }

    /** @test */
    public function dashboard_shows_task_statistics()
    {
        // Create tasks with different statuses
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('5 Total Tasks')  // Total
            ->assertSee('3 Active')       // Incomplete
            ->assertSee('2 Completed');   // Completed
    }

    /** @test */
    public function dashboard_updates_when_task_is_completed()
    {
        // Create an incomplete task
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        // Get the dashboard component
        $dashboard = Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('1 Total Tasks')
            ->assertSee('1 Active')
            ->assertSee('0 Completed');

        // Toggle task completion via TaskList component
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('toggleTask', $task->id);

        // Refresh dashboard to see updates
        $dashboard->emit('task-updated')
            ->assertSee('1 Total Tasks')
            ->assertSee('0 Active')
            ->assertSee('1 Completed');
    }

    /** @test */
    public function dashboard_requires_authentication()
    {
        // Log out user
        auth()->logout();

        // Try to access dashboard as guest
        $response = $this->get('/dashboard');

        // Verify redirect to login
        $response->assertRedirect('/login');
    }

    /** @test */
    public function dashboard_shows_recently_created_tasks()
    {
        // Create tasks with different creation dates
        $oldTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Old task',
            'created_at' => now()->subDays(10),
        ]);

        $recentTask1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Recent task 1',
            'created_at' => now()->subDay(),
        ]);

        $recentTask2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Recent task 2',
            'created_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('Recent Tasks')
            ->assertSee($recentTask1->title)
            ->assertSee($recentTask2->title);
    }

    /** @test */
    public function dashboard_shows_upcoming_tasks()
    {
        // Create tasks with different due dates
        $overdueTasks = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Overdue task',
            'due_date' => now()->subDays(2),
            'completed' => false,
        ]);

        $upcomingTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Upcoming task',
            'due_date' => now()->addDays(2),
            'completed' => false,
        ]);

        $farFutureTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Far future task',
            'due_date' => now()->addDays(10),
            'completed' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(Dashboard::class)
            ->assertSee('Upcoming Tasks')
            ->assertSee('Overdue task')
            ->assertSee('Upcoming task');
    }
}
