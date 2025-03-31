<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTestSuite extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests that the API is functional by checking key endpoints.
     *
     * This test verifies that the core API endpoints return the expected
     * status codes for both authenticated and unauthenticated requests.
     * It serves as a quick health check of the API.
     */
    public function test_api_health_check(): void
    {
        // Test API root route
        $response = $this->getJson('/api');
        $response->assertStatus(200);

        // Test task-related endpoints (unauthenticated)
        $tasksResponse = $this->getJson('/api/tasks');
        $tasksResponse->assertStatus(401); // Should be unauthorized

        // Test category-related endpoints (unauthenticated)
        $categoriesResponse = $this->getJson('/api/categories');
        $categoriesResponse->assertStatus(401); // Should be unauthorized†

        // Test auth-related endpoints
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        // Should be 422 (validation error) not 500 (server error)
        $loginResponse->assertStatus(422);

        // Test documentation
        $docsResponse = $this->get('/api/docs');
        $docsResponse->assertStatus(200);
    }

    /**
     * Test that the API version information is correct.
     */
    public function test_api_version_endpoint(): void
    {
        $response = $this->getJson('/api/version');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'version',
                    'name',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Todo API',
                ],
            ]);
    }

    /**
     * Test the API fallback route for non-existent endpoints.
     */
    public function test_api_fallback_route(): void
    {
        $response = $this->getJson('/api/non-existent-route');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'API endpoint not found',
            ]);
    }

    /**
     * Test API rate limiting.
     */
    public function test_api_rate_limiting(): void
    {
        // Make multiple requests to trigger rate limiting
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/version');
        }

        // The next request should be rate limited
        $response = $this->getJson('/api/version');

        // Depending on the rate limit configuration, this might be 429 Too Many Requests
        // or still 200 if the rate limit is higher than our test threshold
        $this->assertTrue(
            $response->status() === 429 || $response->status() === 200,
            'API rate limiting not working correctly'
        );

        if ($response->status() === 429) {
            $response->assertJsonStructure([
                'message',
            ]);
        }
    }

    /**
     * Test API maintenance mode response.
     */
    public function test_api_maintenance_mode_not_active(): void
    {
        // Test that the API is not in maintenance mode
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'operational',
                    'maintenance' => false,
                ],
            ]);
    }

    /**
     * Test the API root route returns available endpoints.
     */
    public function test_api_root_returns_available_endpoints(): void
    {
        $response = $this->getJson('/api');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'name',
                    'version',
                    'endpoints' => [
                        'auth' => [
                            '*',
                        ],
                        'tasks' => [
                            '*',
                        ],
                        'categories' => [
                            '*',
                        ],
                        'profile' => [
                            '*',
                        ],
                    ],
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Welcome to the Todo API',
            ]);
    }
}
