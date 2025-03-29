<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $categories;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user
        $this->user = User::factory()->create();
        
        // Create a token for authenticated requests
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        
        // Create categories
        $this->categories = Category::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_stats_endpoints()
    {
        $this->getJson('/api/stats/overview')
            ->assertStatus(401);
            
        $this->getJson('/api/stats/completion-rate')
            ->assertStatus(401);
            
        $this->getJson('/api/stats/by-category')
            ->assertStatus(401);
            
        $this->getJson('/api/stats/by-priority')
            ->assertStatus(401);
            
        $this->getJson('/api/stats/by-date')
            ->assertStatus(401);
    }

    /** @test */
    public function it_returns_overview_statistics()
    {
        // Create tasks with different statuses
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => true,
            'category_id' => $this->categories[0]->id
        ]);
        
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => false,
            'category_id' => $this->categories[1]->id
        ]);
        
        // Create tasks for another user (should not be counted)
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        Task::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'completed' => true,
            'category_id' => $otherCategory->id
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/stats/overview');
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'total_tasks' => 5,
                    'completed_tasks' => 3,
                    'pending_tasks' => 2,
                    'completion_rate' => 60,
                    'total_categories' => 3
                ]
            ]);
    }

    /** @test */
    public function it_returns_completion_rate_over_time()
    {
        // Create completed tasks on different dates
        Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => true,
            'category_id' => $this->categories[0]->id,
            'completed_at' => now()->subDays(6),
            'created_at' => now()->subDays(7)
        ]);
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => true,
            'category_id' => $this->categories[0]->id,
            'completed_at' => now()->subDays(5),
            'created_at' => now()->subDays(7)
        ]);
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => true,
            'category_id' => $this->categories[0]->id,
            'completed_at' => now()->subDays(2),
            'created_at' => now()->subDays(3)
        ]);
        
        // Create incomplete tasks
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => false,
            'category_id' => $this->categories[1]->id,
            'created_at' => now()->subDays(4)
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/stats/completion-rate');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'date',
                        'completion_rate'
                    ]
                ]
            ]);
        
        // Should return data for the last 7 days
        $this->assertCount(7, $response->json('data'));
    }

    /** @test */
    public function it_returns_tasks_by_category()
    {
        // Create tasks in different categories
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[0]->id
        ]);
        
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[1]->id
        ]);
        
        Task::factory()->count(1)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[2]->id
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/stats/by-category');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'category_id',
                        'category_name',
                        'category_color',
                        'task_count'
                    ]
                ]
            ]);
        
        // Check the counts
        $responseData = $response->json('data');
        $category0Data = collect($responseData)->firstWhere('category_id', $this->categories[0]->id);
        $category1Data = collect($responseData)->firstWhere('category_id', $this->categories[1]->id);
        $category2Data = collect($responseData)->firstWhere('category_id', $this->categories[2]->id);
        
        $this->assertEquals(3, $category0Data['task_count']);
        $this->assertEquals(2, $category1Data['task_count']);
        $this->assertEquals(1, $category2Data['task_count']);
    }

    /** @test */
    public function it_returns_tasks_by_priority()
    {
        // Create tasks with different priorities
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[0]->id,
            'priority' => 1 // Low
        ]);
        
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[1]->id,
            'priority' => 2 // Medium
        ]);
        
        Task::factory()->count(1)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[2]->id,
            'priority' => 3 // High
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/stats/by-priority');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'priority',
                        'priority_label',
                        'task_count'
                    ]
                ]
            ]);
        
        // Check the counts
        $responseData = $response->json('data');
        $lowPriorityData = collect($responseData)->firstWhere('priority', 1);
        $mediumPriorityData = collect($responseData)->firstWhere('priority', 2);
        $highPriorityData = collect($responseData)->firstWhere('priority', 3);
        
        $this->assertEquals(2, $lowPriorityData['task_count']);
        $this->assertEquals(3, $mediumPriorityData['task_count']);
        $this->assertEquals(1, $highPriorityData['task_count']);
    }

    /** @test */
    public function it_returns_tasks_by_date()
    {
        // Create tasks with different due dates
        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        $nextWeek = now()->addWeek()->format('Y-m-d');
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[0]->id,
            'due_date' => $today
        ]);
        
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[1]->id,
            'due_date' => $tomorrow
        ]);
        
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[2]->id,
            'due_date' => $nextWeek
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/stats/by-date');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'date',
                        'task_count'
                    ]
                ]
            ]);
        
        // Check specific dates
        $responseData = $response->json('data');
        $todayData = collect($responseData)->firstWhere('date', $today);
        $tomorrowData = collect($responseData)->firstWhere('date', $tomorrow);
        $nextWeekData = collect($responseData)->firstWhere('date', $nextWeek);
        
        $this->assertEquals(1, $todayData['task_count']);
        $this->assertEquals(2, $tomorrowData['task_count']);
        $this->assertEquals(3, $nextWeekData['task_count']);
    }

    /** @test */
    public function it_returns_only_user_tasks_in_stats()
    {
        // Create tasks for the current user
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[0]->id
        ]);
        
        // Create tasks for another user
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        Task::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id
        ]);
        
        // Test all endpoints to ensure they only return the current user's data
        $endpoints = [
            '/api/stats/overview',
            '/api/stats/completion-rate',
            '/api/stats/by-category',
            '/api/stats/by-priority',
            '/api/stats/by-date'
        ];
        
        foreach ($endpoints as $endpoint) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->getJson($endpoint);
            
            $response->assertStatus(200);
            
            // For overview specifically, check the total tasks
            if ($endpoint === '/api/stats/overview') {
                $response->assertJson([
                    'data' => [
                        'total_tasks' => 3
                    ]
                ]);
            }
        }
    }

    /** @test */
    public function it_returns_task_completion_time_statistics()
    {
        // Create completed tasks with different completion times
        // Task completed quickly (1 day)
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[0]->id,
            'completed' => true,
            'created_at' => now()->subDays(5),
            'completed_at' => now()->subDays(4)
        ]);
        
        // Task completed in medium time (3 days)
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[1]->id,
            'completed' => true,
            'created_at' => now()->subDays(7),
            'completed_at' => now()->subDays(4)
        ]);
        
        // Task completed slowly (5 days)
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->categories[2]->id,
            'completed' => true,
            'created_at' => now()->subDays(10),
            'completed_at' => now()->subDays(5)
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/stats/completion-time');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'average_days',
                    'fastest_completion',
                    'slowest_completion',
                    'total_completed_tasks'
                ]
            ]);
        
        $responseData = $response->json('data');
        
        // The average should be 3 days
        $this->assertEquals(3, $responseData['average_days']);
        $this->assertEquals(1, $responseData['fastest_completion']);
        $this->assertEquals(5, $responseData['slowest_completion']);
        $this->assertEquals(3, $responseData['total_completed_tasks']);
    }
} 