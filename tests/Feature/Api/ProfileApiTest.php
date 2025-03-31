<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate
        $this->user = User::factory()->create([
            'password' => Hash::make('Password123!'),
        ]);
        Sanctum::actingAs($this->user);

        // Set up a fake storage disk
        Storage::fake('public');
    }

    /**
     * Test getting user profile.
     */
    public function test_can_get_profile(): void
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'photo_url',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
            ]);
    }

    /**
     * Test updating user profile.
     */
    public function test_can_update_profile(): void
    {
        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated_email@example.com',
        ];

        $response = $this->putJson('/api/profile', $updatedData);

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
                'message' => 'Profile updated successfully.',
                'data' => [
                    'name' => $updatedData['name'],
                    'email' => $updatedData['email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email'],
        ]);
    }

    /**
     * Test changing password.
     */
    public function test_can_change_password(): void
    {
        // Ensure the user has the expected password
        $this->user->update([
            'password' => Hash::make('Password123!'),
        ]);
        
        $passwordData = [
            'current_password' => 'Password123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->putJson('/api/profile/password', $passwordData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Password updated successfully.',
            ]);

        // Verify login with new password
        $loginData = [
            'email' => $this->user->email,
            'password' => 'NewPassword123!',
        ];

        $response = $this->postJson('/api/login', $loginData);
        $response->assertStatus(200);

        // Try with old password (should fail)
        $loginData = [
            'email' => $this->user->email,
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/login', $loginData);
        $response->assertStatus(401);
    }

    /**
     * Test changing password with incorrect current password.
     */
    public function test_cannot_change_password_with_incorrect_current_password(): void
    {
        $passwordData = [
            'current_password' => 'WrongPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];

        $response = $this->putJson('/api/profile/password', $passwordData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('current_password')
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test uploading an avatar.
     */
    public function test_can_upload_avatar(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson('/api/profile/photo', [
            'photo' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'photo_url',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Profile photo uploaded successfully.',
            ]);

        // Get the avatar URL from the response
        $avatarUrl = $response->json('data.photo_url');

        // Check that the file exists in storage
        // Extract the filename from the URL
        $filename = basename($avatarUrl);
        Storage::disk('public')->assertExists('profile-photos/'.$filename);

        // Verify that the user's photo_path field has been updated
        $this->user->refresh();
        $this->assertNotNull($this->user->photo_path);
    }

    /**
     * Test validation errors when updating profile.
     */
    public function test_validation_errors_when_updating_profile(): void
    {
        // Create another user to test unique email validation
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // Test email format validation
        $response = $this->putJson('/api/profile', [
            'name' => 'Test User',
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email')
            ->assertJson([
                'success' => false,
            ]);

        // Test unique email validation
        $response = $this->putJson('/api/profile', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email')
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test validation errors when changing password.
     */
    public function test_validation_errors_when_changing_password(): void
    {
        // Test password confirmation validation
        $response = $this->putJson('/api/profile/password', [
            'current_password' => 'Password123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password')
            ->assertJson([
                'success' => false,
            ]);

        // Test password length validation
        $response = $this->putJson('/api/profile/password', [
            'current_password' => 'Password123!',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password')
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test validation errors when uploading avatar.
     */
    public function test_validation_errors_when_uploading_avatar(): void
    {
        // Test file type validation
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/profile/photo', [
            'photo' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('photo')
            ->assertJson([
                'success' => false,
            ]);

        // Test file size validation
        $file = UploadedFile::fake()->image('large_image.jpg')->size(5000); // 5MB

        $response = $this->postJson('/api/profile/photo', [
            'photo' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('photo')
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test unauthenticated access to profile endpoints.
     */
    public function test_unauthenticated_access_to_profile_endpoints(): void
    {
        // Log the user out by creating a new test instance without authentication
        $this->refreshApplication();

        // Try to get profile
        $response = $this->getJson('/api/profile');
        $response->assertStatus(401);

        // Try to update profile
        $response = $this->putJson('/api/profile', ['name' => 'Test']);
        $response->assertStatus(401);

        // Try to change password
        $response = $this->putJson('/api/profile/password', [
            'current_password' => 'Password123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);
        $response->assertStatus(401);

        // Try to upload avatar
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->postJson('/api/profile/photo', ['photo' => $file]);
        $response->assertStatus(401);
    }
}
