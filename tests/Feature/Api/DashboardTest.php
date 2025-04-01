<?php

namespace Tests\Feature\Api;

use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class DashboardTest extends TestCase
{
    use WithFaker;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        
        // Refresh database for SQLite compatibility
        Artisan::call('migrate:fresh');
        
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function can_get_dashboard_data()
    {
        // Create some tasks
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);
        
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => true,
        ]);
        
        // Create some categories
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);
        
        // Assign a task to the category
        Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'photo_url',
                    ],
                    'tasks' => [
                        'total',
                        'completed',
                        'incomplete',
                        'completion_rate',
                        'due_today',
                        'overdue',
                        'upcoming',
                        'by_priority',
                    ],
                    'categories' => [
                        'total',
                        'with_tasks',
                        'stats',
                    ],
                    'recent_tasks',
                    'upcoming_tasks',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'email' => $this->user->email,
                    ],
                ],
            ]);
            
        // Validate the response data with more specific assertions
        $data = $response->json('data');
        $this->assertEquals(6, $data['tasks']['total']);
        $this->assertIsNumeric($data['tasks']['completion_rate']);
    }

    /** @test */
    public function cannot_access_dashboard_when_unauthenticated()
    {
        // Don't authenticate the user for this test
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(401);
    }
} 