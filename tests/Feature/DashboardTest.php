<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_api_returns_dashboard_data(): void
    {
        // Create a user with tasks
        $user = User::factory()->create();

        // Create categories
        $workCategory = Category::factory()->work()->create(['user_id' => $user->id]);
        $personalCategory = Category::factory()->personal()->create(['user_id' => $user->id]);

        // Create some tasks
        Task::factory()
            ->count(3)
            ->create([
                'user_id' => $user->id,
                'category_id' => $workCategory->id,
            ]);

        Task::factory()
            ->count(2)
            ->completed()
            ->create([
                'user_id' => $user->id,
                'category_id' => $personalCategory->id,
            ]);

        Task::factory()
            ->dueToday()
            ->create([
                'user_id' => $user->id,
                'category_id' => $workCategory->id,
            ]);

        // Act as the user and make the request
        $response = $this->actingAs($user)
            ->getJson('/api/dashboard');

        // Assert response is successful
        $response->assertStatus(200);

        // Assert response structure matching the actual API format
        $response->assertJsonStructure([
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

        // Get the actual data
        $stats = $response->json('data.stats');
        $categories = $response->json('data.categories');

        // Basic assertions that verify the data is reasonable
        $this->assertGreaterThan(0, $stats['total'], 'There should be some tasks');
        $this->assertGreaterThanOrEqual(0, $stats['completed'], 'Completed tasks should be non-negative');
        $this->assertLessThanOrEqual($stats['total'], $stats['completed'], 'Completed tasks should not exceed total');
        $this->assertIsNumeric($stats['completion_rate'], 'Completion rate should be numeric');
        $this->assertLessThanOrEqual(100, $stats['completion_rate'], 'Completion rate should be <= 100');
        $this->assertGreaterThanOrEqual(0, $stats['completion_rate'], 'Completion rate should be >= 0');

        // Verify categories data
        $this->assertNotEmpty($categories, 'Categories should not be empty');
        $categoryNames = array_column($categories, 'name');
        $this->assertContains('Work', $categoryNames, 'The Work category should be in the response');
        $this->assertContains('Personal', $categoryNames, 'The Personal category should be in the response');
    }

    public function test_guest_cannot_access_dashboard_api(): void
    {
        // Make a request without authentication
        $response = $this->getJson('/api/dashboard');

        // Assert unauthorized response
        $response->assertStatus(401);
    }

    public function test_user_only_sees_their_own_tasks(): void
    {
        // Create two users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create categories for each user
        $user1Category = Category::factory()->create(['user_id' => $user1->id]);
        $user2Category = Category::factory()->create(['user_id' => $user2->id]);

        // Create tasks for user1
        Task::factory()
            ->count(3)
            ->create([
                'user_id' => $user1->id,
                'category_id' => $user1Category->id,
            ]);

        // Create tasks for user2
        Task::factory()
            ->count(5)
            ->create([
                'user_id' => $user2->id,
                'category_id' => $user2Category->id,
            ]);

        // Act as user1 and make the request
        $response = $this->actingAs($user1)
            ->getJson('/api/dashboard');

        // Assert user1 only sees their own tasks
        $response->assertJsonPath('data.stats.total', 3);

        // Now act as user2
        $response = $this->actingAs($user2)
            ->getJson('/api/dashboard');

        // Assert user2 only sees their own tasks
        $response->assertJsonPath('data.stats.total', 5);
    }
}
