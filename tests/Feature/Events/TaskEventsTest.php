<?php

namespace Tests\Feature\Events;

use App\Events\TaskCompleted;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class TaskEventsTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Refresh database for SQLite compatibility
        Artisan::call('migrate:fresh');
        
        Event::fake([
            TaskCreated::class,
            TaskUpdated::class,
            TaskCompleted::class,
            TaskDeleted::class,
        ]);
    }

    /** @test */
    public function it_dispatches_task_created_event_when_creating_a_task()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        Event::assertDispatched(TaskCreated::class, function ($event) use ($task) {
            return $event->task->id === $task->id;
        });
    }

    /** @test */
    public function it_dispatches_task_updated_event_when_updating_a_task()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $task->update(['title' => 'Updated Task']);

        Event::assertDispatched(TaskUpdated::class, function ($event) use ($task) {
            return $event->task->id === $task->id && $event->task->title === 'Updated Task';
        });
    }

    /** @test */
    public function it_does_not_dispatch_task_updated_event_when_completing_a_task()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'completed' => false,
        ]);

        $task->update(['completed' => true, 'completed_at' => now()]);

        Event::assertNotDispatched(TaskUpdated::class);
        // Note: TaskCompleted won't be dispatched here because we're not using the toggleCompletion method
    }

    /** @test */
    public function it_dispatches_task_deleted_event_when_deleting_a_task()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $taskId = $task->id;
        $userId = $task->user_id;

        $task->delete();

        Event::assertDispatched(TaskDeleted::class, function ($event) use ($taskId, $userId) {
            return $event->taskId === $taskId && $event->userId === $userId;
        });
    }

    /** @test */
    public function it_dispatches_task_completed_event_when_using_toggle_completion_method()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'completed' => false,
        ]);

        $task->toggleCompletion();

        Event::assertDispatched(TaskCompleted::class, function ($event) use ($task) {
            return $event->task->id === $task->id && $event->task->completed === true;
        });
    }

    /** @test */
    public function it_dispatches_task_completed_event_when_using_mark_as_complete_method()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'completed' => false,
        ]);

        $task->markAsComplete();

        Event::assertDispatched(TaskCompleted::class, function ($event) use ($task) {
            return $event->task->id === $task->id && $event->task->completed === true;
        });
    }

    /** @test */
    public function it_does_not_dispatch_task_completed_event_when_already_completed()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'completed' => true,
            'completed_at' => now(),
        ]);

        $task->toggleCompletion(); // This should mark it as incomplete

        Event::assertNotDispatched(TaskCompleted::class);
    }
} 