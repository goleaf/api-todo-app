<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);
        
        // Create a token for authenticated requests
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function it_gets_current_user_profile()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/user');
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->user->id,
                    'name' => 'Test User',
                    'email' => 'test@example.com'
                ]
            ]);
    }

    /** @test */
    public function it_returns_unauthorized_for_non_authenticated_users()
    {
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
    }

    /** @test */
    public function it_updates_user_profile()
    {
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ];
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/user/profile', $updateData);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->user->id,
                    'name' => 'Updated Name',
                    'email' => 'updated@example.com'
                ]
            ]);
        
        // Check database
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    /** @test */
    public function it_validates_email_uniqueness_when_updating_profile()
    {
        // Create another user with a different email
        $otherUser = User::factory()->create([
            'email' => 'other@example.com'
        ]);
        
        // Try to update profile with existing email
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'other@example.com'
        ];
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/user/profile', $updateData);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_updates_password()
    {
        $passwordData = [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/user/password', $passwordData);
        
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password updated successfully'
            ]);
        
        // Verify the password has been changed
        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword', $this->user->password));
    }

    /** @test */
    public function it_validates_current_password()
    {
        $passwordData = [
            'current_password' => 'wrong_password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/user/password', $passwordData);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
        
        // Verify the password has NOT been changed
        $this->user->refresh();
        $this->assertTrue(Hash::check('password', $this->user->password));
    }

    /** @test */
    public function it_requires_password_confirmation()
    {
        $passwordData = [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'different_password'
        ];
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/user/password', $passwordData);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function it_uploads_user_photo()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->image('avatar.jpg');
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/user/photo', [
            'photo' => $file
        ]);
        
        $response->assertStatus(200);
        
        // Get the path from the response
        $photoPath = $response->json('data.photo_path');
        
        // Assert the file was stored
        Storage::disk('public')->assertExists($photoPath);
        
        // Assert user record was updated
        $this->user->refresh();
        $this->assertEquals($photoPath, $this->user->photo_path);
    }

    /** @test */
    public function it_validates_photo_file_type()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('document.pdf', 100);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/user/photo', [
            'photo' => $file
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photo']);
    }

    /** @test */
    public function it_validates_photo_file_size()
    {
        Storage::fake('public');
        
        // Create an image larger than the validation limit (assuming 2MB limit)
        $file = UploadedFile::fake()->image('large_avatar.jpg')->size(3000);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/user/photo', [
            'photo' => $file
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photo']);
    }

    /** @test */
    public function it_deletes_user_photo()
    {
        Storage::fake('public');
        
        // Upload a photo first
        $file = UploadedFile::fake()->image('avatar.jpg');
        
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/user/photo', [
            'photo' => $file
        ]);
        
        $this->user->refresh();
        $photoPath = $this->user->photo_path;
        
        // Now delete the photo
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/user/photo');
        
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Photo deleted successfully'
            ]);
        
        // Assert the file was deleted
        Storage::disk('public')->assertMissing($photoPath);
        
        // Assert user record was updated
        $this->user->refresh();
        $this->assertNull($this->user->photo_path);
    }

    /** @test */
    public function it_gets_user_statistics()
    {
        // Create tasks with different statuses for the user
        \App\Models\Category::factory()
            ->count(3)
            ->create(['user_id' => $this->user->id]);
        
        // Create 5 total tasks: 2 completed, 3 incomplete
        \App\Models\Task::factory()
            ->count(2)
            ->create([
                'user_id' => $this->user->id,
                'completed' => true,
                'category_id' => \App\Models\Category::where('user_id', $this->user->id)->first()->id
            ]);
        
        \App\Models\Task::factory()
            ->count(3)
            ->create([
                'user_id' => $this->user->id,
                'completed' => false,
                'category_id' => \App\Models\Category::where('user_id', $this->user->id)->first()->id
            ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/user/statistics');
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'total_tasks' => 5,
                    'completed_tasks' => 2,
                    'incomplete_tasks' => 3,
                    'completion_rate' => 40, // 2/5 * 100 = 40%
                    'total_categories' => 3
                ]
            ]);
    }

    /** @test */
    public function it_allows_users_to_logout()
    {
        // First verify the user has a token
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->user->id
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/logout');
        
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);
        
        // Verify the token has been deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'token' => hash('sha256', explode('|', $this->token)[1])
        ]);
    }
} 