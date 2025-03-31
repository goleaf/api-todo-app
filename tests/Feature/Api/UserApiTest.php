<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected User $adminUser;

    protected string $baseUrl = '/api/users';

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function admin_can_get_all_users()
    {
        Sanctum::actingAs($this->adminUser);

        User::factory()->count(3)->create();

        $response = $this->getJson($this->baseUrl);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(5, 'data') // 5 = 3 + admin + regular user
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function regular_user_cannot_get_all_users()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->baseUrl);

        $response->assertStatus(403);
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
            'role' => 'user',
        ];

        $response = $this->postJson($this->baseUrl, $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'role' => 'user',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_a_user()
    {
        Sanctum::actingAs($this->user);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson($this->baseUrl, $data);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_show_a_user()
    {
        Sanctum::actingAs($this->adminUser);

        $testUser = User::factory()->create();

        $response = $this->getJson("{$this->baseUrl}/{$testUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $testUser->id,
                    'name' => $testUser->name,
                    'email' => $testUser->email,
                ],
            ]);
    }

    /** @test */
    public function user_can_view_their_own_profile()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("{$this->baseUrl}/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
            ]);
    }

    /** @test */
    public function regular_user_cannot_view_other_users_profile()
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();

        $response = $this->getJson("{$this->baseUrl}/{$otherUser->id}");

        $response->assertStatus(403);
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

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $testUser->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $testUser->id,
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /** @test */
    public function user_can_update_their_own_profile()
    {
        Sanctum::actingAs($this->user);

        $data = [
            'name' => 'My New Name',
            'email' => 'mynew@example.com',
        ];

        $response = $this->putJson('/api/profile', $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $data['name'],
                    'email' => $data['email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /** @test */
    public function regular_user_cannot_update_other_users()
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();

        $data = [
            'name' => 'Hacked Name',
        ];

        $response = $this->putJson("{$this->baseUrl}/{$otherUser->id}", $data);

        $response->assertStatus(403);
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

    /** @test */
    public function regular_user_cannot_delete_a_user()
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();

        $response = $this->deleteJson("{$this->baseUrl}/{$otherUser->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_get_their_statistics()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/users/statistics');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function validation_fails_when_creating_user_with_invalid_data()
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ];

        $response = $this->postJson($this->baseUrl, $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_user_endpoints()
    {
        $response = $this->getJson($this->baseUrl);

        $response->assertStatus(401);
    }
}
