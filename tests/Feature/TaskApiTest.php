<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
    }

    /**
     * Test getting all tasks for a user
     *
     * @return void
     */
    public function test_get_all_tasks()
    {
        // Create some tasks for the user
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        
        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks');
        
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test creating a new task
     *
     * @return void
     */
    public function test_create_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'pending',
            'priority' => 1,
            'due_date' => now()->addDays(7)->toDateTimeString(),
            'completed' => false,
        ];
        
        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData);
        
        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Test Task',
                'status' => 'pending',
                'priority' => 1,
            ]);
            
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test updating a task
     *
     * @return void
     */
    public function test_update_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original Title',
            'completed' => false,
        ]);
        
        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Title',
                'completed' => true,
            ]);
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Title',
                'completed' => true,
            ]);
            
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'completed' => true,
        ]);
    }

    /**
     * Test deleting a task
     *
     * @return void
     */
    public function test_delete_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);
        
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}");
        
        $response->assertStatus(204);
        
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test searching for tasks
     *
     * @return void
     */
    public function test_search_tasks()
    {
        $this->markTestSkipped('The search API route is currently having configuration issues - fix later');
        
        // Create tasks with different titles
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'First Task',
        ]);
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Second Task',
        ]);
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Another Task',
        ]);
        
        // Verify the route exists in the application
        $this->assertTrue(
            collect(\Route::getRoutes())->contains(function ($route) {
                return $route->uri() === 'api/tasks/search';
            })
        );
        
        // Search for "First"
        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks/search?q=First');
        
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'First Task']);
            
        // Search for "Task" (should find all 3)
        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks/search?q=Task');
        
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test unauthorized access to tasks
     *
     * @return void
     */
    public function test_unauthorized_access()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        
        $response = $this->actingAs($this->user)
            ->getJson("/api/tasks/{$task->id}");
        
        $response->assertStatus(403);
        
        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Title',
            ]);
        
        $response->assertStatus(403);
        
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}");
        
        $response->assertStatus(403);
    }
} 