<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var User
     */
    protected $user;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    /** @test */
    public function user_can_get_their_profile()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
                'message' => 'User profile retrieved successfully',
            ]);
    }

    /** @test */
    public function user_can_update_their_profile()
    {
        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated_'.$this->user->email,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('/api/users/profile', $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $this->user->id,
                    'name' => 'Updated Name',
                    'email' => $updatedData['email'],
                ],
                'message' => 'Profile updated successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => $updatedData['email'],
        ]);
    }

    /** @test */
    public function user_cannot_update_email_to_existing_one()
    {
        // Create another user
        $anotherUser = User::factory()->create();

        $updatedData = [
            'email' => $anotherUser->email,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('/api/users/profile', $updatedData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function user_can_update_password()
    {
        $passwordData = [
            'current_password' => 'password',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ];

        $response = $this->actingAs($this->user)
            ->putJson('/api/users/password', $passwordData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Password updated successfully',
            ]);

        // Verify new password works
        $this->assertTrue(Hash::check('new_password123', User::find($this->user->id)->password));
    }

    /** @test */
    public function user_cannot_update_password_with_wrong_current_password()
    {
        $passwordData = [
            'current_password' => 'wrong_password',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ];

        $response = $this->actingAs($this->user)
            ->putJson('/api/users/password', $passwordData);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'The current password is incorrect');
    }

    /** @test */
    public function user_can_upload_profile_photo()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->user)
            ->postJson('/api/users/photo', [
                'photo' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile photo uploaded successfully',
            ]);

        // Assert photo was stored
        $user = User::find($this->user->id);
        Storage::disk('public')->assertExists($user->photo_path);
    }

    /** @test */
    public function user_can_delete_profile_photo()
    {
        Storage::fake('public');

        // First upload a photo
        $file = UploadedFile::fake()->image('avatar.jpg');
        $this->actingAs($this->user)
            ->postJson('/api/users/photo', [
                'photo' => $file,
            ]);

        $user = User::find($this->user->id);
        $photoPath = $user->photo_path;

        // Then delete it
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/users/photo');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile photo deleted successfully',
            ]);

        // Assert photo was deleted
        Storage::disk('public')->assertMissing($photoPath);

        // Assert photo_path is null in database
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'photo_path' => null,
        ]);
    }

    /** @test */
    public function user_can_get_their_statistics()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/users/statistics');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_tasks' => 0,
                    'completed_tasks' => 0,
                    'incomplete_tasks' => 0,
                    'completion_rate' => 0,
                    'total_categories' => 0,
                ],
                'message' => 'User statistics retrieved successfully',
            ]);
    }

    /** @test */
    public function guest_cannot_access_user_endpoints()
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(401);

        $response = $this->putJson('/api/users/profile', ['name' => 'Test']);
        $response->assertStatus(401);

        $response = $this->putJson('/api/users/password', ['password' => 'test']);
        $response->assertStatus(401);

        $response = $this->postJson('/api/users/photo', ['photo' => 'test']);
        $response->assertStatus(401);

        $response = $this->deleteJson('/api/users/photo');
        $response->assertStatus(401);

        $response = $this->getJson('/api/users/statistics');
        $response->assertStatus(401);
    }
}
