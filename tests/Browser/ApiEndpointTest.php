<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ApiEndpointTest extends DuskTestCase
{
    use DatabaseMigrations;

    private string $apiToken;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and generate token
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Get token through login
        $response = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $responseBody = json_decode($response->getContent(), true);
        $this->apiToken = $responseBody['data']['token'] ?? '';
    }

    /**
     * Test authentication endpoints.
     */
    public function test_authentication_endpoints(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/api/documentation')
                ->assertSee('API documentation');

            // Test user registration (through API client call)
            $response = $this->post('/api/register', [
                'name' => 'New User',
                'email' => 'new@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertStatus(201)
                ->assertJson(['success' => true]);

            // Test invalid login
            $response = $this->post('/api/login', [
                'email' => 'wrong@example.com',
                'password' => 'wrongpassword',
            ]);

            $response->assertStatus(401)
                ->assertJson(['success' => false]);
        });
    }

    /**
     * Test task endpoints.
     */
    public function test_task_endpoints(): void
    {
        $this->browse(function (Browser $browser) {
            // Create a task
            $response = $this->withHeaders([
                'Authorization' => 'Bearer '.$this->apiToken,
                'Accept' => 'application/json',
            ])->post('/api/tasks', [
                'title' => 'Test Task',
                'description' => 'This is a test task',
                'priority' => 2,
                'due_date' => now()->format('Y-m-d'),
            ]);

            $response->assertStatus(201)
                ->assertJson(['success' => true]);

            // Get the task ID from the response
            $responseBody = json_decode($response->getContent(), true);
            $taskId = $responseBody['data']['id'] ?? null;

            // Get the task
            if ($taskId) {
                $response = $this->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiToken,
                    'Accept' => 'application/json',
                ])->get('/api/tasks/'.$taskId);

                $response->assertStatus(200)
                    ->assertJson([
                        'success' => true,
                        'data' => [
                            'id' => $taskId,
                            'title' => 'Test Task',
                        ],
                    ]);

                // Update the task
                $response = $this->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiToken,
                    'Accept' => 'application/json',
                ])->put('/api/tasks/'.$taskId, [
                    'title' => 'Updated Task',
                    'description' => 'This task has been updated',
                    'priority' => 3,
                ]);

                $response->assertStatus(200)
                    ->assertJson([
                        'success' => true,
                        'data' => [
                            'title' => 'Updated Task',
                            'priority' => 3,
                        ],
                    ]);

                // Toggle task completion
                $response = $this->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiToken,
                    'Accept' => 'application/json',
                ])->patch('/api/tasks/'.$taskId.'/toggle');

                $response->assertStatus(200)
                    ->assertJson(['success' => true]);

                // Delete the task
                $response = $this->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiToken,
                    'Accept' => 'application/json',
                ])->delete('/api/tasks/'.$taskId);

                $response->assertStatus(204);
            }
        });
    }

    /**
     * Test category endpoints.
     */
    public function test_category_endpoints(): void
    {
        $this->browse(function (Browser $browser) {
            // Create a category
            $response = $this->withHeaders([
                'Authorization' => 'Bearer '.$this->apiToken,
                'Accept' => 'application/json',
            ])->post('/api/categories', [
                'name' => 'Test Category',
                'color' => '#FF5733',
                'icon' => 'folder',
            ]);

            $response->assertStatus(201)
                ->assertJson(['success' => true]);

            // Get the category ID from the response
            $responseBody = json_decode($response->getContent(), true);
            $categoryId = $responseBody['data']['id'] ?? null;

            // Get the category
            if ($categoryId) {
                $response = $this->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiToken,
                    'Accept' => 'application/json',
                ])->get('/api/categories/'.$categoryId);

                $response->assertStatus(200)
                    ->assertJson([
                        'success' => true,
                        'data' => [
                            'id' => $categoryId,
                            'name' => 'Test Category',
                        ],
                    ]);

                // Update the category
                $response = $this->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiToken,
                    'Accept' => 'application/json',
                ])->put('/api/categories/'.$categoryId, [
                    'name' => 'Updated Category',
                    'color' => '#33FF57',
                    'icon' => 'edit',
                ]);

                $response->assertStatus(200)
                    ->assertJson([
                        'success' => true,
                        'data' => [
                            'name' => 'Updated Category',
                            'color' => '#33FF57',
                        ],
                    ]);

                // Get task counts by category
                $response = $this->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiToken,
                    'Accept' => 'application/json',
                ])->get('/api/categories/task-counts');

                $response->assertStatus(200)
                    ->assertJson(['success' => true]);

                // Delete the category
                $response = $this->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiToken,
                    'Accept' => 'application/json',
                ])->delete('/api/categories/'.$categoryId);

                $response->assertStatus(204);
            }
        });
    }

    /**
     * Test dashboard endpoint.
     */
    public function test_dashboard_endpoint(): void
    {
        $this->browse(function (Browser $browser) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer '.$this->apiToken,
                'Accept' => 'application/json',
            ])->get('/api/dashboard');

            $response->assertStatus(200)
                ->assertJson(['success' => true]);
        });
    }

    /**
     * Test profile endpoints.
     */
    public function test_profile_endpoints(): void
    {
        $this->browse(function (Browser $browser) {
            // Get profile
            $response = $this->withHeaders([
                'Authorization' => 'Bearer '.$this->apiToken,
                'Accept' => 'application/json',
            ])->get('/api/profile');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $this->user->id,
                        'email' => $this->user->email,
                    ],
                ]);

            // Update profile
            $response = $this->withHeaders([
                'Authorization' => 'Bearer '.$this->apiToken,
                'Accept' => 'application/json',
            ])->put('/api/profile', [
                'name' => 'Updated User',
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'name' => 'Updated User',
                    ],
                ]);

            // Update password
            $response = $this->withHeaders([
                'Authorization' => 'Bearer '.$this->apiToken,
                'Accept' => 'application/json',
            ])->put('/api/profile/password', [
                'current_password' => 'password',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

            $response->assertStatus(200)
                ->assertJson(['success' => true]);
        });
    }
}
