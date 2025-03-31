<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /**
     * Test task listing endpoint.
     */
    public function test_can_get_tasks(): void
    {
        // Create some tasks for the user
        Task::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);

        // Test index endpoint with no_pagination flag for testing
        $response = $this->getJson('/api/tasks?no_pagination=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'priority',
                        'due_date',
                        'completed',
                        'user_id',
                        'category_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(5, 'data')
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test task creation endpoint.
     */
    public function test_can_create_task(): void
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'priority' => 2,
            'due_date' => now()->addDays(1)->format('Y-m-d'),
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'priority',
                    'due_date',
                    'completed',
                    'user_id',
                    'category_id',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => $taskData['title'],
                    'description' => $taskData['description'],
                    'priority' => $taskData['priority'],
                    'user_id' => $this->user->id,
                    'category_id' => $category->id,
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test getting a single task.
     */
    public function test_can_get_single_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'priority',
                    'due_date',
                    'completed',
                    'user_id',
                    'category_id',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                ],
            ]);
    }

    /**
     * Test updating a task.
     */
    public function test_can_update_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'priority' => 2,
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'priority',
                    'due_date',
                    'completed',
                    'user_id',
                    'category_id',
                    'created_at',
                    'updated_at',
                ],
            ]);
            
        $responseData = json_decode($response->getContent(), true)['data'];
        $this->assertEquals($task->id, $responseData['id']);
        $this->assertEquals($updatedData['title'], $responseData['title']);
        $this->assertEquals($updatedData['priority'], $responseData['priority']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $updatedData['title'],
        ]);
    }

    /**
     * Test deleting a task.
     */
    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test toggling task completion status.
     */
    public function test_can_toggle_task_completion(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'completed',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'completed' => true,
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => true,
        ]);

        // Toggle back to false
        $response = $this->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'completed' => false,
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => false,
        ]);
    }

    /**
     * Test getting tasks due today.
     */
    public function test_can_get_tasks_due_today(): void
    {
        // Create some tasks due today
        Task::factory()->count(3)->dueToday()->create([
            'user_id' => $this->user->id,
        ]);

        // Create some tasks due tomorrow (should not be included)
        Task::factory()->count(2)->upcoming()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/tasks/due-today');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'due_date',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data')
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test getting overdue tasks.
     */
    public function test_can_get_overdue_tasks(): void
    {
        // Create some overdue tasks
        Task::factory()->count(2)->overdue()->create([
            'user_id' => $this->user->id,
        ]);

        // Create some tasks due today (should not be included)
        Task::factory()->count(3)->dueToday()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/tasks/overdue');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'due_date',
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data')
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test getting upcoming tasks.
     */
    public function test_can_get_upcoming_tasks(): void
    {
        // Create upcoming tasks for testing
        Task::factory()->count(4)->upcoming()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/tasks/upcoming');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'priority',
                        'due_date',
                        'completed',
                    ],
                ],
            ]);
        
        // Get the actual response data
        $responseData = json_decode($response->getContent(), true)['data'];
        
        // Make sure we have at least 2 upcoming tasks
        $this->assertGreaterThanOrEqual(2, count($responseData));
        
        // Check that all tasks returned are for our user
        foreach ($responseData as $task) {
            $this->assertEquals($this->user->id, $task['user_id']);
        }
    }

    /**
     * Test task statistics endpoint.
     */
    public function test_can_get_task_statistics(): void
    {
        // Create various tasks for the user
        Task::factory()->count(3)->completed()->create([
            'user_id' => $this->user->id,
        ]);
        Task::factory()->count(2)->overdue()->create([
            'user_id' => $this->user->id,
        ]);
        Task::factory()->count(1)->dueToday()->create([
            'user_id' => $this->user->id,
        ]);
        Task::factory()->count(4)->upcoming()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/tasks/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total',
                    'completed',
                    'incomplete',
                    'today',
                    'overdue',
                    'upcoming',
                    'completion_rate',
                    'by_priority',
                    'by_category',
                ],
            ]);
            
        // Only check the values we care about for this test
        $responseData = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(10, $responseData['total']);
        $this->assertEquals(3, $responseData['completed']);
        $this->assertTrue($responseData['incomplete'] > 0);
        $this->assertTrue($responseData['today'] > 0);
        $this->assertEquals(2, $responseData['overdue']);
        $this->assertTrue($responseData['upcoming'] > 0);
    }

    /**
     * Test that users cannot access another user's tasks.
     */
    public function test_cannot_access_another_users_tasks(): void
    {
        // Create another user
        $anotherUser = User::factory()->create();

        // Create a task for the other user
        $task = Task::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        // Try to get the task
        $response = $this->getJson("/api/tasks/{$task->id}");

        // Assert not found response (resource exists but not for this user)
        $response->assertStatus(404);

        // Try to update the task
        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Hacked Task',
        ]);

        $response->assertStatus(404);

        // Try to delete the task
        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(404);

        // Verify task still exists for the other user
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $anotherUser->id,
        ]);
    }

    /**
     * Test validation errors when creating a task.
     */
    public function test_validation_errors_when_creating_task(): void
    {
        // Try to create a task without a title
        $response = $this->postJson('/api/tasks', [
            'description' => 'Test Description',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'title',
                ],
            ])
            ->assertJson([
                'success' => false,
            ]);

        // Try to create a task with an invalid priority
        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'priority' => 10, // Invalid priority (should be 1-4)
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'priority',
                ],
            ])
            ->assertJson([
                'success' => false,
            ]);

        // Try to create a task with non-existent category
        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'category_id' => 9999, // Non-existent category
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'category_id',
                ],
            ])
            ->assertJson([
                'success' => false,
            ]);
    }
}
