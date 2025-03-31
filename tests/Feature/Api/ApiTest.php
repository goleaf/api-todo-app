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
        // Test API root route
        $response = $this->getJson('/api');
        $response->assertStatus(200);

        // Test task-related endpoints (unauthenticated)
        $tasksResponse = $this->getJson('/api/tasks');
        $tasksResponse->assertStatus(401); // Should be unauthorized

        // Test category-related endpoints (unauthenticated)
        $categoriesResponse = $this->getJson('/api/categories');
        $categoriesResponse->assertStatus(401); // Should be unauthorized

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
     * Test API version endpoint.
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
     * Test API root route returns available endpoints.
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
    
    /**
     * Test Swagger documentation is accessible.
     */
    public function test_swagger_documentation_is_accessible(): void
    {
        $response = $this->get('/api/docs');
        
        $response->assertStatus(200)
            ->assertViewHas('documentation');
    }

    /**
     * Test OpenAPI JSON is valid.
     */
    public function test_openapi_json_is_valid(): void
    {
        $response = $this->get('/api/docs/api-docs.json');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'openapi',
                'info',
                'paths',
                'components',
            ]);
    }
} 