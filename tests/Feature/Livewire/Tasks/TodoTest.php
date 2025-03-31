<?php

namespace Tests\Feature\Livewire\Tasks;

use App\Livewire\Tasks\Todo;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function todo_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(Todo::class)
            ->assertStatus(200);
    }

    /** @test */
    public function it_can_display_tasks()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Test Task',
        ]);

        Livewire::actingAs($this->user)
            ->test(Todo::class)
            ->assertSee('Test Task');
    }

    /** @test */
    public function it_can_filter_tasks()
    {
        $task1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Task 1',
            'completed' => true,
        ]);
        
        $task2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Task 2',
            'completed' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(Todo::class)
            ->assertSee('Task 1')
            ->assertSee('Task 2')
            ->call('applyFilter', 'completed')
            ->assertSee('Task 1')
            ->assertDontSee('Task 2')
            ->call('applyFilter', 'incomplete')
            ->assertDontSee('Task 1')
            ->assertSee('Task 2');
    }

    /** @test */
    public function it_can_search_tasks()
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Shopping List',
        ]);
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Work Meeting',
        ]);

        Livewire::actingAs($this->user)
            ->test(Todo::class)
            ->set('search', 'shopping')
            ->assertSee('Shopping List')
            ->assertDontSee('Work Meeting');
    }

    /** @test */
    public function it_can_toggle_task_status()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Toggle Test',
            'completed' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(Todo::class)
            ->call('toggleComplete', $task->id);
            
        $this->assertTrue($task->fresh()->completed);
    }
} 