<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tasks\TaskList;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskListTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function the_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertStatus(200);
    }

    /** @test */
    public function it_shows_tasks_for_authenticated_user()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Test Task',
        ]);

        // Create a task for another user that shouldn't be visible
        Task::factory()->create(['title' => 'Other User Task']);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSee('Test Task')
            ->assertDontSee('Other User Task');
    }

    /** @test */
    public function it_can_filter_tasks_by_status()
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Incomplete Task',
            'completed' => false,
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Completed Task',
            'completed' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSee('Incomplete Task')
            ->assertSee('Completed Task')

            // Filter by completed
            ->set('filter', 'completed')
            ->assertSee('Completed Task')
            ->assertDontSee('Incomplete Task')

            // Filter by incomplete
            ->set('filter', 'incomplete')
            ->assertSee('Incomplete Task')
            ->assertDontSee('Completed Task');
    }

    /** @test */
    public function it_can_filter_tasks_by_category()
    {
        $category1 = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Category 1',
        ]);

        $category2 = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Category 2',
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category1->id,
            'title' => 'Task in Category 1',
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category2->id,
            'title' => 'Task in Category 2',
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSee('Task in Category 1')
            ->assertSee('Task in Category 2')

            // Filter by category 1
            ->set('selectedCategory', $category1->id)
            ->assertSee('Task in Category 1')
            ->assertDontSee('Task in Category 2');
    }

    /** @test */
    public function it_can_search_tasks()
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'First Task',
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Second Task',
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSee('First Task')
            ->assertSee('Second Task')

            // Search for "First"
            ->set('search', 'First')
            ->assertSee('First Task')
            ->assertDontSee('Second Task');
    }

    /** @test */
    public function it_can_sort_tasks()
    {
        $yesterday = Carbon::yesterday();
        $tomorrow = Carbon::tomorrow();

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'A Task',
            'due_date' => $tomorrow,
            'priority' => 1,
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Z Task',
            'due_date' => $yesterday,
            'priority' => 2,
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->assertSeeInOrder(['A Task', 'Z Task'])

            // Sort by title in descending order
            ->call('sortBy', 'title')
            ->call('sortBy', 'title') // Call twice to toggle direction
            ->assertSeeInOrder(['Z Task', 'A Task'])

            // Sort by priority
            ->call('sortBy', 'priority')
            ->assertSeeInOrder(['Z Task', 'A Task']);
    }

    /** @test */
    public function it_can_mark_task_as_complete()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Test Task',
            'completed' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('toggleComplete', $task->id)
            ->assertEmitted('task-updated');

        $this->assertTrue($task->fresh()->completed);
    }

    /** @test */
    public function it_can_mark_task_as_incomplete()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Test Task',
            'completed' => true,
            'completed_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('toggleComplete', $task->id)
            ->assertEmitted('task-updated');

        $this->assertFalse($task->fresh()->completed);
        $this->assertNull($task->fresh()->completed_at);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Task to Delete',
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('confirmTaskDeletion', $task->id)
            ->assertSet('taskIdToDelete', $task->id)
            ->call('deleteTask')
            ->assertEmitted('task-deleted');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
