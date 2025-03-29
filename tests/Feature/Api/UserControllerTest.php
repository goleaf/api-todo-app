<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup for tests.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test getting user profile via API.
     */
    public function test_user_can_get_their_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Test updating user profile via API.
     */
    public function test_user_can_update_their_profile(): void
    {
        $user = User::factory()->create();
        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated-email@example.com',
        ];

        $response = $this->actingAs($user)->putJson('/api/user', $updatedData);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => 'Updated Name',
                'email' => 'updated-email@example.com',
            ],
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated-email@example.com',
        ]);
    }

    /**
     * Test updating user password via API.
     */
    public function test_user_can_update_their_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('current-password'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/user/password', [
            'current_password' => 'current-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(200);
        $this->assertTrue(
            auth()->validate([
                'email' => $user->email,
                'password' => 'new-password',
            ])
        );
    }

    /**
     * Test password update requires correct current password.
     */
    public function test_password_update_requires_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('current-password'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/user/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('current_password');
    }

    /**
     * Test password confirmation must match.
     */
    public function test_password_confirmation_must_match(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('current-password'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/user/password', [
            'current_password' => 'current-password',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    /**
     * Test user can upload profile photo.
     */
    public function test_user_can_upload_profile_photo(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->postJson('/api/user/photo', [
            'photo' => $file,
        ]);

        $response->assertStatus(200);
        Storage::disk('public')->assertExists('profile-photos/'.$file->hashName());

        $user->refresh();
        $this->assertNotNull($user->photo_path);
    }

    /**
     * Test API returns proper JSON structure for users.
     */
    public function test_api_returns_proper_json_structure_for_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'photo_url',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    /**
     * Test user stats endpoint.
     */
    public function test_user_can_get_their_stats(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'total_todos',
                'completed_todos',
                'total_tasks',
                'completed_tasks',
                'categories',
            ],
        ]);
    }

    /**
     * Test user validation rules.
     */
    public function test_user_validation_rules(): void
    {
        $user = User::factory()->create();
        $existingUser = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/user', [
            'name' => '', // Empty name
            'email' => $existingUser->email, // Duplicate email
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email']);
    }

    /**
     * Test unauthenticated access is prevented.
     */
    public function test_unauthenticated_access_is_prevented(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}
