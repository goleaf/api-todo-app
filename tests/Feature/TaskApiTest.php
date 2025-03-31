<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected $user;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_get_their_tasks()
    {
        // Create tasks for the user
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        // Create tasks for another user (should not be returned)
        $otherUser = User::factory()->create();
        Task::factory()->count(2)->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'success',
                'status_code',
                'message',
                'data',
                'meta' => ['pagination'],
            ]);
    }

    /** @test */
    public function user_can_filter_tasks_by_status()
    {
        // Create completed tasks
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => true,
        ]);

        // Create pending tasks
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        // Test completed filter
        $response = $this->actingAs($this->user)->getJson('/api/tasks?status=completed');
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // Test pending filter
        $response = $this->actingAs($this->user)->getJson('/api/tasks?status=pending');
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function user_can_create_a_task()
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'priority' => 'high',
            'category_id' => $category->id,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => [
                    'title' => 'Test Task',
                    'description' => 'This is a test task',
                    'priority' => 2, // high = 2
                    'category_id' => $category->id,
                    'user_id' => $this->user->id,
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_can_view_their_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'user_id' => $this->user->id,
                ],
            ]);
    }

    /** @test */
    public function user_cannot_view_other_users_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_update_their_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original Title',
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'title' => 'Updated Title',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function user_cannot_update_other_users_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_toggle_task_completion()
    {
        // Create an incomplete task
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        // Toggle to complete
        $response = $this->actingAs($this->user)->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'completed' => true,
                ],
            ]);

        // Toggle back to incomplete
        $response = $this->actingAs($this->user)->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'completed' => false,
                ],
            ]);
    }

    /** @test */
    public function user_can_delete_their_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task deleted successfully',
            ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_other_users_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }
}
