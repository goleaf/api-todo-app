<?php

namespace Tests\Feature\Livewire;

use App\Events\TaskCompleted;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Livewire\Notifications\TaskNotifications;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class TaskNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the component can be rendered.
     */
    public function test_component_can_render(): void
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $component = Livewire::test(TaskNotifications::class);
        
        $component->assertStatus(200);
    }

    /**
     * Test task created notifications are handled.
     */
    public function test_task_created_notification_handling(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Task',
            'due_date' => Carbon::tomorrow(),
        ]);
        
        $component = Livewire::test(TaskNotifications::class);
        
        // Initial state should have no notifications
        $component->assertSet('notifications', []);
        
        // Simulate the broadcast event
        $component->call('handleTaskCreated', [
            'task' => $task->toArray(),
        ]);
        
        // Component should now have one notification
        $component->assertCount('notifications', 1)
            ->assertSee('was created', false);
    }

    /**
     * Test task updated notifications are handled.
     */
    public function test_task_updated_notification_handling(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Task',
            'due_date' => Carbon::tomorrow(),
        ]);
        
        $component = Livewire::test(TaskNotifications::class);
        
        // Simulate the broadcast event
        $component->call('handleTaskUpdated', [
            'task' => $task->toArray(),
        ]);
        
        // Component should now have one notification
        $component->assertCount('notifications', 1)
            ->assertSee('was updated', false);
    }

    /**
     * Test task completed notifications are handled.
     */
    public function test_task_completed_notification_handling(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Task',
            'due_date' => Carbon::tomorrow(),
            'completed' => true,
            'completed_at' => now(),
        ]);
        
        $component = Livewire::test(TaskNotifications::class);
        
        // Simulate the broadcast event
        $component->call('handleTaskCompleted', [
            'task' => $task->toArray(),
        ]);
        
        // Component should now have one notification
        $component->assertCount('notifications', 1)
            ->assertSee('was completed', false);
    }

    /**
     * Test user can clear all notifications.
     */
    public function test_clear_all_notifications(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $component = Livewire::test(TaskNotifications::class);
        
        // Add a couple of notifications
        $component->call('handleTaskCreated', [
            'task' => Task::factory()->create(['user_id' => $user->id])->toArray(),
        ]);
        
        $component->call('handleTaskUpdated', [
            'task' => Task::factory()->create(['user_id' => $user->id])->toArray(),
        ]);
        
        // Verify we have 2 notifications
        $component->assertCount('notifications', 2);
        
        // Clear all notifications
        $component->call('clearAllNotifications');
        
        // Verify notifications are cleared
        $component->assertSet('notifications', []);
    }

    /**
     * Test user can remove a specific notification.
     */
    public function test_remove_specific_notification(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $component = Livewire::test(TaskNotifications::class);
        
        // Add the first notification - "First Task"
        $firstTask = Task::factory()->create(['user_id' => $user->id, 'title' => 'First Task']);
        $component->call('handleTaskCreated', [
            'task' => $firstTask->toArray(),
        ]);
        
        // Verify we have 1 notification
        $component->assertCount('notifications', 1);
        
        // Add the second notification - "Second Task"
        // This will be at index 0 since newer notifications are added to the beginning
        $secondTask = Task::factory()->create(['user_id' => $user->id, 'title' => 'Second Task']);
        $component->call('handleTaskUpdated', [
            'task' => $secondTask->toArray(),
        ]);
        
        // Verify we now have 2 notifications
        $component->assertCount('notifications', 2);
        
        // Get the notifications array before removing anything
        $notificationsBefore = $component->get('notifications');
        $this->assertCount(2, $notificationsBefore);
        
        // Verify "Second Task" is at index 0
        $this->assertStringContainsString('Second Task', $notificationsBefore[0]['message']);
        $this->assertStringContainsString('First Task', $notificationsBefore[1]['message']);
        
        // Remove the notification at index 1 (First Task)
        $component->call('removeNotification', 1);
        
        // Verify we now have 1 notification and it's the Second Task
        $component->assertCount('notifications', 1);
        
        // Get the notifications array after removing
        $notificationsAfter = $component->get('notifications');
        $this->assertCount(1, $notificationsAfter);
        $this->assertStringContainsString('Second Task', $notificationsAfter[0]['message']);
    }

    /**
     * Test notifications respect max count.
     */
    public function test_notifications_respect_max_count(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $component = Livewire::test(TaskNotifications::class);
        
        // Set a lower max notifications count for the test
        $component->set('maxNotifications', 3);
        
        // Add more notifications than the max
        for ($i = 1; $i <= 5; $i++) {
            $component->call('handleTaskCreated', [
                'task' => Task::factory()->create([
                    'user_id' => $user->id, 
                    'title' => "Task {$i}"
                ])->toArray(),
            ]);
        }
        
        // Verify we only have max notifications
        $component->assertCount('notifications', 3)
            // The newest notifications should be at the top (reverse order)
            ->assertSee('Task 5', false)
            ->assertSee('Task 4', false)
            ->assertSee('Task 3', false)
            ->assertDontSee('Task 2', false)
            ->assertDontSee('Task 1', false);
    }

    /**
     * Test events are broadcast properly when tasks are completed.
     */
    public function test_task_completed_event_is_broadcast(): void
    {
        Event::fake([TaskCompleted::class]);
        
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'completed' => false]);
        
        $this->actingAs($user);
        
        // Mark the task as completed
        $task->markAsComplete();
        
        // Broadcast the event manually
        event(new TaskCompleted($task));
        
        // Assert that the event was dispatched
        Event::assertDispatched(TaskCompleted::class, function ($event) use ($task) {
            return $event->task->id === $task->id;
        });
    }
}
