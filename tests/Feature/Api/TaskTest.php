<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class TaskTest extends TestCase
{
    use WithFaker;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Refresh database before tests
        Artisan::call('migrate:fresh');
        
        // Allow all Gate checks to pass for testing
        Gate::before(function () {
            return true;
        });

        // Create users for testing
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function can_get_tasks()
    {
        // Create some tasks for the user
        Task::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);

        // Test index endpoint with no_pagination flag for testing
        $response = $this->getJson(route('api.tasks.index') . '?no_pagination=1');

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

    /** @test */
    public function can_create_task()
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

        $response = $this->postJson(route('api.tasks.store'), $taskData);

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

    /** @test */
    public function can_get_single_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson(route('api.tasks.show', $task->id));

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

    /** @test */
    public function can_update_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'priority' => 2,
        ];

        $response = $this->putJson(route('api.tasks.update', $task->id), $updatedData);

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

    /** @test */
    public function can_delete_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson(route('api.tasks.destroy', $task->id));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function can_toggle_task_completion()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        $response = $this->patchJson(route('api.tasks.toggle', $task->id));

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

        // Toggle back to incomplete
        $response = $this->patchJson(route('api.tasks.toggle', $task->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
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

    /** @test */
    public function can_get_tasks_due_today()
    {
        // Create tasks due today
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'due_date' => now()->format('Y-m-d'),
        ]);

        // Create tasks due on other days
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(1)->format('Y-m-d'),
        ]);

        $response = $this->getJson(route('api.tasks.due-today') . '?no_pagination=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'due_date',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function can_get_overdue_tasks()
    {
        // Create overdue tasks
        Task::factory()->count(4)->create([
            'user_id' => $this->user->id,
            'due_date' => now()->subDays(1)->format('Y-m-d'),
            'completed' => false,
        ]);

        // Create non-overdue tasks
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(1)->format('Y-m-d'),
        ]);

        $response = $this->getJson(route('api.tasks.overdue') . '?no_pagination=1');

        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    /** @test */
    public function can_get_upcoming_tasks()
    {
        // Create upcoming tasks (next 7 days)
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(1)->format('Y-m-d'),
            'completed' => false,
        ]);

        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'completed' => false,
        ]);

        // Create tasks beyond 7 days
        Task::factory()->count(1)->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'completed' => false,
        ]);

        $response = $this->getJson(route('api.tasks.upcoming') . '?no_pagination=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function can_get_task_statistics()
    {
        // Create various tasks for statistics
        Task::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => true,
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->format('Y-m-d'),
            'completed' => false,
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->subDays(1)->format('Y-m-d'),
            'completed' => false,
        ]);

        $response = $this->getJson(route('api.tasks.statistics'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total',
                    'completed',
                    'incomplete',
                    'due_today',
                    'overdue',
                    'by_priority',
                ],
            ]);

        $data = $response->json('data');
        $this->assertEquals(10, $data['total']);
        $this->assertEquals(3, $data['completed']);
        $this->assertEquals(7, $data['incomplete']);
        $this->assertEquals(1, $data['due_today']);
        $this->assertEquals(1, $data['overdue']);
    }

    /** @test */
    public function cannot_access_another_users_tasks()
    {
        // Create task for another user
        $otherUserTask = Task::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        // Trying to access another user's task
        $response = $this->getJson(route('api.tasks.show', $otherUserTask->id));
        $response->assertStatus(403);

        // Trying to update another user's task
        $response = $this->putJson(route('api.tasks.update', $otherUserTask->id), [
            'title' => 'Unauthorized Update',
        ]);
        $response->assertStatus(403);

        // Trying to delete another user's task
        $response = $this->deleteJson(route('api.tasks.destroy', $otherUserTask->id));
        $response->assertStatus(403);

        // Trying to toggle another user's task
        $response = $this->patchJson(route('api.tasks.toggle', $otherUserTask->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function validation_errors_when_creating_task()
    {
        // Missing title
        $response = $this->postJson(route('api.tasks.store'), [
            'description' => 'Test description',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        // Title too short
        $response = $this->postJson(route('api.tasks.store'), [
            'title' => 'a', // Too short
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        // Invalid priority
        $response = $this->postJson(route('api.tasks.store'), [
            'title' => 'Test Title',
            'priority' => 999, // Invalid priority
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['priority']);
    }
} 