<?php

namespace Tests\Feature\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\HypervelTestHelpers;
use Tests\TestCase;

class AsyncApiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        HypervelTestHelpers::setupHypervelTestEnv();
    }

    /** @test */
    public function it_returns_authenticated_users_tasks()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        
        // Create tasks for both users
        $userTasks = Task::factory()->count(3)->create([
            'user_id' => $user->id
        ]);
        
        $otherUserTasks = Task::factory()->count(2)->create([
            'user_id' => $otherUser->id
        ]);
        
        $response = $this->actingAs($user)->getJson('/api/tasks');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'completed',
                        'user_id',
                        'due_date',
                        'priority',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta' => [
                    'total_incomplete',
                    'total_completed',
                    'total_overdue',
                    'total_due_today'
                ]
            ]);
        
        // Verify we only see our own tasks
        foreach ($userTasks as $task) {
            $response->assertJsonFragment(['id' => $task->id]);
        }
        
        foreach ($otherUserTasks as $task) {
            $response->assertJsonMissing(['id' => $task->id]);
        }
    }
    
    /** @test */
    public function it_paginates_tasks()
    {
        $user = User::factory()->create();
        
        // Create 25 tasks
        $tasks = Task::factory()->count(25)->create([
            'user_id' => $user->id
        ]);
        
        // Default is 10 per page
        $response = $this->actingAs($user)->getJson('/api/tasks');
        
        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data',
                'links' => [
                    'first', 'last', 'prev', 'next'
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links',
                    'path',
                    'per_page',
                    'to',
                    'total',
                    'total_incomplete',
                    'total_completed',
                    'total_overdue',
                    'total_due_today'
                ]
            ]);
        
        // Test page 2
        $page2Response = $this->actingAs($user)->getJson('/api/tasks?page=2');
        $page2Response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.current_page', 2);
            
        // Ensure page 1 and page 2 have different tasks
        $page1Ids = collect($response->json('data'))->pluck('id');
        $page2Ids = collect($page2Response->json('data'))->pluck('id');
        
        $this->assertEmpty($page1Ids->intersect($page2Ids));
    }
    
    /** @test */
    public function it_can_filter_tasks_by_status()
    {
        $user = User::factory()->create();
        
        // Create mix of completed and incomplete tasks
        $completedTasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'completed' => true
        ]);
        
        $incompleteTasks = Task::factory()->count(4)->create([
            'user_id' => $user->id,
            'completed' => false
        ]);
        
        // Test completed filter
        $completedResponse = $this->actingAs($user)->getJson('/api/tasks?status=completed');
        $completedResponse->assertStatus(200)
            ->assertJsonCount(3, 'data');
            
        foreach ($completedTasks as $task) {
            $completedResponse->assertJsonFragment(['id' => $task->id]);
        }
        
        // Test active filter
        $activeResponse = $this->actingAs($user)->getJson('/api/tasks?status=active');
        $activeResponse->assertStatus(200)
            ->assertJsonCount(4, 'data');
            
        foreach ($incompleteTasks as $task) {
            $activeResponse->assertJsonFragment(['id' => $task->id]);
        }
    }
    
    /** @test */
    public function it_can_filter_tasks_by_due_date()
    {
        $user = User::factory()->create();
        
        // Create tasks with different due dates
        $todayTasks = Task::factory()->count(2)->create([
            'user_id' => $user->id,
            'due_date' => now()->format('Y-m-d'),
            'completed' => false
        ]);
        
        $tomorrowTasks = Task::factory()->count(2)->create([
            'user_id' => $user->id,
            'due_date' => now()->addDay()->format('Y-m-d'),
            'completed' => false
        ]);
        
        $yesterdayTasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'due_date' => now()->subDay()->format('Y-m-d'),
            'completed' => false
        ]);
        
        // Test due-today filter
        $todayResponse = $this->actingAs($user)->getJson('/api/tasks?due=today');
        $todayResponse->assertStatus(200)
            ->assertJsonCount(2, 'data');
        
        foreach ($todayTasks as $task) {
            $todayResponse->assertJsonFragment(['id' => $task->id]);
        }
        
        // Test overdue filter
        $overdueResponse = $this->actingAs($user)->getJson('/api/tasks?due=overdue');
        $overdueResponse->assertStatus(200)
            ->assertJsonCount(3, 'data');
        
        foreach ($yesterdayTasks as $task) {
            $overdueResponse->assertJsonFragment(['id' => $task->id]);
        }
        
        // Test upcoming filter
        $upcomingResponse = $this->actingAs($user)->getJson('/api/tasks?due=upcoming');
        $upcomingResponse->assertStatus(200)
            ->assertJsonCount(2, 'data');
        
        foreach ($tomorrowTasks as $task) {
            $upcomingResponse->assertJsonFragment(['id' => $task->id]);
        }
    }
    
    /** @test */
    public function it_can_search_tasks_by_title()
    {
        $user = User::factory()->create();
        
        // Create tasks with different titles
        $task1 = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Buy groceries'
        ]);
        
        $task2 = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Call mom'
        ]);
        
        $task3 = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Buy birthday gift'
        ]);
        
        // Search for 'buy'
        $response = $this->actingAs($user)->getJson('/api/tasks?search=buy');
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $task1->id])
            ->assertJsonFragment(['id' => $task3->id])
            ->assertJsonMissing(['id' => $task2->id]);
    }
    
    /** @test */
    public function unauthenticated_users_cannot_access_tasks()
    {
        $response = $this->getJson('/api/tasks');
        $response->assertStatus(401);
    }

    /** @test */
    public function it_gets_dashboard_stats_concurrently()
    {
        // Create a user with tasks
        $user = User::factory()->create();
        $tasks = Task::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        // Mark 2 tasks as completed
        $tasks[0]->update(['completed' => true]);
        $tasks[1]->update(['completed' => true]);

        // Set 1 task as overdue
        $tasks[2]->update([
            'completed' => false,
            'due_date' => now()->subDays(2)
        ]);

        // Authenticate as the user
        $this->actingAs($user, 'sanctum');

        // Send request to get dashboard stats
        $response = $this->getJson('/api/async/dashboard-stats');

        // Assert response is correct
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'tasks_count',
                    'completed_count',
                    'pending_count',
                    'overdue_count',
                    'recent_tasks',
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'tasks_count' => 5,
                    'completed_count' => 2,
                    'pending_count' => 3,
                    'overdue_count' => 1,
                ]
            ]);

        // Assert recent tasks contains the right count
        $this->assertCount(5, $response->json('data.recent_tasks'));
    }

    /** @test */
    public function it_processes_tasks_in_bulk()
    {
        // Create a user with tasks
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);

        // Set one task with high priority
        $tasks[0]->update(['priority' => 'high']);

        // Set one task with due date today
        $tasks[1]->update(['due_date' => now()]);

        // Set one task as overdue
        $tasks[2]->update(['due_date' => now()->subDays(1)]);

        // Authenticate as the user
        $this->actingAs($user, 'sanctum');

        // Send request to process tasks
        $response = $this->postJson('/api/async/process-tasks');

        // Assert response is correct
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Tasks processed successfully',
            ]);

        // Check that the tasks were updated with processed_at and correct status
        $this->assertDatabaseHas('tasks', [
            'id' => $tasks[0]->id,
            'status' => 'high-priority',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $tasks[1]->id,
            'status' => 'due-today',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $tasks[2]->id,
            'status' => 'overdue',
        ]);

        // Check that processed_at is set for all tasks
        foreach ($tasks as $task) {
            $this->assertNotNull($task->fresh()->processed_at);
        }
    }

    /** @test */
    public function it_returns_error_for_unauthenticated_users()
    {
        // Access route without authentication
        $response = $this->getJson('/api/async/dashboard-stats');
        
        // Assert it returns 401 Unauthorized
        $response->assertStatus(401);
    }

    /**
     * @test
     * @group external-api
     */
    public function it_fetches_external_apis_concurrently()
    {
        // This test is marked as external-api and may be skipped in CI environments
        // since it depends on external API availability

        // Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Use a mock if external APIs are not accessible in the test environment
        if (env('MOCK_EXTERNAL_APIS', true)) {
            $this->markTestSkipped('This test requires external API access. Set MOCK_EXTERNAL_APIS=false to run it.');
        }

        // Send request to fetch external APIs
        $response = $this->getJson('/api/async/external-apis');

        // Assert response structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'weather',
                    'github',
                    'quotes',
                ]
            ]);
    }
} 