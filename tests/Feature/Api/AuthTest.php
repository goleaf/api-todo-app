<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test user registration.
     */
    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
            'device_name' => 'test_device',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'token',
                ],
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('User registered successfully', $response->json('message'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test user login.
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'password',
            'device_name' => 'test_device',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'token',
                ],
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('User logged in successfully', $response->json('message'));
    }

    /**
     * Test user login with invalid credentials.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'wrong_password',
            'device_name' => 'test_device',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);

        $this->assertFalse($response->json('success'));
    }

    /**
     * Test logout functionality.
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_device')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('User logged out successfully', $response->json('message'));
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /**
     * Test accessing a protected route without authentication.
     */
    public function test_protected_route_requires_authentication(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /**
     * Test accessing a protected route with authentication.
     */
    public function test_authenticated_user_can_access_protected_route(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_device')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
            ]);
    }

    /**
     * Test registration validation errors.
     */
    public function test_registration_validation_errors(): void
    {
        // Test with missing required fields
        $response = $this->postJson('/api/register', [
            // No name, email, or password
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'status_code',
                'message',
                'errors' => [
                    'name',
                    'email',
                    'password',
                    'terms',
                ],
            ]);

        $this->assertFalse($response->json('success'));

        // Test with invalid email
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
            'device_name' => 'test_device',
        ]);

        $response->assertStatus(422);
        $this->assertFalse($response->json('success'));
        $this->assertArrayHasKey('email', $response->json('errors'));

        // Test with password confirmation mismatch
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
            'terms' => true,
            'device_name' => 'test_device',
        ]);

        $response->assertStatus(422);
        $this->assertFalse($response->json('success'));
        $this->assertArrayHasKey('password', $response->json('errors'));

        // Test with terms not accepted
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => false,
            'device_name' => 'test_device',
        ]);

        $response->assertStatus(422);
        $this->assertFalse($response->json('success'));
        $this->assertArrayHasKey('terms', $response->json('errors'));
    }

    /**
     * Test registration with duplicate email.
     */
    public function test_registration_with_duplicate_email(): void
    {
        // Create a user first
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        // Try to register with the same email
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'duplicate@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
            'device_name' => 'test_device',
        ]);

        $response->assertStatus(422);
        $this->assertFalse($response->json('success'));
        $this->assertArrayHasKey('email', $response->json('errors'));
    }
}
