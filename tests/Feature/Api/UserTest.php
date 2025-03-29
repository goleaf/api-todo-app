<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting user profile.
     */
    public function testGetUserProfile(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/users/' . $user->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * Test updating user profile.
     */
    public function testUpdateUserProfile(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $updateData = [
            'name' => 'Updated Name',
        ];

        $response = $this->putJson('/api/users/' . $user->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test updating user profile with validation errors.
     */
    public function testUpdateUserProfileValidationErrors(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/users/' . $user->id, [
            'name' => '', // Name is required
            'email' => 'not-an-email', // Invalid email
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    /**
     * Test a user can't update another user's profile.
     */
    public function testCannotUpdateOtherUserProfile(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/users/' . $otherUser->id, [
            'name' => 'Attempted Update',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test updating user password.
     */
    public function testUpdateUserPassword(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/users/' . $user->id . '/password', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Password updated successfully',
            ]);

        // Test login with new password
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'new-password',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test updating user password with incorrect current password.
     */
    public function testUpdatePasswordWithIncorrectCurrentPassword(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/users/' . $user->id . '/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    /**
     * Test updating user password with validation errors.
     */
    public function testUpdatePasswordValidationErrors(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/users/' . $user->id . '/password', [
            'current_password' => 'correct-password',
            'password' => 'short',
            'password_confirmation' => 'does-not-match',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
} 