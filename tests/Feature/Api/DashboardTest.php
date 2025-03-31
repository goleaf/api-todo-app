<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Work',
            'color' => 'blue',
        ]);
    }

    /** @test */
    public function authenticated_user_can_get_dashboard_data()
    {
        // Create tasks for testing
        Task::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => true,
        ]);

        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => false,
        ]);

        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => false,
            'due_date' => Carbon::now()->subDays(2),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'status_code',
                'message',
                'data' => [
                    'stats' => [
                        'total',
                        'completed',
                        'pending',
                        'overdue',
                        'completion_rate',
                    ],
                    'categories',
                    'recentTasks',
                    'upcomingDeadlines',
                    'recentActivity',
                    'completionRateOverTime',
                ],
            ]);

        // Verify counts
        $responseData = $response->json('data');
        $this->assertEquals(10, $responseData['stats']['total']);
        $this->assertEquals(5, $responseData['stats']['completed']);
        $this->assertEquals(5, $responseData['stats']['pending']);
        $this->assertEquals(2, $responseData['stats']['overdue']);
        $this->assertEquals(50, $responseData['stats']['completion_rate']);

        // Verify message
        $this->assertEquals('Dashboard data retrieved successfully', $response->json('message'));
    }

    /** @test */
    public function dashboard_data_includes_categories()
    {
        // Create multiple categories
        $personalCategory = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Personal',
            'color' => 'green',
        ]);

        // Add tasks to categories
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $personalCategory->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/dashboard');

        $response->assertStatus(200);

        // Verify categories are returned
        $categories = $response->json('data.categories');
        $this->assertCount(2, $categories);

        // Categories should be sorted by task count
        $this->assertEquals('Work', $categories[0]['name']);
        $this->assertEquals(3, $categories[0]['tasks_count']);

        $this->assertEquals('Personal', $categories[1]['name']);
        $this->assertEquals(2, $categories[1]['tasks_count']);

        // Check that categories include task counts
        $this->assertArrayHasKey('tasks_count', $categories[0]);
    }

    /** @test */
    public function dashboard_data_includes_recent_activity()
    {
        // Create a task with a specific timestamp for testing
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Test Activity Task',
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);

        // Update the task to generate activity
        $task->title = 'Updated Task Title';
        $task->updated_at = now();
        $task->save();

        // Complete the task
        $task->completed = true;
        $task->completed_at = now();
        $task->save();

        $response = $this->actingAs($this->user)
            ->getJson('/api/dashboard');

        $response->assertStatus(200);

        // Verify recent activity is included
        $activities = $response->json('data.recentActivity');
        $this->assertNotEmpty($activities);

        // Find activity entries
        $createdActivity = collect($activities)->firstWhere('type', 'created');
        $this->assertNotNull($createdActivity);
        $this->assertEquals($task->id, $createdActivity['id']);

        $completedActivity = collect($activities)->firstWhere('type', 'completed');
        $this->assertNotNull($completedActivity);
        $this->assertEquals($task->id, $completedActivity['id']);
    }

    /** @test */
    public function dashboard_data_includes_completion_rate_over_time()
    {
        // Create tasks on different days
        $yesterday = Carbon::yesterday();
        $twoDaysAgo = Carbon::now()->subDays(2);

        // Tasks from two days ago (2 total, 1 completed)
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => true,
            'completed_at' => $twoDaysAgo,
            'created_at' => $twoDaysAgo,
            'updated_at' => $twoDaysAgo,
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => false,
            'created_at' => $twoDaysAgo,
            'updated_at' => $twoDaysAgo,
        ]);

        // Tasks from yesterday (3 total, 2 completed)
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => true,
            'completed_at' => $yesterday,
            'created_at' => $yesterday,
            'updated_at' => $yesterday,
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'completed' => false,
            'created_at' => $yesterday,
            'updated_at' => $yesterday,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/dashboard');

        $response->assertStatus(200);

        // Verify completion rate over time data is included
        $completionRates = $response->json('data.completionRateOverTime');
        $this->assertNotEmpty($completionRates);

        // We should have 7 days of data
        $this->assertCount(7, $completionRates);

        // Find the relevant days in the result
        $twoDaysAgoDate = $twoDaysAgo->format('Y-m-d');
        $yesterdayDate = $yesterday->format('Y-m-d');

        $twoDaysAgoRate = collect($completionRates)->firstWhere('date', $twoDaysAgoDate);
        $yesterdayRate = collect($completionRates)->firstWhere('date', $yesterdayDate);

        // Two days ago: 1 of 2 tasks completed = 50%
        $this->assertEquals(50, $twoDaysAgoRate['completion_rate']);

        // Yesterday: 3 of 5 tasks completed = 60%
        $this->assertEquals(60, $yesterdayRate['completion_rate']);
    }

    /** @test */
    public function dashboard_shows_upcoming_tasks_correctly()
    {
        // Create upcoming tasks with different due dates
        $tomorrow = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'due_date' => now()->addDay(),
            'completed' => false,
            'title' => 'Tomorrow task',
        ]);

        $nextWeek = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'due_date' => now()->addWeek(),
            'completed' => false,
            'title' => 'Next week task',
        ]);

        // Completed task with future due date (should not appear in upcoming)
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'due_date' => now()->addDays(3),
            'completed' => true,
            'title' => 'Completed future task',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/dashboard');

        $response->assertStatus(200);

        $upcomingDeadlines = $response->json('data.upcomingDeadlines');

        // Should only include incomplete tasks with future due dates
        $this->assertCount(2, $upcomingDeadlines);

        // Tasks should be ordered by due date (ascending)
        $this->assertEquals('Tomorrow task', $upcomingDeadlines[0]['title']);
        $this->assertEquals('Next week task', $upcomingDeadlines[1]['title']);
    }

    /** @test */
    public function dashboard_with_no_tasks_returns_empty_data()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'status_code',
                'message',
                'data' => [
                    'stats' => [
                        'total',
                        'completed',
                        'pending',
                        'overdue',
                        'completion_rate',
                    ],
                    'categories',
                    'recentTasks',
                    'upcomingDeadlines',
                ],
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals(0, $response->json('data.stats.total'));
        $this->assertEquals(0, $response->json('data.stats.completed'));
        $this->assertEquals(0, $response->json('data.stats.pending'));
        $this->assertEquals(0, $response->json('data.stats.overdue'));
        $this->assertEquals(0, $response->json('data.stats.completion_rate'));
        $this->assertEmpty($response->json('data.categories'));
        $this->assertEmpty($response->json('data.recentTasks'));
        $this->assertEmpty($response->json('data.upcomingDeadlines'));
    }

    /** @test */
    public function dashboard_only_includes_user_data()
    {
        // Create a second user
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        // Create tasks for main user
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        // Create tasks for other user
        Task::factory()->count(5)->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
        ]);

        // Test main user's dashboard
        $response = $this->actingAs($this->user)
            ->getJson('/api/dashboard');

        $response->assertStatus(200);
        $this->assertEquals(3, $response->json('data.stats.total'));
        $this->assertCount(1, $response->json('data.categories'));

        // Test other user's dashboard
        $response = $this->actingAs($otherUser)
            ->getJson('/api/dashboard');

        $response->assertStatus(200);
        $this->assertEquals(5, $response->json('data.stats.total'));
    }

    /** @test */
    public function unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(401);
    }
}
