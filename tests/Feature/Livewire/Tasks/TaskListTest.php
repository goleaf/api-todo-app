<?php

namespace Tests\Feature\Livewire\Tasks;

use App\Livewire\Tasks\TaskList;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Feature\Livewire\LivewireTestCase;

class TaskListTest extends LivewireTestCase
{
    use RefreshDatabase;

    /** @test */
    public function task_list_component_can_render()
    {
        $this->assertLivewireCanSee(TaskList::class, 'My Tasks');
    }

    /** @test */
    public function it_shows_tasks_for_authenticated_user()
    {
        // Create tasks for the authenticated user
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSee($tasks[0]->title)
            ->assertSee($tasks[1]->title)
            ->assertSee($tasks[2]->title);
    }

    /** @test */
    public function it_does_not_show_tasks_from_other_users()
    {
        // Create a task for the authenticated user
        $userTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'My Task',
        ]);

        // Create a task for another user
        $otherUser = $this->createUser();
        $otherUserTask = Task::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Task',
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSee($userTask->title)
            ->assertDontSee($otherUserTask->title);
    }

    /** @test */
    public function it_can_filter_tasks_by_status()
    {
        // Create completed and incomplete tasks
        $completedTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Completed Task',
            'completed' => true,
        ]);

        $incompleteTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Incomplete Task',
            'completed' => false,
        ]);

        // Test filtering by completed
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('filterTasks', 'completed')
            ->assertSee($completedTask->title)
            ->assertDontSee($incompleteTask->title);

        // Test filtering by incomplete
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('filterTasks', 'incomplete')
            ->assertSee($incompleteTask->title)
            ->assertDontSee($completedTask->title);

        // Test showing all tasks
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('filterTasks', 'all')
            ->assertSee($completedTask->title)
            ->assertSee($incompleteTask->title);
    }

    /** @test */
    public function it_can_toggle_task_completion()
    {
        // Create an incomplete task
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        // Toggle task completion
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('toggleTask', $task->id)
            ->assertEmitted('task-updated');

        // Check that the task is now completed
        $this->assertTrue(Task::find($task->id)->completed);

        // Toggle again to mark as incomplete
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('toggleTask', $task->id)
            ->assertEmitted('task-updated');

        // Check that the task is now incomplete
        $this->assertFalse(Task::find($task->id)->completed);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        // Create a task
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Delete the task
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('confirmTaskDeletion', $task->id) // First confirm deletion
            ->call('deleteTask') // Then delete
            ->assertEmitted('task-deleted');

        // Check that the task was deleted
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function it_cannot_delete_tasks_from_other_users()
    {
        // Create a task for another user
        $otherUser = $this->createUser();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        // Try to delete the task
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('confirmTaskDeletion', $task->id)
            ->call('deleteTask')
            ->assertStatus(403); // Expect authorization failure

        // Check that the task still exists
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function it_can_search_tasks()
    {
        // Create tasks with different titles
        $task1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Meeting with client',
        ]);

        $task2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Buy groceries',
        ]);

        $task3 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Team meeting',
        ]);

        // Search for 'meeting'
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->set('searchTerm', 'meeting')
            ->assertSee($task1->title)
            ->assertSee($task3->title)
            ->assertDontSee($task2->title);
    }

    /** @test */
    public function it_shows_empty_state_when_no_tasks()
    {
        // No tasks created for user

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSee('No tasks found');
    }

    /** @test */
    public function it_shows_loading_state_when_filtering()
    {
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('filterTasks', 'completed')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('loading-tasks');
    }
}
