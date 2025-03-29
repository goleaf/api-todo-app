<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($this->user->id, $task->user->id);
    }

    /** @test */
    public function it_belongs_to_a_category()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $this->assertInstanceOf(Category::class, $task->category);
        $this->assertEquals($this->category->id, $task->category->id);
    }

    /** @test */
    public function it_can_be_marked_as_complete()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => false
        ]);

        $this->assertFalse($task->completed);

        $task->markAsComplete();
        $this->assertTrue($task->completed);
        $this->assertEquals(100, $task->progress);
    }

    /** @test */
    public function it_can_be_marked_as_incomplete()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => true,
            'progress' => 100
        ]);

        $this->assertTrue($task->completed);

        $task->markAsIncomplete();
        $this->assertFalse($task->completed);
        $this->assertTrue($task->progress < 100); // Progress should be reset
    }

    /** @test */
    public function it_has_priority_levels()
    {
        $highPriorityTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'priority' => 2 // High priority
        ]);

        $mediumPriorityTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'priority' => 1 // Medium priority
        ]);

        $lowPriorityTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'priority' => 0 // Low priority
        ]);

        $this->assertEquals('High', $highPriorityTask->priorityText());
        $this->assertEquals('Medium', $mediumPriorityTask->priorityText());
        $this->assertEquals('Low', $lowPriorityTask->priorityText());
    }

    /** @test */
    public function it_scopes_by_user()
    {
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);

        // Create tasks for our test user
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        // Create tasks for another user
        Task::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id
        ]);

        $userTasks = Task::forUser($this->user->id)->get();
        $this->assertCount(3, $userTasks);
        $userTasks->each(function ($task) {
            $this->assertEquals($this->user->id, $task->user_id);
        });
    }

    /** @test */
    public function it_scopes_by_category()
    {
        $anotherCategory = Category::factory()->create(['user_id' => $this->user->id]);

        // Create tasks for first category
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        // Create tasks for second category
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $anotherCategory->id
        ]);

        $categoryTasks = Task::inCategory($this->category->id)->get();
        $this->assertCount(2, $categoryTasks);
        $categoryTasks->each(function ($task) {
            $this->assertEquals($this->category->id, $task->category_id);
        });
    }

    /** @test */
    public function it_scopes_by_completed_status()
    {
        // Create completed tasks
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => true
        ]);

        // Create incomplete tasks
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => false
        ]);

        $completedTasks = Task::completed()->get();
        $this->assertCount(2, $completedTasks);
        $completedTasks->each(function ($task) {
            $this->assertTrue($task->completed);
        });

        $incompleteTasks = Task::incomplete()->get();
        $this->assertCount(3, $incompleteTasks);
        $incompleteTasks->each(function ($task) {
            $this->assertFalse($task->completed);
        });
    }

    /** @test */
    public function it_scopes_by_due_date()
    {
        // Create tasks due today
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'due_date' => now()->format('Y-m-d')
        ]);

        // Create tasks due tomorrow
        Task::factory()->count(1)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'due_date' => now()->addDay()->format('Y-m-d')
        ]);

        // Create tasks due yesterday (overdue)
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'due_date' => now()->subDay()->format('Y-m-d')
        ]);

        // Due this week
        $dueThisWeek = Task::dueThisWeek()->get();
        $this->assertGreaterThanOrEqual(3, $dueThisWeek->count()); // At least today + tomorrow

        // Overdue
        $overdueTasks = Task::overdue()->get();
        $this->assertCount(3, $overdueTasks);
    }

    /** @test */
    public function it_scopes_by_priority()
    {
        // Create high priority tasks
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'priority' => 2
        ]);

        // Create medium priority tasks
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'priority' => 1
        ]);

        // Create low priority tasks
        Task::factory()->count(1)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'priority' => 0
        ]);

        $highPriorityTasks = Task::highPriority()->get();
        $this->assertCount(2, $highPriorityTasks);
        $highPriorityTasks->each(function ($task) {
            $this->assertEquals(2, $task->priority);
        });

        $mediumPriorityTasks = Task::mediumPriority()->get();
        $this->assertCount(3, $mediumPriorityTasks);
        $mediumPriorityTasks->each(function ($task) {
            $this->assertEquals(1, $task->priority);
        });

        $lowPriorityTasks = Task::lowPriority()->get();
        $this->assertCount(1, $lowPriorityTasks);
        $lowPriorityTasks->each(function ($task) {
            $this->assertEquals(0, $task->priority);
        });
    }

    /** @test */
    public function it_can_search_tasks_by_title_or_description()
    {
        // Create tasks with searchable terms
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Meeting with client',
            'description' => 'Discuss project requirements'
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Create project plan',
            'description' => 'Include client requirements'
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Buy groceries',
            'description' => 'Milk, eggs, bread'
        ]);

        // Search for 'client'
        $clientTasks = Task::search('client')->get();
        $this->assertCount(2, $clientTasks);

        // Search for 'groceries'
        $groceryTasks = Task::search('groceries')->get();
        $this->assertCount(1, $groceryTasks);

        // Search for 'requirements'
        $requirementsTasks = Task::search('requirements')->get();
        $this->assertCount(2, $requirementsTasks);
    }
} 