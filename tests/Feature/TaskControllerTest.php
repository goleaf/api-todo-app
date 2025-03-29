<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user
        $this->user = User::factory()->create();
        
        // Create a category
        $this->category = Category::factory()->create(['user_id' => $this->user->id]);
        
        // Create a token for authenticated requests
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function unauthenticated_users_cannot_access_task_endpoints()
    {
        // Index endpoint
        $this->getJson('/api/tasks')
            ->assertStatus(401);
        
        // Show endpoint
        $this->getJson('/api/tasks/1')
            ->assertStatus(401);
        
        // Store endpoint
        $this->postJson('/api/tasks', [])
            ->assertStatus(401);
        
        // Update endpoint
        $this->putJson('/api/tasks/1', [])
            ->assertStatus(401);
        
        // Delete endpoint
        $this->deleteJson('/api/tasks/1')
            ->assertStatus(401);
    }

    /** @test */
    public function it_lists_all_tasks_for_authenticated_user()
    {
        // Create tasks for the user
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);
        
        // Create tasks for another user
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        Task::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id
        ]);
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'due_date',
                        'completed',
                        'priority',
                        'progress',
                        'category_id',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_shows_a_specific_task()
    {
        // Create a task for the user
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Test Task'
        ]);
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks/' . $task->id);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => 'Test Task',
                    'user_id' => $this->user->id,
                    'category_id' => $this->category->id
                ]
            ]);
    }

    /** @test */
    public function it_cannot_show_tasks_belonging_to_other_users()
    {
        // Create a task for another user
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id
        ]);
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks/' . $task->id);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function it_creates_a_new_task()
    {
        $taskData = [
            'title' => 'New Test Task',
            'description' => 'This is a test task description',
            'due_date' => now()->addDay()->format('Y-m-d'),
            'priority' => 2,
            'category_id' => $this->category->id
        ];
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/tasks', $taskData);
        
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'New Test Task',
                    'description' => 'This is a test task description',
                    'priority' => 2,
                    'category_id' => $this->category->id,
                    'user_id' => $this->user->id
                ]
            ]);
        
        // Check database
        $this->assertDatabaseHas('tasks', [
            'title' => 'New Test Task',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_a_task()
    {
        // Missing title and category_id
        $taskData = [
            'description' => 'This is a test task description',
            'due_date' => now()->addDay()->format('Y-m-d'),
            'priority' => 2
        ];
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/tasks', $taskData);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'category_id']);
    }

    /** @test */
    public function it_updates_a_task()
    {
        // Create a task for the user
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Original Title',
            'description' => 'Original Description',
            'completed' => false
        ]);
        
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'completed' => true
        ];
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/tasks/' . $task->id, $updateData);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => 'Updated Title',
                    'description' => 'Updated Description',
                    'completed' => true
                ]
            ]);
        
        // Check database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'completed' => true
        ]);
    }

    /** @test */
    public function it_cannot_update_tasks_belonging_to_other_users()
    {
        // Create a task for another user
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
            'title' => 'Other User Task'
        ]);
        
        $updateData = [
            'title' => 'Updated Title'
        ];
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/tasks/' . $task->id, $updateData);
        
        $response->assertStatus(403);
        
        // Database should be unchanged
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Other User Task'
        ]);
    }

    /** @test */
    public function it_deletes_a_task()
    {
        // Create a task for the user
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/tasks/' . $task->id);
        
        $response->assertStatus(204);
        
        // Check database
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    /** @test */
    public function it_cannot_delete_tasks_belonging_to_other_users()
    {
        // Create a task for another user
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id
        ]);
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/tasks/' . $task->id);
        
        $response->assertStatus(403);
        
        // Database should be unchanged
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id
        ]);
    }

    /** @test */
    public function it_searches_tasks_by_term()
    {
        // Create tasks with searchable terms
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Meeting with client',
            'description' => 'Discuss project requirements'
        ]);
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Buy groceries',
            'description' => 'Milk, eggs, bread'
        ]);
        
        // Create a task for another user (should not be returned)
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        Task::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
            'title' => 'Meeting with team',
            'description' => 'Team meeting'
        ]);
        
        // Search for "client"
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks/search?q=client');
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Meeting with client');
        
        // Search for "meeting" (should only return our user's meeting, not the other user's)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks/search?q=meeting');
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Meeting with client');
    }

    /** @test */
    public function it_filters_tasks_by_category()
    {
        // Create a second category
        $category2 = Category::factory()->create(['user_id' => $this->user->id]);
        
        // Create tasks in first category
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);
        
        // Create tasks in second category
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $category2->id
        ]);
        
        // Filter by first category
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?category=' . $this->category->id);
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
        
        // Filter by second category
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?category=' . $category2->id);
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_filters_tasks_by_completion_status()
    {
        // Create completed tasks
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => true
        ]);
        
        // Create incomplete tasks
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => false
        ]);
        
        // Filter completed tasks
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?status=completed');
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
        
        // Filter incomplete tasks
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?status=incomplete');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_returns_tasks_sorted_by_due_date()
    {
        // Create tasks with different due dates
        $task1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'title' => 'Task due in 5 days'
        ]);
        
        $task2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'due_date' => now()->addDays(1)->format('Y-m-d'),
            'title' => 'Task due tomorrow'
        ]);
        
        $task3 = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'title' => 'Task due in 10 days'
        ]);
        
        // Get tasks sorted by due date
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?sort=due_date');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.title', 'Task due tomorrow')
            ->assertJsonPath('data.1.title', 'Task due in 5 days')
            ->assertJsonPath('data.2.title', 'Task due in 10 days');
    }
} 