<?php

namespace Tests\Feature\Api;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test_device')->plainTextToken;
    }

    /**
     * Test retrieving all tasks for a user.
     */
    public function test_user_can_get_all_tasks(): void
    {
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'completed',
                        'due_date',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test creating a new task.
     */
    public function test_user_can_create_task(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'due_date' => now()->addDays(1)->toDateTimeString(),
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'completed',
                    'due_date',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('Task created successfully', $response->json('message'));
        $this->assertEquals($taskData['title'], $response->json('data.title'));
        $this->assertEquals($taskData['description'], $response->json('data.description'));
        $this->assertFalse($response->json('data.completed'));

        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test retrieving a specific task.
     */
    public function test_user_can_get_specific_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'completed',
                    'due_date',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($task->id, $response->json('data.id'));
    }

    /**
     * Test updating a task.
     */
    public function test_user_can_update_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated task description',
            'due_date' => now()->addDays(5)->toDateTimeString(),
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'completed',
                    'due_date',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('Task updated successfully', $response->json('message'));
        $this->assertEquals($updatedData['title'], $response->json('data.title'));
        $this->assertEquals($updatedData['description'], $response->json('data.description'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $updatedData['title'],
        ]);
    }

    /**
     * Test toggling task completion status.
     */
    public function test_user_can_toggle_task_completion(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'completed',
                ],
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertTrue($response->json('data.completed'));
        $this->assertEquals('Task status toggled successfully', $response->json('message'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => true,
        ]);

        // Toggle back to incomplete
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->patchJson("/api/tasks/{$task->id}/toggle");

        $this->assertFalse($response->json('data.completed'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => false,
        ]);
    }

    /**
     * Test deleting a task.
     */
    public function test_user_can_delete_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('Task deleted successfully', $response->json('message'));
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test user cannot access another user's tasks.
     */
    public function test_user_cannot_access_others_tasks(): void
    {
        $anotherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Should Not Update',
            ]);

        $response->assertStatus(403);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }
}
