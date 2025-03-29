<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test all auth routes are accessible.
     */
    public function test_auth_routes_are_accessible(): void
    {
        // Test login page
        $response = $this->get('/login');
        $response->assertStatus(200);

        // Test register page
        $response = $this->get('/register');
        $response->assertStatus(200);

        // Test API login route
        $response = $this->postJson('/api/login', []);
        $response->assertStatus(422); // Validation error is expected, but the route is accessible

        // Test API register route
        $response = $this->postJson('/api/register', []);
        $response->assertStatus(422); // Validation error is expected, but the route is accessible
    }

    /**
     * Test API login endpoint validates required fields.
     */
    public function test_api_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test API register endpoint validates required fields.
     */
    public function test_api_register_validates_required_fields(): void
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test API login with valid credentials returns token.
     */
    public function test_api_login_with_valid_credentials_returns_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }

    /**
     * Test API login with invalid credentials returns error.
     */
    public function test_api_login_with_invalid_credentials_returns_error(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test API register creates a new user and returns token.
     */
    public function test_api_register_creates_new_user_and_returns_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['token', 'user']);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
        ]);
    }

    /**
     * Test API register with existing email returns validation error.
     */
    public function test_api_register_with_existing_email_returns_validation_error(): void
    {
        // Create a user with a specific email
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // Try to register with the same email
        $response = $this->postJson('/api/register', [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test API register with password mismatch returns validation error.
     */
    public function test_api_register_with_password_mismatch_returns_validation_error(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test API user endpoint requires authentication.
     */
    public function test_api_user_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /**
     * Test API user endpoint returns authenticated user data.
     */
    public function test_api_user_endpoint_returns_authenticated_user_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    /**
     * Test protected routes require authentication.
     */
    public function test_protected_routes_require_authentication(): void
    {
        // Test a few protected routes
        $protectedRoutes = [
            '/api/todos',
            '/api/categories',
            '/api/tasks'
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->getJson($route);
            $response->assertStatus(401);
        }
    }

    /**
     * Test authenticated users can access protected routes.
     */
    public function test_authenticated_users_can_access_protected_routes(): void
    {
        $user = User::factory()->create();

        // Test a few protected routes
        $protectedRoutes = [
            '/api/todos',
            '/api/categories',
            '/api/tasks'
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->actingAs($user)
                ->getJson($route);
            
            // The route might return 200 or 404 depending on if data exists,
            // but it should not return 401 (Unauthorized)
            $this->assertNotEquals(401, $response->status());
        }
    }
} 