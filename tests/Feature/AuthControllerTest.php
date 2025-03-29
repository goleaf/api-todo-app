<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_new_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at'
                    ],
                    'token'
                ]
            ]);

        // Check database
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        // Check token was created
        $user = User::where('email', 'test@example.com')->first();
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class
        ]);
    }

    /** @test */
    public function it_validates_registration_input()
    {
        // Missing name and password confirmation
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'password']);
    }

    /** @test */
    public function it_validates_unique_email_during_registration()
    {
        // Create a user first
        User::factory()->create([
            'email' => 'existing@example.com'
        ]);

        // Try to register with the same email
        $userData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_logs_in_a_user()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ],
                    'token'
                ]
            ]);

        // Check token was created
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class
        ]);
    }

    /** @test */
    public function it_validates_login_credentials()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        // Try to login with wrong password
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrong_password'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials'
            ]);
    }

    /** @test */
    public function it_requires_both_email_and_password_for_login()
    {
        $loginData = [
            'email' => 'test@example.com'
            // Missing password
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function it_logs_out_a_user()
    {
        // Create a user
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Make logout request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);

        // Check token was deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'token' => hash('sha256', explode('|', $token)[1])
        ]);
    }

    /** @test */
    public function it_prevents_unauthenticated_logout()
    {
        $response = $this->postJson('/api/logout');
        $response->assertStatus(401);
    }

    /** @test */
    public function it_refreshes_token()
    {
        // Create a user
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Make refresh token request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token'
                ]
            ]);

        // Old token should be deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'token' => hash('sha256', explode('|', $token)[1])
        ]);

        // New token should be created
        $newToken = $response->json('data.token');
        $this->assertNotEquals($token, $newToken);
    }

    /** @test */
    public function it_validates_password_strength_during_registration()
    {
        // Try with a short password
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Check database to ensure user was not created
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function it_requires_confirmed_password_during_registration()
    {
        // Password and confirmation don't match
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Check database to ensure user was not created
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com'
        ]);
    }
} 