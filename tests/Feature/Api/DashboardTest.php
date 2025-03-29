<?php

namespace Tests\Feature\Api;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test_device')->plainTextToken;
    }

    /**
     * Test retrieving dashboard data.
     */
    public function test_user_can_get_dashboard_data(): void
    {
        // Create some tasks with different statuses
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);
        
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'stats' => [
                        'total_tasks',
                        'completed_tasks',
                        'pending_tasks',
                        'completion_rate',
                    ],
                    'recent_tasks' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'completed',
                            'due_date',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                ],
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals(5, $response->json('data.stats.total_tasks'));
        $this->assertEquals(2, $response->json('data.stats.completed_tasks'));
        $this->assertEquals(3, $response->json('data.stats.pending_tasks'));
        $this->assertEquals(40, $response->json('data.stats.completion_rate'));
    }

    /**
     * Test dashboard data with no tasks.
     */
    public function test_dashboard_with_no_tasks(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'stats' => [
                        'total_tasks',
                        'completed_tasks',
                        'pending_tasks',
                        'completion_rate',
                    ],
                    'recent_tasks',
                ],
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals(0, $response->json('data.stats.total_tasks'));
        $this->assertEquals(0, $response->json('data.stats.completed_tasks'));
        $this->assertEquals(0, $response->json('data.stats.pending_tasks'));
        $this->assertEquals(0, $response->json('data.stats.completion_rate'));
        $this->assertEmpty($response->json('data.recent_tasks'));
    }

    /**
     * Test dashboard access requires authentication.
     */
    public function test_dashboard_access_requires_authentication(): void
    {
        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(401);
    }
} 