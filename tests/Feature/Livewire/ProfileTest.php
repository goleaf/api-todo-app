<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'password' => Hash::make('password')
        ]);
    }

    /** @test */
    public function profile_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->assertStatus(200)
            ->assertSee('Profile Settings')
            ->assertSee('Original Name')
            ->assertSee('original@example.com');
    }

    /** @test */
    public function user_can_update_profile_information()
    {
        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->set('name', 'Updated Name')
            ->set('email', 'updated@example.com')
            ->call('updateProfile');
            
        $this->user->refresh();
        
        $this->assertEquals('Updated Name', $this->user->name);
        $this->assertEquals('updated@example.com', $this->user->email);
    }

    /** @test */
    public function profile_information_requires_valid_data()
    {
        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->set('name', '')
            ->set('email', 'not-an-email')
            ->call('updateProfile')
            ->assertHasErrors(['name', 'email']);
    }

    /** @test */
    public function user_cannot_use_another_users_email()
    {
        // Create another user
        User::factory()->create([
            'email' => 'existing@example.com'
        ]);
        
        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->set('name', 'Updated Name')
            ->set('email', 'existing@example.com')
            ->call('updateProfile')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    public function user_can_update_password()
    {
        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->set('current_password', 'password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('updatePassword');
            
        $this->user->refresh();
        
        $this->assertTrue(Hash::check('new-password', $this->user->password));
    }

    /** @test */
    public function current_password_must_be_correct()
    {
        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->set('current_password', 'wrong-password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
    }

    /** @test */
    public function new_passwords_must_match()
    {
        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->set('current_password', 'password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'different-password')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    }

    /** @test */
    public function password_must_be_minimum_length()
    {
        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->set('current_password', 'password')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    }
}
