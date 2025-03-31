<?php

namespace Tests\Feature\Livewire;

use App\Livewire\TodoMvc;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Feature\Livewire\LivewireTestCase;

class TodoMvcTest extends LivewireTestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_renders_component()
    {
        $this->assertLivewireCanRender(TodoMvc::class);
    }
    
    /** @test */
    public function it_can_create_a_new_task()
    {
        $this->actingAs($this->user);
        
        Livewire::test(TodoMvc::class)
            ->set('newTodo', 'Test new task')
            ->call('addTodo')
            ->assertSet('newTodo', '')
            ->assertDispatchedBrowserEvent('todoAdded'); // This event triggers frontend updates
        
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test new task',
            'user_id' => $this->user->id,
        ]);
    }
    
    /** @test */
    public function it_validates_new_task_input()
    {
        $this->actingAs($this->user);
        
        // Test with too short input
        Livewire::test(TodoMvc::class)
            ->set('newTodo', 'AB')
            ->call('addTodo')
            ->assertHasErrors(['newTodo' => 'min']);
        
        // Test with empty input
        Livewire::test(TodoMvc::class)
            ->set('newTodo', '')
            ->call('addTodo')
            ->assertHasErrors(['newTodo' => 'required']);
        
        // Test with valid input
        Livewire::test(TodoMvc::class)
            ->set('newTodo', 'Valid task')
            ->call('addTodo')
            ->assertHasNoErrors();
    }
    
    /** @test */
    public function it_can_toggle_task_completion()
    {
        $this->actingAs($this->user);
        
        // Create a task
        $todo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Toggle me',
            'completed' => false,
        ]);
        
        Livewire::test(TodoMvc::class)
            ->call('toggleComplete', $todo->id)
            ->assertStatus(200);
        
        // Verify task was toggled to completed
        $this->assertDatabaseHas('tasks', [
            'id' => $todo->id,
            'completed' => true,
        ]);
        
        // Toggle back to incomplete
        Livewire::test(TodoMvc::class)
            ->call('toggleComplete', $todo->id)
            ->assertStatus(200);
        
        // Verify task was toggled back to incomplete
        $this->assertDatabaseHas('tasks', [
            'id' => $todo->id,
            'completed' => false,
        ]);
    }
    
    /** @test */
    public function it_can_edit_a_task()
    {
        $this->actingAs($this->user);
        
        $todo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original title',
        ]);
        
        Livewire::test(TodoMvc::class)
            ->call('startEditing', $todo->id)
            ->assertSet('editingId', $todo->id)
            ->assertSet('editingTitle', 'Original title')
            ->set('editingTitle', 'Updated title')
            ->call('updateTodo')
            ->assertSet('editingId', null);
        
        $this->assertDatabaseHas('tasks', [
            'id' => $todo->id,
            'title' => 'Updated title',
        ]);
    }
    
    /** @test */
    public function it_can_cancel_editing()
    {
        $this->actingAs($this->user);
        
        $todo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original title',
        ]);
        
        Livewire::test(TodoMvc::class)
            ->call('startEditing', $todo->id)
            ->assertSet('editingId', $todo->id)
            ->set('editingTitle', 'This will not be saved')
            ->call('cancelEditing')
            ->assertSet('editingId', null);
        
        // Verify original title is still in database
        $this->assertDatabaseHas('tasks', [
            'id' => $todo->id,
            'title' => 'Original title',
        ]);
    }
    
    /** @test */
    public function it_can_delete_a_task()
    {
        $this->actingAs($this->user);
        
        $todo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Task to delete',
        ]);
        
        $this->assertDatabaseHas('tasks', ['id' => $todo->id]);
        
        Livewire::test(TodoMvc::class)
            ->call('deleteTodo', $todo->id);
        
        $this->assertDatabaseMissing('tasks', ['id' => $todo->id]);
    }
    
    /** @test */
    public function it_can_filter_tasks()
    {
        $this->actingAs($this->user);
        
        $completedTodo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Completed task',
            'completed' => true,
        ]);
        
        $activeTodo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Active task',
            'completed' => false,
        ]);
        
        // Test "completed" filter
        $component = Livewire::test(TodoMvc::class, ['filter' => 'completed']);
        $component->assertSee('Completed task')
            ->assertDontSee('Active task');
        
        // Test "active" filter
        $component = Livewire::test(TodoMvc::class, ['filter' => 'active']);
        $component->assertSee('Active task')
            ->assertDontSee('Completed task');
        
        // Test "all" filter
        $component = Livewire::test(TodoMvc::class, ['filter' => 'all']);
        $component->assertSee('Completed task')
            ->assertSee('Active task');
    }
    
    /** @test */
    public function it_can_clear_completed_tasks()
    {
        $this->actingAs($this->user);
        
        $completedTodo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Completed task',
            'completed' => true,
        ]);
        
        $activeTodo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Active task',
            'completed' => false,
        ]);
        
        Livewire::test(TodoMvc::class)
            ->call('clearCompleted');
        
        // Verify completed task was removed
        $this->assertDatabaseMissing('tasks', ['id' => $completedTodo->id]);
        
        // Verify active task remains
        $this->assertDatabaseHas('tasks', ['id' => $activeTodo->id]);
    }
    
    /** @test */
    public function it_can_set_due_date_for_task()
    {
        $this->actingAs($this->user);
        
        $todo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Task with due date',
            'due_date' => null,
        ]);
        
        $tomorrow = now()->addDay()->format('Y-m-d');
        
        Livewire::test(TodoMvc::class)
            ->call('setDueDate', $todo->id, $tomorrow);
        
        $this->assertDatabaseHas('tasks', [
            'id' => $todo->id,
            'due_date' => $tomorrow,
        ]);
    }
    
    /** @test */
    public function it_filters_by_due_date_statuses()
    {
        $this->actingAs($this->user);
        
        $todayTodo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Due today',
            'due_date' => now()->startOfDay(),
            'completed' => false,
        ]);
        
        $tomorrowTodo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Due tomorrow',
            'due_date' => now()->addDay()->startOfDay(),
            'completed' => false,
        ]);
        
        $yesterdayTodo = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Overdue',
            'due_date' => now()->subDay()->startOfDay(),
            'completed' => false,
        ]);
        
        // Test due today filter
        $component = Livewire::test(TodoMvc::class, ['filter' => 'due-today']);
        $component->assertSee('Due today')
            ->assertDontSee('Due tomorrow')
            ->assertDontSee('Overdue');
        
        // Test overdue filter
        $component = Livewire::test(TodoMvc::class, ['filter' => 'overdue']);
        $component->assertSee('Overdue')
            ->assertDontSee('Due today')
            ->assertDontSee('Due tomorrow');
        
        // Test upcoming filter
        $component = Livewire::test(TodoMvc::class, ['filter' => 'upcoming']);
        $component->assertSee('Due tomorrow')
            ->assertDontSee('Due today')
            ->assertDontSee('Overdue');
    }
} 