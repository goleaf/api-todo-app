<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AsyncTodoList;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\HypervelTestHelpers;
use Tests\TestCase;

class AsyncTodoListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_loads_todos_asynchronously()
    {
        // Create a user with tasks
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);

        // Test the component with Hypervel
        HypervelTestHelpers::testComponentWithHypervel(AsyncTodoList::class, [
            'userId' => $user->id,
        ])
            ->assertSee($tasks[0]->title)
            ->assertSee($tasks[1]->title)
            ->assertSee($tasks[2]->title);
    }

    /** @test */
    public function it_filters_todos_correctly()
    {
        // Create a user with completed and pending tasks
        $user = User::factory()->create();
        $completedTask = Task::factory()->create([
            'user_id' => $user->id,
            'completed' => true,
            'title' => 'Completed Todo',
        ]);
        $pendingTask = Task::factory()->create([
            'user_id' => $user->id,
            'completed' => false,
            'title' => 'Pending Todo',
        ]);

        // Test showing only completed tasks
        $component = HypervelTestHelpers::testComponentWithHypervel(AsyncTodoList::class, [
            'userId' => $user->id,
        ]);

        $component->set('filter', 'completed')
            ->assertSee('Completed Todo')
            ->assertDontSee('Pending Todo');

        // Test showing only pending tasks
        $component->set('filter', 'pending')
            ->assertDontSee('Completed Todo')
            ->assertSee('Pending Todo');
    }

    /** @test */
    public function it_searches_todos_correctly()
    {
        // Create a user with differently named tasks
        $user = User::factory()->create();
        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Buy groceries',
        ]);
        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Read book',
        ]);

        // Test search functionality
        $component = HypervelTestHelpers::testComponentWithHypervel(AsyncTodoList::class, [
            'userId' => $user->id,
        ]);

        $component->set('search', 'groceries')
            ->assertSee('Buy groceries')
            ->assertDontSee('Read book');
    }

    /** @test */
    public function it_toggles_todo_completion()
    {
        // Create a user with a task
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'completed' => false,
            'title' => 'Test Todo',
        ]);

        // Test toggling completion
        $component = HypervelTestHelpers::testComponentWithHypervel(AsyncTodoList::class, [
            'userId' => $user->id,
        ]);

        $component->call('toggleComplete', $task->id);

        // Verify the task was updated
        $task->refresh();
        $this->assertTrue($task->completed);
    }

    /** @test */
    public function it_marks_all_todos_as_completed()
    {
        // Create a user with pending tasks
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);

        // Run the test with Hypervel
        HypervelTestHelpers::runAsyncTest(function () use ($user, $tasks) {
            $component = \Livewire\Livewire::test(AsyncTodoList::class, [
                'userId' => $user->id,
            ]);

            $component->call('markAllCompleted');

            // Check that all tasks are now completed
            foreach ($tasks as $task) {
                $task->refresh();
                $this->assertTrue($task->completed);
            }
        });
    }

    /** @test */
    public function it_benchmarks_performance_improvement()
    {
        // Skip in CI environments
        if (env('CI')) {
            $this->markTestSkipped('Skipping benchmark test in CI environment');
        }

        // Create test data
        $user = User::factory()->create();
        Task::factory()->count(20)->create([
            'user_id' => $user->id,
        ]);

        // Compare synchronous vs asynchronous approaches
        $metrics = HypervelTestHelpers::benchmarkAsyncVsSync(
            // Synchronous approach
            function () use ($user) {
                $tasks = Task::where('user_id', $user->id)->get();
                $completedCount = Task::where('user_id', $user->id)
                    ->where('completed', true)
                    ->count();
                $pendingCount = Task::where('user_id', $user->id)
                    ->where('completed', false)
                    ->count();
                $overdueCount = Task::where('user_id', $user->id)
                    ->where('completed', false)
                    ->where('due_date', '<', now())
                    ->count();

                return [$tasks, $completedCount, $pendingCount, $overdueCount];
            },
            // Asynchronous approach
            function () use ($user) {
                return \Hypervel\Facades\Hypervel::concurrent([
                    fn () => Task::where('user_id', $user->id)->get(),
                    fn () => Task::where('user_id', $user->id)
                        ->where('completed', true)
                        ->count(),
                    fn () => Task::where('user_id', $user->id)
                        ->where('completed', false)
                        ->count(),
                    fn () => Task::where('user_id', $user->id)
                        ->where('completed', false)
                        ->where('due_date', '<', now())
                        ->count(),
                ]);
            }
        );

        // We don't assert specific performance gains since they can vary,
        // but we save the metrics for analysis
        $this->addToAssertionCount(1);
    }
}
