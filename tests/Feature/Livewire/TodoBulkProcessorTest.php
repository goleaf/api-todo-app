<?php

namespace Tests\Feature\Livewire;

use App\Livewire\TodoBulkProcessor;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\HypervelTestHelpers;

class TodoBulkProcessorTest extends LivewireTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        HypervelTestHelpers::setupHypervelTestEnv();
    }

    /** @test */
    public function it_renders_component()
    {
        $this->assertLivewireCanRender(TodoBulkProcessor::class);
    }

    /** @test */
    public function it_can_select_all_tasks()
    {
        $this->actingAs($this->user);

        // Create some tasks
        $tasks = Task::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);

        $taskIds = $tasks->pluck('id')->toArray();

        Livewire::test(TodoBulkProcessor::class)
            ->call('selectAll')
            ->assertSet('selectedIds', $taskIds)
            ->assertSet('allSelected', true);
    }

    /** @test */
    public function it_can_deselect_all_tasks()
    {
        $this->actingAs($this->user);

        // Create some tasks
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::test(TodoBulkProcessor::class)
            ->call('selectAll')
            ->call('deselectAll')
            ->assertSet('selectedIds', [])
            ->assertSet('allSelected', false);
    }

    /** @test */
    public function it_can_toggle_selection_of_one_task()
    {
        $this->actingAs($this->user);

        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::test(TodoBulkProcessor::class)
            ->call('toggleSelection', $task->id)
            ->assertSet('selectedIds', [$task->id]);

        // Toggle off
        Livewire::test(TodoBulkProcessor::class)
            ->set('selectedIds', [$task->id])
            ->call('toggleSelection', $task->id)
            ->assertSet('selectedIds', []);
    }

    /** @test */
    public function it_can_bulk_mark_tasks_as_completed()
    {
        $this->actingAs($this->user);

        // Create some tasks
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        $taskIds = $tasks->pluck('id')->toArray();

        Livewire::test(TodoBulkProcessor::class)
            ->set('selectedIds', $taskIds)
            ->call('bulkComplete')
            ->assertDispatchedBrowserEvent('tasksUpdated');

        foreach ($taskIds as $id) {
            $this->assertDatabaseHas('tasks', [
                'id' => $id,
                'completed' => true,
            ]);
        }
    }

    /** @test */
    public function it_can_bulk_mark_tasks_as_incomplete()
    {
        $this->actingAs($this->user);

        // Create some tasks
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => true,
        ]);

        $taskIds = $tasks->pluck('id')->toArray();

        Livewire::test(TodoBulkProcessor::class)
            ->set('selectedIds', $taskIds)
            ->call('bulkIncomplete')
            ->assertDispatchedBrowserEvent('tasksUpdated');

        foreach ($taskIds as $id) {
            $this->assertDatabaseHas('tasks', [
                'id' => $id,
                'completed' => false,
            ]);
        }
    }

    /** @test */
    public function it_can_bulk_delete_tasks()
    {
        $this->actingAs($this->user);

        // Create some tasks
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $taskIds = $tasks->pluck('id')->toArray();

        Livewire::test(TodoBulkProcessor::class)
            ->set('selectedIds', $taskIds)
            ->call('bulkDelete')
            ->assertDispatchedBrowserEvent('tasksUpdated');

        foreach ($taskIds as $id) {
            $this->assertDatabaseMissing('tasks', [
                'id' => $id,
            ]);
        }
    }

    /** @test */
    public function it_can_bulk_set_due_date()
    {
        $this->actingAs($this->user);

        // Create some tasks
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'due_date' => null,
        ]);

        $taskIds = $tasks->pluck('id')->toArray();
        $dueDate = now()->addDays(2)->format('Y-m-d');

        Livewire::test(TodoBulkProcessor::class)
            ->set('selectedIds', $taskIds)
            ->set('bulkDueDate', $dueDate)
            ->call('bulkSetDueDate')
            ->assertDispatchedBrowserEvent('tasksUpdated');

        foreach ($taskIds as $id) {
            $this->assertDatabaseHas('tasks', [
                'id' => $id,
                'due_date' => $dueDate,
            ]);
        }
    }

    /** @test */
    public function it_can_filter_selection_by_completed_status()
    {
        $this->actingAs($this->user);

        // Create some tasks with different statuses
        $completedTasks = Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => true,
        ]);

        $incompleteTasks = Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        $completedIds = $completedTasks->pluck('id')->toArray();

        Livewire::test(TodoBulkProcessor::class)
            ->call('selectCompleted')
            ->assertSet('selectedIds', $completedIds);
    }

    /** @test */
    public function it_can_filter_selection_by_incomplete_status()
    {
        $this->actingAs($this->user);

        // Create some tasks with different statuses
        $completedTasks = Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => true,
        ]);

        $incompleteTasks = Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        $incompleteIds = $incompleteTasks->pluck('id')->toArray();

        Livewire::test(TodoBulkProcessor::class)
            ->call('selectIncomplete')
            ->assertSet('selectedIds', $incompleteIds);
    }

    /** @test */
    public function it_can_filter_selection_by_due_date()
    {
        $this->actingAs($this->user);

        // Create tasks with different due dates
        $todayTasks = Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'due_date' => now()->format('Y-m-d'),
        ]);

        $tomorrowTasks = Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDay()->format('Y-m-d'),
        ]);

        $yesterdayTasks = Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'due_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $todayIds = $todayTasks->pluck('id')->toArray();
        $overdueIds = $yesterdayTasks->pluck('id')->toArray();
        $upcomingIds = $tomorrowTasks->pluck('id')->toArray();

        // Test selecting today's tasks
        Livewire::test(TodoBulkProcessor::class)
            ->call('selectDueToday')
            ->assertSet('selectedIds', $todayIds);

        // Test selecting overdue tasks
        Livewire::test(TodoBulkProcessor::class)
            ->call('selectOverdue')
            ->assertSet('selectedIds', $overdueIds);

        // Test selecting upcoming tasks
        Livewire::test(TodoBulkProcessor::class)
            ->call('selectUpcoming')
            ->assertSet('selectedIds', $upcomingIds);
    }

    /** @test */
    public function it_validates_bulk_operation_inputs()
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(TodoBulkProcessor::class);

        // Test with no todos selected
        $component->call('processBulkOperation')
            ->assertSet('errorMessage', 'Please select at least one todo to process');

        // Test with todos selected but no operation type
        $component->set('selectedTodos', [1])
            ->call('processBulkOperation')
            ->assertSet('errorMessage', 'Please select an operation');

        // Test categorize operation without a category selected
        $component->set('operationType', 'categorize')
            ->call('processBulkOperation')
            ->assertSet('errorMessage', 'Please select a category');
    }

    /** @test */
    public function it_can_perform_bulk_mark_completed_operation()
    {
        $user = User::factory()->create();
        $todos = Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);

        // Run the test using Hypervel test helpers to properly handle async operations
        HypervelTestHelpers::testComponentWithHypervel(
            TodoBulkProcessor::class,
            [],
            function ($component) use ($todos) {
                // Select all todos
                $component->call('selectAll');

                // Choose the mark completed operation
                $component->set('operationType', 'mark_completed');

                // Process the operation
                $component->call('processBulkOperation');

                // Check that processing started
                $component->assertSet('isProcessing', true);

                // After processing is complete, check database state
                foreach ($todos as $todo) {
                    $this->assertDatabaseHas('tasks', [
                        'id' => $todo->id,
                        'completed' => true,
                    ]);
                }

                // Check that the results array contains entries for each todo
                $component->assertCount('results', 3);

                // Verify that the component shows completion
                $component->assertSet('processingStatus', 'Completed');
            },
            $user
        );
    }

    /** @test */
    public function it_can_perform_bulk_set_high_priority_operation()
    {
        $user = User::factory()->create();
        $todos = Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'priority' => 'low',
        ]);

        // Run the test using Hypervel test helpers
        HypervelTestHelpers::testComponentWithHypervel(
            TodoBulkProcessor::class,
            [],
            function ($component) use ($todos) {
                // Select all todos
                $component->call('selectAll');

                // Set operation type
                $component->set('operationType', 'set_high_priority');

                // Process operation
                $component->call('processBulkOperation');

                // Verify results
                foreach ($todos as $todo) {
                    $this->assertDatabaseHas('tasks', [
                        'id' => $todo->id,
                        'priority' => 'high',
                    ]);
                }
            },
            $user
        );
    }

    /** @test */
    public function it_can_perform_bulk_set_due_tomorrow_operation()
    {
        $user = User::factory()->create();
        $todos = Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'due_date' => null,
        ]);

        $tomorrow = now()->addDay()->format('Y-m-d');

        // Run the test using Hypervel test helpers
        HypervelTestHelpers::testComponentWithHypervel(
            TodoBulkProcessor::class,
            [],
            function ($component) use ($todos, $tomorrow) {
                // Select all todos
                $component->call('selectAll');

                // Set operation type
                $component->set('operationType', 'set_due_tomorrow');

                // Process operation
                $component->call('processBulkOperation');

                // Verify results
                foreach ($todos as $todo) {
                    $this->assertDatabaseHas('tasks', [
                        'id' => $todo->id,
                    ]);

                    // Check due date is tomorrow (using string comparison for date only)
                    $updatedTodo = Task::find($todo->id);
                    $this->assertEquals($tomorrow, $updatedTodo->due_date->format('Y-m-d'));
                }
            },
            $user
        );
    }

    /** @test */
    public function it_can_perform_bulk_categorize_operation()
    {
        $user = User::factory()->create();
        $todos = Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'category' => null,
        ]);

        // Run the test using Hypervel test helpers
        HypervelTestHelpers::testComponentWithHypervel(
            TodoBulkProcessor::class,
            [],
            function ($component) use ($todos) {
                // Select all todos
                $component->call('selectAll');

                // Set operation type and category
                $component->set('operationType', 'categorize')
                    ->set('selectedCategory', 'work');

                // Process operation
                $component->call('processBulkOperation');

                // Verify results
                foreach ($todos as $todo) {
                    $this->assertDatabaseHas('tasks', [
                        'id' => $todo->id,
                        'category' => 'work',
                    ]);
                }
            },
            $user
        );
    }

    /** @test */
    public function it_can_perform_bulk_delete_operation()
    {
        $user = User::factory()->create();
        $todos = Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        // Run the test using Hypervel test helpers
        HypervelTestHelpers::testComponentWithHypervel(
            TodoBulkProcessor::class,
            [],
            function ($component) use ($todos) {
                // Select all todos
                $component->call('selectAll');

                // Set operation type
                $component->set('operationType', 'delete');

                // Process operation
                $component->call('processBulkOperation');

                // Verify all todos were deleted
                foreach ($todos as $todo) {
                    $this->assertDatabaseMissing('tasks', [
                        'id' => $todo->id,
                    ]);
                }
            },
            $user
        );
    }
}
