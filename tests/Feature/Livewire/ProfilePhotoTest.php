<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function can_upload_profile_photo()
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create(['photo_path' => null]);
        $this->actingAs($user);
        
        $test = Livewire::test(Profile::class)
            ->set('photo', UploadedFile::fake()->image('avatar.jpg'))
            ->call('uploadPhoto');
            
        $user->refresh();
        
        $this->assertNotNull($user->photo_path);
        Storage::disk('public')->assertExists($user->photo_path);
    }

    /** @test */
    public function can_update_existing_profile_photo()
    {
        $this->withoutExceptionHandling();
        
        // Create a user with an existing photo
        $user = User::factory()->create(['photo_path' => 'profile-photos/old-avatar.jpg']);
        Storage::disk('public')->put('profile-photos/old-avatar.jpg', 'old content');
        $this->actingAs($user);
        
        // Test uploading a new photo
        $test = Livewire::test(Profile::class)
            ->set('photo', UploadedFile::fake()->image('new-avatar.jpg'))
            ->call('uploadPhoto');
        
        // Refresh user from database    
        $user->refresh();
        
        // Assert that the photo_path is different now
        $this->assertNotNull($user->photo_path);
        $this->assertNotEquals('profile-photos/old-avatar.jpg', $user->photo_path);
        
        // Assert old photo is deleted
        Storage::disk('public')->assertMissing('profile-photos/old-avatar.jpg');
        
        // Assert new photo exists
        Storage::disk('public')->assertExists($user->photo_path);
    }

    /** @test */
    public function can_delete_profile_photo()
    {
        $this->withoutExceptionHandling();
        
        // Create user with a photo
        $user = User::factory()->create(['photo_path' => 'profile-photos/avatar.jpg']);
        Storage::disk('public')->put('profile-photos/avatar.jpg', 'test content');
        $this->actingAs($user);
        
        // Delete the photo
        $test = Livewire::test(Profile::class)
            ->call('deletePhoto');
        
        // Refresh user from database
        $user->refresh();
        
        // Assert that photo_path is now null
        $this->assertNull($user->photo_path);
        
        // Assert the photo file is removed
        Storage::disk('public')->assertMissing('profile-photos/avatar.jpg');
    }

    /** @test */
    public function photo_must_be_an_image()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        Livewire::test(Profile::class)
            ->set('photo', UploadedFile::fake()->create('document.pdf', 100))
            ->call('uploadPhoto')
            ->assertHasErrors(['photo' => 'image']);
    }

    /** @test */
    public function photo_must_not_exceed_maximum_size()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        Livewire::test(Profile::class)
            ->set('photo', UploadedFile::fake()->create('avatar.jpg', 3000))
            ->call('uploadPhoto')
            ->assertHasErrors(['photo' => 'max']);
    }

    /** @test */
    public function can_start_and_cancel_editing_photo()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        Livewire::test(Profile::class)
            ->assertSet('editingPhoto', false)
            ->call('startEditingPhoto')
            ->assertSet('editingPhoto', true)
            ->call('cancelEditing')
            ->assertSet('editingPhoto', false);
    }
} 