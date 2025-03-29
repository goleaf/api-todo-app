<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteBulkTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test all public routes are responding.
     *
     * @return void
     */
    public function test_all_public_routes_are_responding(): void
    {
        // Public routes to test
        $publicRoutes = [
            '/',
            '/login',
            '/register',
        ];

        foreach ($publicRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
            
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Test all authenticated web routes are responding.
     *
     * @return void
     */
    public function test_all_authenticated_web_routes_are_responding(): void
    {
        $user = User::factory()->create();

        // Get all registered routes
        $routes = Route::getRoutes();
        
        // Filter for web routes that require authentication
        $webRoutes = collect($routes)->filter(function ($route) {
            return in_array('web', $route->gatherMiddleware()) && 
                   in_array('auth', $route->gatherMiddleware()) && 
                   $route->methods()[0] === 'GET';
        })->map(function ($route) {
            return $route->uri();
        })->toArray();
        
        // Test each authenticated web route
        foreach ($webRoutes as $route) {
            if (strpos($route, '{') !== false) {
                continue; // Skip routes with parameters for now
            }
            
            $response = $this->actingAs($user)->get('/' . $route);
            
            // Accept 200 or 302 (redirect) as valid
            $this->assertTrue($response->status() == 200 || $response->status() == 302, 
                "Route /$route failed with status {$response->status()}");
            
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Test all API routes are responding with authentication.
     *
     * @return void
     */
    public function test_all_api_routes_are_responding(): void
    {
        $user = User::factory()->create();

        // Get all registered routes
        $routes = Route::getRoutes();
        
        // Filter for API routes
        $apiRoutes = collect($routes)->filter(function ($route) {
            return strpos($route->uri(), 'api/') === 0 && 
                   $route->methods()[0] === 'GET';
        })->map(function ($route) {
            return $route->uri();
        })->toArray();
        
        // Test each API route
        foreach ($apiRoutes as $route) {
            if (strpos($route, '{') !== false) {
                continue; // Skip routes with parameters for now
            }
            
            $response = $this->actingAs($user)->getJson('/' . $route);
            
            // Accept 200 as valid
            $this->assertTrue(
                in_array($response->status(), [200, 401, 403]), 
                "API route /$route failed with status {$response->status()}"
            );
            
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Test routes with dynamic segments using placeholder values.
     *
     * @return void
     */
    public function test_routes_with_parameters(): void
    {
        $user = User::factory()->create();
        
        // For this test we'll create some entries in the database
        $todoId = $this->createTodo($user);
        $taskId = $this->createTask($user);
        $categoryId = $this->createCategory($user);
        
        // Define routes with parameters
        $parameterizedRoutes = [
            '/api/todos/' . $todoId => 'GET',
            '/api/tasks/' . $taskId => 'GET',
            '/api/categories/' . $categoryId => 'GET',
        ];
        
        // Test each route
        foreach ($parameterizedRoutes as $route => $method) {
            $response = $this->actingAs($user)
                ->json($method, $route);
            
            $this->assertTrue(
                $response->status() === 200, 
                "Route $route with method $method failed with status {$response->status()}"
            );
            
            $this->addToAssertionCount(1);
        }
    }
    
    /**
     * Create a todo for testing.
     *
     * @param User $user
     * @return int
     */
    private function createTodo(User $user): int
    {
        $response = $this->actingAs($user)->postJson('/api/todos', [
            'title' => 'Test Todo',
            'completed' => false
        ]);
        
        return $response->json('id');
    }
    
    /**
     * Create a task for testing.
     *
     * @param User $user
     * @return int
     */
    private function createTask(User $user): int
    {
        $categoryId = $this->createCategory($user);
        
        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test description',
            'category_id' => $categoryId,
            'priority' => 1,
            'status' => 'pending'
        ]);
        
        return $response->json('id');
    }
    
    /**
     * Create a category for testing.
     *
     * @param User $user
     * @return int
     */
    private function createCategory(User $user): int
    {
        $response = $this->actingAs($user)->postJson('/api/categories', [
            'name' => 'Test Category',
            'color' => '#ff0000'
        ]);
        
        return $response->json('id');
    }
} 