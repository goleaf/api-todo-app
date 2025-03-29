<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Todo;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the main welcome page.
     */
    public function test_welcome_page_loads(): void
    {
        // Don't check for exact status - just ensure it doesn't throw exception
        $response = $this->get('/');
        $this->assertTrue($response->status() < 500, 'Welcome page should not cause server error');
    }

    /**
     * Test API routes existence.
     */
    public function test_api_routes_exist(): void
    {
        // Get all API routes
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return strpos($route->uri(), 'api/') === 0;
        })->map(function ($route) {
            return [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
            ];
        })->values()->all();
        
        // Assert we have various API routes
        $this->assertNotEmpty($routes, 'API routes should exist');
        
        // Check for essential route patterns
        $uris = collect($routes)->pluck('uri')->all();
        $hasLoginRoute = false;
        $hasUserRoute = false;
        $hasTodosRoute = false;
        
        foreach ($uris as $uri) {
            if (strpos($uri, 'api/login') !== false) {
                $hasLoginRoute = true;
            }
            if (strpos($uri, 'api/user') !== false) {
                $hasUserRoute = true;
            }
            if (strpos($uri, 'api/todos') !== false) {
                $hasTodosRoute = true;
            }
        }
        
        $this->assertTrue($hasLoginRoute, 'Login API route should exist');
        $this->assertTrue($hasUserRoute, 'User API route should exist');
        $this->assertTrue($hasTodosRoute, 'Todos API route should exist');
    }

    /**
     * Test authentication API endpoints.
     */
    public function test_auth_endpoints_return_expected_structure(): void
    {
        // Test register API with validation errors
        $response = $this->postJson('/api/register', []);
        $this->assertEquals(422, $response->status(), 'Register should require validation');
        $response->assertJsonStructure(['message', 'errors']);
        
        // Test login API with validation errors
        $response = $this->postJson('/api/login', []);
        $this->assertEquals(422, $response->status(), 'Login should require validation');
        $response->assertJsonStructure(['message', 'errors']);
    }
    
    /**
     * Test protected routes require authentication.
     */
    public function test_protected_routes_require_authentication(): void
    {
        $protectedEndpoints = [
            '/api/user',
            '/api/todos',
            '/api/tasks',
            '/api/categories',
        ];
        
        foreach ($protectedEndpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            
            // Should return 401 (Unauthorized)
            $this->assertEquals(
                401, 
                $response->status(), 
                "Unauthenticated request to {$endpoint} should return 401"
            );
        }
    }
    
    /**
     * Test authenticated user API access.
     */
    public function test_authenticated_user_can_access_user_endpoint(): void
    {
        // Create a user
        $user = User::factory()->create();
        
        // Test access to user endpoint
        $response = $this->actingAs($user)->getJson('/api/user');
        
        // Check for 200 response with correct structure
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'name', 'email']);
    }

    /**
     * Test user registration works.
     */
    public function test_user_registration(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        
        // Allow for 201 (created) or 200 (success)
        $this->assertTrue(
            in_array($response->status(), [200, 201]), 
            "Registration should return 200 or 201, got {$response->status()}"
        );
    }

    /**
     * Test SPA route handling.
     */
    public function test_spa_route_handling(): void
    {
        // Just check the SPA doesn't throw a server error
        $response = $this->get('/any-frontend-route');
        $this->assertTrue($response->status() < 500, 'SPA route should not cause server error');
    }
    
    /**
     * Test authenticated user can access API resources.
     */
    public function test_authenticated_user_can_access_api_resources(): void
    {
        $user = User::factory()->create();
        
        $apiEndpoints = [
            '/api/user',
            '/api/todos',
            '/api/tasks',
            '/api/categories',
        ];
        
        foreach ($apiEndpoints as $endpoint) {
            $response = $this->actingAs($user)->getJson($endpoint);
            
            // Accept 200 (success) or 404 (not found) but not 401 (unauthorized)
            $this->assertNotEquals(
                401, 
                $response->status(), 
                "Authenticated user should not get 401 for {$endpoint}"
            );
        }
    }
    
    /**
     * Test unauthenticated user cannot access protected API resources.
     */
    public function test_unauthenticated_user_cannot_access_protected_api(): void
    {
        $protectedEndpoints = [
            '/api/user',
            '/api/todos',
            '/api/tasks',
            '/api/categories',
        ];
        
        foreach ($protectedEndpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            
            // Should be either 401 (unauthorized) or 403 (forbidden)
            $this->assertTrue(
                in_array($response->status(), [401, 403]), 
                "Unauthenticated user should get 401 or 403 for {$endpoint}, got {$response->status()}"
            );
        }
    }

    /**
     * Test public routes.
     */
    public function test_public_routes_return_successful_response(): void
    {
        // Test welcome page
        $response = $this->get(route('welcome'));
        $response->assertStatus(200);

        // Test login page
        $response = $this->get('/login');
        $response->assertStatus(200);

        // Test register page
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Test authenticated web routes.
     */
    public function test_authenticated_web_routes(): void
    {
        $user = User::factory()->create();

        // Test dashboard page
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);

        // Test tasks index page
        $response = $this->actingAs($user)->get(route('tasks.index'));
        $response->assertStatus(200);

        // Test profile page
        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertStatus(200);
        
        // Test todos index page
        $response = $this->actingAs($user)->get(route('todos.index'));
        $response->assertStatus(200);
    }

    /**
     * Test authenticated API routes.
     */
    public function test_authenticated_api_routes(): void
    {
        $user = User::factory()->create();
        
        // Test user info endpoint
        $response = $this->actingAs($user)->getJson('/api/user');
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'name', 'email']);

        // Test todos index
        $response = $this->actingAs($user)->getJson('/api/todos');
        $response->assertStatus(200);
        
        // Test categories index
        $response = $this->actingAs($user)->getJson('/api/categories');
        $response->assertStatus(200);
        
        // Test tasks index
        $response = $this->actingAs($user)->getJson('/api/tasks');
        $response->assertStatus(200);
    }

    /**
     * Test CRUD operations on Todo resource.
     */
    public function test_todo_crud_operations(): void
    {
        $user = User::factory()->create();
        
        // Create test
        $response = $this->actingAs($user)->postJson('/api/todos', [
            'title' => 'Test Todo',
            'completed' => false
        ]);
        $response->assertStatus(201);
        $todoId = $response->json('id');
        
        // Read test
        $response = $this->actingAs($user)->getJson("/api/todos/{$todoId}");
        $response->assertStatus(200);
        $response->assertJson(['title' => 'Test Todo']);
        
        // Update test
        $response = $this->actingAs($user)->putJson("/api/todos/{$todoId}", [
            'title' => 'Updated Todo',
            'completed' => true
        ]);
        $response->assertStatus(200);
        
        // Delete test
        $response = $this->actingAs($user)->deleteJson("/api/todos/{$todoId}");
        $response->assertStatus(200);
    }
    
    /**
     * Test CRUD operations on Task resource.
     */
    public function test_task_crud_operations(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        // Create test
        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test description',
            'category_id' => $category->id,
            'priority' => 1,
            'status' => 'pending'
        ]);
        $response->assertStatus(201);
        $taskId = $response->json('id');
        
        // Read test
        $response = $this->actingAs($user)->getJson("/api/tasks/{$taskId}");
        $response->assertStatus(200);
        $response->assertJson(['title' => 'Test Task']);
        
        // Update test
        $response = $this->actingAs($user)->putJson("/api/tasks/{$taskId}", [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'priority' => 2
        ]);
        $response->assertStatus(200);
        
        // Delete test
        $response = $this->actingAs($user)->deleteJson("/api/tasks/{$taskId}");
        $response->assertStatus(200);
    }
    
    /**
     * Test CRUD operations on Category resource.
     */
    public function test_category_crud_operations(): void
    {
        $user = User::factory()->create();
        
        // Create test
        $response = $this->actingAs($user)->postJson('/api/categories', [
            'name' => 'Test Category',
            'color' => '#ff0000'
        ]);
        $response->assertStatus(201);
        $categoryId = $response->json('id');
        
        // Read test
        $response = $this->actingAs($user)->getJson("/api/categories/{$categoryId}");
        $response->assertStatus(200);
        $response->assertJson(['name' => 'Test Category']);
        
        // Update test
        $response = $this->actingAs($user)->putJson("/api/categories/{$categoryId}", [
            'name' => 'Updated Category',
            'color' => '#00ff00'
        ]);
        $response->assertStatus(200);
        
        // Delete test
        $response = $this->actingAs($user)->deleteJson("/api/categories/{$categoryId}");
        $response->assertStatus(200);
    }
    
    /**
     * Test task search endpoint.
     */
    public function test_task_search_endpoint(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Find this specific task'
        ]);
        
        $response = $this->actingAs($user)->getJson('/api/tasks/search?query=specific');
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Find this specific task']);
    }
    
    /**
     * Test catch-all SPA route.
     */
    public function test_catch_all_spa_route(): void
    {
        // Test with unauthenticated user (should show welcome page)
        $response = $this->get('/non-existent-page');
        $response->assertStatus(200);
        
        // Test with authenticated user (should show dashboard)
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/non-existent-page');
        $response->assertStatus(200);
    }
} 