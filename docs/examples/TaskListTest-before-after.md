# TaskList Component Test Conversion Example

This document demonstrates how to convert a Vue.js task list component test to a Livewire test using our specialized test case classes.

## Vue Test (Before)

```javascript
// resources/js/components/TaskList.test.js
import { mount } from '@vue/test-utils'
import TaskList from '@/components/TaskList.vue'
import { createStore } from 'vuex'
import axios from 'axios'

// Mock axios
jest.mock('axios')

describe('TaskList.vue', () => {
  let store
  let wrapper
  
  beforeEach(() => {
    // Create mock tasks
    const tasks = [
      { id: 1, title: 'Task 1', completed: false },
      { id: 2, title: 'Task 2', completed: true },
      { id: 3, title: 'Task 3', completed: false }
    ]
    
    // Mock axios response
    axios.get.mockResolvedValue({ data: { tasks } })
    
    // Create mock store
    store = createStore({
      state: {
        tasks: []
      },
      getters: {
        allTasks: state => state.tasks,
        completedTasks: state => state.tasks.filter(task => task.completed),
        incompleteTasks: state => state.tasks.filter(task => !task.completed)
      },
      mutations: {
        setTasks: (state, tasks) => { state.tasks = tasks },
        toggleTask: (state, taskId) => {
          const task = state.tasks.find(t => t.id === taskId)
          if (task) task.completed = !task.completed
        },
        deleteTask: (state, taskId) => {
          state.tasks = state.tasks.filter(t => t.id !== taskId)
        }
      },
      actions: {
        fetchTasks: jest.fn(({ commit }) => {
          return axios.get('/api/tasks')
            .then(response => {
              commit('setTasks', response.data.tasks)
            })
        }),
        toggleTaskStatus: jest.fn(({ commit }, taskId) => {
          return axios.patch(`/api/tasks/${taskId}/toggle`)
            .then(() => {
              commit('toggleTask', taskId)
            })
        }),
        removeTask: jest.fn(({ commit }, taskId) => {
          return axios.delete(`/api/tasks/${taskId}`)
            .then(() => {
              commit('deleteTask', taskId)
            })
        })
      }
    })

    // Mount component
    wrapper = mount(TaskList, {
      global: {
        plugins: [store],
        stubs: ['router-link']
      }
    })
  })

  test('displays tasks from store', async () => {
    // Simulate successful API call
    await store.dispatch('fetchTasks')
    
    // Wait for component to update
    await wrapper.vm.$nextTick()
    
    // Check if all tasks are displayed
    expect(wrapper.findAll('.task-item')).toHaveLength(3)
  })

  test('can filter tasks', async () => {
    // Simulate successful API call
    await store.dispatch('fetchTasks')
    
    // Wait for component to update
    await wrapper.vm.$nextTick()
    
    // Test the "all tasks" filter (default)
    expect(wrapper.findAll('.task-item')).toHaveLength(3)
    
    // Test the "completed" filter
    await wrapper.find('select.task-filter').setValue('completed')
    await wrapper.vm.$nextTick()
    expect(wrapper.findAll('.task-item')).toHaveLength(1)
    
    // Test the "incomplete" filter
    await wrapper.find('select.task-filter').setValue('incomplete')
    await wrapper.vm.$nextTick()
    expect(wrapper.findAll('.task-item')).toHaveLength(2)
  })

  test('can toggle task completion status', async () => {
    // Simulate successful API call
    await store.dispatch('fetchTasks')
    
    // Wait for component to update
    await wrapper.vm.$nextTick()
    
    // Find the first incomplete task
    const taskItem = wrapper.findAll('.task-item')[0]
    
    // Simulate clicking the toggle button
    await taskItem.find('.toggle-button').trigger('click')
    
    // Check that the action was called with the right task ID
    expect(store.actions.toggleTaskStatus).toHaveBeenCalledWith(
      expect.any(Object),
      1,
      undefined
    )
  })

  test('can delete a task', async () => {
    // Simulate successful API call
    await store.dispatch('fetchTasks')
    
    // Wait for component to update
    await wrapper.vm.$nextTick()
    
    // Find the first task
    const taskItem = wrapper.findAll('.task-item')[0]
    
    // Simulate clicking the delete button
    await taskItem.find('.delete-button').trigger('click')
    
    // Check that the action was called with the right task ID
    expect(store.actions.removeTask).toHaveBeenCalledWith(
      expect.any(Object),
      1,
      undefined
    )
  })
})
```

## Livewire Test (After)

```php
<?php
// tests/Feature/Livewire/Tasks/TaskListTest.php

namespace Tests\Feature\Livewire\Tasks;

use App\Livewire\Tasks\TaskList;
use App\Models\Todo;
use Livewire\Livewire;
use Tests\Feature\Livewire\LivewireTestCase;

class TaskListTest extends LivewireTestCase
{
    /** @test */
    public function it_displays_tasks_for_authenticated_user()
    {
        // Create a user with 3 tasks
        [$user, $todos] = LivewireTestHelpers::createTestEnvironment(3);
        
        // Test that the component shows the tasks
        $this->assertLivewireCanSee(TaskList::class, $todos[0]->title);
        $this->assertLivewireCanSee(TaskList::class, $todos[1]->title);
        $this->assertLivewireCanSee(TaskList::class, $todos[2]->title);
    }
    
    /** @test */
    public function it_can_filter_tasks()
    {
        // Create a user with completed and incomplete tasks
        [$user, $todos] = LivewireTestHelpers::createTestEnvironment(3);
        
        // Make the first task completed
        $todos[0]->update(['completed' => true]);
        
        // Test "all tasks" filter (default)
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->assertSee($todos[0]->title)
            ->assertSee($todos[1]->title)
            ->assertSee($todos[2]->title);
            
        // Test "completed" filter
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->set('filter', 'completed')
            ->assertSee($todos[0]->title)
            ->assertDontSee($todos[1]->title)
            ->assertDontSee($todos[2]->title);
            
        // Test "incomplete" filter
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->set('filter', 'incomplete')
            ->assertDontSee($todos[0]->title)
            ->assertSee($todos[1]->title)
            ->assertSee($todos[2]->title);
    }
    
    /** @test */
    public function it_can_toggle_task_completion_status()
    {
        // Create a task
        $todo = LivewireTestHelpers::createTodoWithUser(['completed' => false]);
        $user = $todo->user;
        
        // Toggle the task status
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('toggleComplete', $todo->id)
            ->assertEmitted('task-updated', $todo->id);
            
        // Check that the task is now completed
        $this->assertTrue($todo->fresh()->completed);
        
        // Toggle it back
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('toggleComplete', $todo->id)
            ->assertEmitted('task-updated', $todo->id);
            
        // Check that the task is now incomplete
        $this->assertFalse($todo->fresh()->completed);
    }
    
    /** @test */
    public function it_can_delete_a_task()
    {
        // Create a task
        $todo = LivewireTestHelpers::createTodoWithUser();
        $user = $todo->user;
        
        // Delete the task
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('deleteTask', $todo->id)
            ->assertEmitted('task-deleted', $todo->id);
            
        // Check that the task no longer exists
        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }
    
    /** @test */
    public function it_shows_appropriate_message_when_no_tasks_exist()
    {
        // Create a user with no tasks
        [$user, $todos] = LivewireTestHelpers::createTestEnvironment(0);
        
        // Test that the component shows a message
        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->assertSee('No tasks found');
    }
    
    /** @test */
    public function it_prevents_users_from_accessing_other_users_tasks()
    {
        // Create a task for user1
        $todo = LivewireTestHelpers::createTodoWithUser();
        $user1 = $todo->user;
        
        // Create user2
        $user2 = $this->createUser();
        
        // Check that user2 doesn't see user1's task
        Livewire::actingAs($user2)
            ->test(TaskList::class)
            ->assertDontSee($todo->title);
            
        // Try to toggle user1's task as user2
        Livewire::actingAs($user2)
            ->test(TaskList::class)
            ->call('toggleComplete', $todo->id)
            ->assertForbidden();
            
        // Try to delete user1's task as user2
        Livewire::actingAs($user2)
            ->test(TaskList::class)
            ->call('deleteTask', $todo->id)
            ->assertForbidden();
    }
}
```

## Key Differences

1. **Test Organization**:
   - Vue test: One large file with multiple tests and complex setup
   - Livewire test: Clean, modular tests with helpers handling setup

2. **State Management**:
   - Vue test: Complex Vuex store mocking with actions, mutations, getters
   - Livewire test: Direct database interactions, no mocking needed

3. **Component Mounting**:
   - Vue test: Manual wrapper mounting with global plugins
   - Livewire test: Simplified mounting with `Livewire::test()` or helpers

4. **Async Handling**:
   - Vue test: Manual `$nextTick()` calls to handle Vue's reactivity
   - Livewire test: No need to wait for updates, testing is synchronous

5. **API Testing**:
   - Vue test: Mock Axios calls and responses
   - Livewire test: Real database operations, no need to mock API calls

6. **Test Assertions**:
   - Vue test: Find DOM elements, check length/content
   - Livewire test: Higher-level assertions (`assertSee`, `assertEmitted`)

7. **User Authentication**:
   - Vue test: Complex authentication mocking
   - Livewire test: Simple `actingAs($user)` authentication

8. **Test Helpers**:
   - Vue test: Manual setup for each test
   - Livewire test: Reusable helpers and base classes 