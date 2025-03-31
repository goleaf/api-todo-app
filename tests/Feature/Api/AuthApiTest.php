<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test user registration.
     */
    public function test_user_can_register(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                    ],
                    'token',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'User registered successfully.',
                'data' => [
                    'user' => [
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    /**
     * Test user login.
     */
    public function test_user_can_login(): void
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'token',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'messages.auth.logged_in',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                    ],
                ],
            ]);

        // Ensure the response has a token
        $this->assertNotEmpty($response->json('data.token'));
    }

    /**
     * Test login with invalid credentials.
     */
    public function test_login_with_invalid_credentials(): void
    {
        // Create a user
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'WrongPassword123!',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'These credentials do not match our records.',
            ]);
    }

    /**
     * Test user logout.
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        // Create a token for the user
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'messages.auth.logged_out',
            ]);

        // Check that the token was deleted
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /**
     * Test get authenticated user profile.
     */
    public function test_can_get_authenticated_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }

    /**
     * Test unauthenticated access to protected routes.
     */
    public function test_unauthenticated_access_to_protected_routes(): void
    {
        // Try to access user profile without authentication
        $response = $this->getJson('/api/me');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test validation errors during registration.
     */
    public function test_validation_errors_during_registration(): void
    {
        // Test email validation
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');

        // Test password length validation
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');

        // Test password confirmation validation
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');

        // Test unique email validation
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /**
     * Test validation errors during login.
     */
    public function test_validation_errors_during_login(): void
    {
        // Test required fields
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        // Test email format
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /**
     * Test refresh token.
     */
    public function test_can_refresh_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Token refreshed successfully.',
            ]);

        // Ensure the response has a token
        $this->assertNotEmpty($response->json('data.token'));
    }
}
