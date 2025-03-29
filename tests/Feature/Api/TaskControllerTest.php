<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Task;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all tasks via API.
     */
    public function test_user_can_get_their_tasks(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $tasks = Task::factory(3)->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
        ]);

        // Create tasks for another user (should not be returned)
        $otherUser = User::factory()->create();
        $otherTodo = Todo::factory()->create(['user_id' => $otherUser->id]);
        Task::factory(2)->create([
            'todo_id' => $otherTodo->id,
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'completed',
                    'completed_at',
                    'priority',
                    'todo_id',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * Test getting tasks for a specific todo.
     */
    public function test_user_can_get_tasks_for_todo(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $tasks = Task::factory(3)->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/todos/{$todo->id}/tasks");

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Test getting a specific task via API.
     */
    public function test_user_can_get_specific_task(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'completed' => $task->completed,
                'todo_id' => $todo->id,
            ],
        ]);
    }

    /**
     * Test creating a task via API.
     */
    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id]);

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'priority' => 2,
            'todo_id' => $todo->id,
            'category_id' => $category->id,
        ];

        $response = $this->actingAs($user)->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'priority' => 2,
                'todo_id' => $todo->id,
            ],
        ]);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'user_id' => $user->id,
            'todo_id' => $todo->id,
        ]);
    }

    /**
     * Test updating a task via API.
     */
    public function test_user_can_update_task(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
        ]);

        $updatedData = [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'priority' => 3,
        ];

        $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => 'Updated Task',
                'description' => 'Updated Description',
                'priority' => 3,
            ],
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'priority' => 3,
        ]);
    }

    /**
     * Test toggling task completion status.
     */
    public function test_user_can_toggle_task_completion(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
            'completed' => false,
            'completed_at' => null,
        ]);

        $response = $this->actingAs($user)->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $task->id,
                'completed' => true,
            ],
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => true,
        ]);

        // Toggle back to incomplete
        $response = $this->actingAs($user)->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $task->id,
                'completed' => false,
            ],
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => false,
        ]);
    }

    /**
     * Test deleting a task via API.
     */
    public function test_user_can_delete_task(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($task);
    }

    /**
     * Test user cannot access another user's task.
     */
    public function test_user_cannot_access_another_users_task(): void
    {
        $user1 = User::factory()->create();
        $todo1 = Todo::factory()->create(['user_id' => $user1->id]);
        $task = Task::factory()->create([
            'todo_id' => $todo1->id,
            'user_id' => $user1->id,
        ]);

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    /**
     * Test user cannot update another user's task.
     */
    public function test_user_cannot_update_another_users_task(): void
    {
        $user1 = User::factory()->create();
        $todo1 = Todo::factory()->create(['user_id' => $user1->id]);
        $task = Task::factory()->create([
            'todo_id' => $todo1->id,
            'user_id' => $user1->id,
        ]);

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Hacked Task',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test user cannot delete another user's task.
     */
    public function test_user_cannot_delete_another_users_task(): void
    {
        $user1 = User::factory()->create();
        $todo1 = Todo::factory()->create(['user_id' => $user1->id]);
        $task = Task::factory()->create([
            'todo_id' => $todo1->id,
            'user_id' => $user1->id,
        ]);

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    /**
     * Test task validation rules.
     */
    public function test_task_validation_rules(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', [
            // Missing required fields
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'todo_id']);
    }
}
