<?php

namespace Tests\Feature\Integration;

use App\Livewire\Tasks\TaskCreate;
use App\Livewire\Tasks\TaskList;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function creating_task_updates_task_list()
    {
        // First, render the TaskList component
        $taskList = Livewire::actingAs($this->user)
            ->test(TaskList::class);

        // Create a new task
        Livewire::actingAs($this->user)
            ->test(TaskCreate::class)
            ->set('form.title', 'New Integration Task')
            ->call('save')
            ->assertEmitted('task-created');

        // Task list should refresh when task-created event is fired
        $taskList->emit('task-created')
            ->assertSee('New Integration Task');
    }

    /** @test */
    public function completing_all_tasks_shows_completion_message()
    {
        // Create two tasks
        $tasks = Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        // Get the TaskList component
        $taskList = Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertDontSee('All tasks completed!');

        // Complete both tasks
        foreach ($tasks as $task) {
            $taskList->call('toggleTask', $task->id);
        }

        // After completing all tasks, we should see the completion message
        $taskList->assertSee('All tasks completed!');
    }

    /** @test */
    public function delete_task_removes_from_list_and_updates_stats()
    {
        // Create a task
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Task to be deleted',
        ]);

        // Get the TaskList component and verify the task is visible
        $taskList = Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSee('Task to be deleted');

        // Delete the task
        $taskList->call('confirmTaskDeletion', $task->id)
            ->call('deleteTask')
            ->assertEmitted('task-deleted');

        // Verify the task is no longer visible
        $taskList->assertDontSee('Task to be deleted');

        // Verify the dashboard stats are updated
        Livewire::actingAs($this->user)
            ->test('App\Livewire\Dashboard\Dashboard')
            ->emit('task-deleted')
            ->assertSee('0 Total Tasks');
    }

    /** @test */
    public function task_list_and_filter_components_interact()
    {
        // Create completed and incomplete tasks
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Completed task',
            'completed' => true,
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Incomplete task',
            'completed' => false,
        ]);

        // Test the filter component interaction with the task list
        $taskList = Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSee('Completed task')
            ->assertSee('Incomplete task');

        // Simulate filter component emitting a filter-tasks event
        $taskList->emit('filter-tasks', 'completed')
            ->assertSee('Completed task')
            ->assertDontSee('Incomplete task');

        // Switch filter to incomplete tasks
        $taskList->emit('filter-tasks', 'incomplete')
            ->assertSee('Incomplete task')
            ->assertDontSee('Completed task');
    }

    /** @test */
    public function searching_tasks_works_across_components()
    {
        // Create tasks with different titles
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Meeting with client',
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Buy groceries',
        ]);

        // Simulate search component emitting a search-tasks event
        $taskList = Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSee('Meeting with client')
            ->assertSee('Buy groceries');

        // Search for 'meeting'
        $taskList->emit('search-tasks', 'meeting')
            ->assertSee('Meeting with client')
            ->assertDontSee('Buy groceries');

        // Clear search
        $taskList->emit('search-tasks', '')
            ->assertSee('Meeting with client')
            ->assertSee('Buy groceries');
    }

    /** @test */
    public function task_form_validation_errors_are_displayed()
    {
        // Test form validation in an integrated context
        Livewire::actingAs($this->user)
            ->test(TaskCreate::class)
            ->set('form.title', '') // Empty title should fail validation
            ->call('save')
            ->assertHasErrors(['form.title' => 'required'])
            ->assertSee('The title field is required');
    }

    /** @test */
    public function it_can_execute_batch_operations_on_tasks()
    {
        // Set up various tasks
        $tasks = Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => false,
            'priority' => 0,
        ]);

        // Define a series of operations to perform
        $operations = [
            'mark_completed' => function () use ($tasks) {
                // Mark tasks as completed
                $tasksToUpdate = $tasks->slice(0, 1);

                foreach ($tasksToUpdate as $task) {
                    $task->completed = true;
                    $task->save();
                }

                return count($tasksToUpdate);
            },
            'set_priority' => function () use ($tasks) {
                // Set priority
                foreach ($tasks as $task) {
                    $task->priority = 'high';
                    $task->save();
                }

                return count($tasks);
            },
        ];

        // Execute operations
        foreach ($operations as $name => $operation) {
            $count = $operation();
            $this->assertGreaterThan(0, $count, "Operation {$name} didn't affect any tasks");
        }

        // Verify tasks were updated
        $completedCount = Task::where('user_id', $this->user->id)
            ->where('completed', true)
            ->count();

        $this->assertEquals(1, $completedCount);

        $highPriorityCount = Task::where('user_id', $this->user->id)
            ->where('priority', 'high')
            ->count();

        $this->assertEquals(2, $highPriorityCount);
    }

    /** @test */
    public function it_can_create_and_retrieve_tasks()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'priority' => 'medium',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Test Task',
        ]);

        $response = $this->get("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $task->id,
                'title' => 'Test Task',
                'description' => 'This is a test task',
                'priority' => 'medium',
            ]);
    }

    /** @test */
    public function it_can_filter_tasks_by_multiple_criteria()
    {
        // Create tasks with different attributes
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'High Priority Task',
            'priority' => 'high',
            'completed' => false,
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Completed Task',
            'priority' => 'low',
            'completed' => true,
        ]);

        // Search for high priority incomplete tasks
        $response = $this->get('/api/tasks?priority=high&completed=0');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'High Priority Task');

        // Search for completed tasks
        $response = $this->get('/api/tasks?completed=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Completed Task');
    }

    /** @test */
    public function it_can_categorize_tasks()
    {
        // Create a category
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Work',
        ]);

        // Create a task with that category
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Categorized Task',
            'category_id' => $category->id,
        ]);

        // Create a task without category
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Uncategorized Task',
            'category_id' => null,
        ]);

        // Filter by category
        $response = $this->get("/api/tasks?category_id={$category->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Categorized Task');

        // Get tasks without category
        $response = $this->get('/api/tasks?no_category=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Uncategorized Task');
    }

    /** @test */
    public function it_can_execute_complex_batch_operations()
    {
        // Create a bunch of tasks
        $todos = Task::factory()->count(10)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        // Define batch operations
        $batchOperations = [
            'mark_high_priority' => function () use ($todos) {
                // Mark tasks with even ids as high priority
                $highPriorityTodos = $todos->filter(function ($todo, $index) {
                    return $index % 2 === 0;
                });

                foreach ($highPriorityTodos as $todo) {
                    $todo->priority = 'high';
                    $todo->save();
                }

                return $highPriorityTodos->count();
            },
            'set_due_dates' => function () use ($todos) {
                // Set due dates for every other task
                $dueDateTodos = $todos->filter(function ($todo, $index) {
                    return $index % 2 === 1;
                });

                foreach ($dueDateTodos as $todo) {
                    $todo->due_date = now()->addDays(7);
                    $todo->save();
                }

                return $dueDateTodos->count();
            },
            'categorize' => function () use ($todos) {
                // Create a category
                $category = Category::factory()->create([
                    'user_id' => $this->user->id,
                    'name' => 'Work',
                ]);

                // Categorize first 4 tasks
                $categorizeTodos = $todos->take(4);

                foreach ($categorizeTodos as $todo) {
                    $todo->category_id = $category->id;
                    $todo->save();
                }

                return $categorizeTodos->count();
            },
        ];

        // Execute operations
        $results = [];
        foreach ($batchOperations as $name => $operation) {
            $results[$name] = $operation();
        }

        // Verify results
        $this->assertEquals(5, $results['mark_high_priority']);
        $this->assertEquals(5, $results['set_due_dates']);
        $this->assertEquals(4, $results['categorize']);

        // Verify database state
        $this->assertEquals(5, Task::where('user_id', $this->user->id)->where('priority', 'high')->count());
        $this->assertEquals(4, Task::where('user_id', $this->user->id)->whereNotNull('due_date')->count());
        $this->assertEquals(4, Task::where('user_id', $this->user->id)->where('category_id', '!=', null)->count());
    }

    /** @test */
    public function it_can_perform_concurrent_operations_on_tasks()
    {
        // Create a series of tasks
        $user = $this->user;
        $numberOfTasks = 20;

        Task::factory()->count($numberOfTasks)->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);

        // Verify initial state
        $this->assertEquals($numberOfTasks, Task::where('user_id', $user->id)->count());
        $this->assertEquals(0, Task::where('user_id', $user->id)->where('completed', true)->count());

        // Define concurrent operations
        $operations = [
            'mark_half_completed' => function () use ($user) {
                $tasks = Task::where('user_id', $user->id)->get();
                $halfCount = floor($tasks->count() / 2);

                foreach ($tasks->take($halfCount) as $task) {
                    $task->completed = true;
                    $task->save();
                }

                return $halfCount;
            },
            'set_priorities' => function () use ($user) {
                $tasks = Task::where('user_id', $user->id)->get();

                // Set different priorities
                foreach ($tasks as $index => $task) {
                    $task->priority = $index % 3 === 0 ? 'high' : ($index % 3 === 1 ? 'medium' : 'low');
                    $task->save();
                }

                return $tasks->count();
            },
        ];

        // Execute concurrent operations
        $results = [];
        foreach ($operations as $name => $operation) {
            $results[$name] = $operation();
        }

        // Verify results
        $completedCount = Task::where('user_id', $user->id)->where('completed', true)->count();
        $this->assertEquals(10, $completedCount);

        $highPriorityCount = Task::where('user_id', $user->id)->where('priority', 'high')->count();
        $this->assertGreaterThan(0, $highPriorityCount);
    }

    /** @test */
    public function it_can_handle_sorting_and_filtering_with_large_dataset()
    {
        // Create a large number of tasks
        $todos = Task::factory()->count(25)->create([
            'user_id' => $this->user->id,
        ]);

        // Mix of completed/incomplete
        foreach ($todos->take(10) as $todo) {
            $todo->completed = true;
            $todo->save();
        }

        // Various priorities
        foreach ($todos as $index => $todo) {
            $todo->priority = $index % 3 === 0 ? 'high' : ($index % 3 === 1 ? 'medium' : 'low');
            $todo->save();
        }

        // Various due dates
        foreach ($todos->take(15) as $index => $todo) {
            $todo->due_date = now()->addDays($index);
            $todo->save();
        }

        // Test complex filtering
        $response = $this->get('/api/tasks/search?completed=0&priority=high&sort=due_date&direction=asc');

        $response->assertStatus(200);
        $data = $response->json('data');

        // Verify filtering logic
        foreach ($data as $task) {
            $this->assertEquals(false, $task['completed']);
            $this->assertEquals('high', $task['priority']);
        }

        // Verify sorting logic for tasks with due dates
        $dueDates = collect($data)->pluck('due_date');
        $sortedDueDates = $dueDates->sort()->values();

        $this->assertEquals($sortedDueDates, $dueDates);
    }
}
