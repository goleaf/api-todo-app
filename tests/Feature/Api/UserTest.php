<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class UserTest extends TestCase
{
    use WithFaker;

    protected User $user;
    protected User $adminUser;
    protected string $baseUrl = '/api/users';

    protected function setUp(): void
    {
        parent::setUp();
        
        // Refresh database for SQLite compatibility
        Artisan::call('migrate:fresh');
        
        // Create users for testing
        $this->user = User::factory()->create([
            'password' => Hash::make('Password123!'),
        ]);
        $this->adminUser = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'password' => Hash::make('Password123!'),
        ]);
        
        // Set up a fake storage disk for profile photo tests
        Storage::fake('public');
    }

    /**
     * AUTH TESTS
     */

    /** @test */
    public function user_can_register()
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
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    /** @test */
    public function user_can_login()
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
            ]);

        // Ensure the response has a token
        $this->assertNotEmpty($response->json('data.token'));
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();

        // Create a token for the user
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/logout');

        $response->assertStatus(200);

        // Check that the token was deleted
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /**
     * PROFILE TESTS
     */

    /** @test */
    public function can_get_profile()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                ],
            ]);
    }

    /** @test */
    public function can_update_profile()
    {
        Sanctum::actingAs($this->user);

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated_email@example.com',
        ];

        $response = $this->putJson('/api/profile', $updatedData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
        ]);
    }

    /**
     * USER MANAGEMENT TESTS
     */

    /** @test */
    public function admin_can_get_all_users()
    {
        Sanctum::actingAs($this->adminUser);

        User::factory()->count(3)->create();

        $response = $this->getJson($this->baseUrl);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
            
        // Verify we got users back
        $responseData = $response->json('data');
        $this->assertIsArray($responseData);
        $this->assertNotEmpty($responseData);
    }

    /** @test */
    public function admin_can_create_a_user()
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRole::USER->value,
        ];

        $response = $this->postJson($this->baseUrl, $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /** @test */
    public function admin_can_update_a_user()
    {
        Sanctum::actingAs($this->adminUser);

        $testUser = User::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson("{$this->baseUrl}/{$testUser->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $testUser->id,
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /** @test */
    public function admin_can_delete_a_user()
    {
        Sanctum::actingAs($this->adminUser);

        $testUser = User::factory()->create();

        $response = $this->deleteJson("{$this->baseUrl}/{$testUser->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', [
            'id' => $testUser->id,
        ]);
    }
} 