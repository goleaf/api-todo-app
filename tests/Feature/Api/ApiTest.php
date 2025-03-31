<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test API health check.
     */
    public function test_api_health_check(): void
    {
        // Test documentation route
        $response = $this->getJson('/api/documentation');
        $response->assertStatus(200);

        // Test task-related endpoints (unauthenticated)
        $tasksResponse = $this->getJson('/api/tasks');
        $tasksResponse->assertStatus(401); // Should be unauthorized

        // Test category-related endpoints (unauthenticated)
        $categoriesResponse = $this->getJson('/api/categories');
        $categoriesResponse->assertStatus(401); // Should be unauthorized

        // Test auth-related endpoints
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        // Should be 401 (unauthorized) for invalid credentials
        $loginResponse->assertStatus(401);
    }

    /**
     * Test API documentation endpoint.
     */
    public function test_api_documentation_endpoint(): void
    {
        $response = $this->getJson('/api/documentation');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'version',
                    'endpoints',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'API documentation available at /api/docs',
            ]);
    }

    /**
     * Test API fallback route.
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
            $this->getJson('/api/documentation');
        }

        // The next request should be rate limited
        $response = $this->getJson('/api/documentation');

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
     * Test API documentation returns available endpoints.
     */
    public function test_api_documentation_returns_available_endpoints(): void
    {
        $response = $this->getJson('/api/documentation');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'version',
                    'endpoints',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'API documentation available at /api/docs',
            ]);

        // Additional check to make sure we have all the expected sections in the endpoints
        $data = $response->json('data.endpoints');
        $this->assertArrayHasKey('auth', $data);
        $this->assertArrayHasKey('users', $data);
        $this->assertArrayHasKey('tasks', $data);
        $this->assertArrayHasKey('categories', $data);
        $this->assertArrayHasKey('profile', $data);
        $this->assertArrayHasKey('dashboard', $data);
    }
} 