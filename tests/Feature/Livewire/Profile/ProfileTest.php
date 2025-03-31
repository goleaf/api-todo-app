<?php

namespace Tests\Feature\Livewire\Profile;

use App\Livewire\Profile\ProfileManager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_manager_component_can_render()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(ProfileManager::class);
        $component->assertStatus(200);
    }

    /** @test */
    public function user_can_update_profile_information()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        $this->actingAs($user);

        Livewire::test(ProfileManager::class)
            ->set('name', 'New Name')
            ->set('email', 'new@example.com')
            ->call('updateProfile')
            ->assertHasNoErrors()
            ->assertDispatched('profile-updated');

        $user->refresh();
        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('new@example.com', $user->email);
    }

    /** @test */
    public function user_can_update_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        $this->actingAs($user);

        Livewire::test(ProfileManager::class)
            ->set('currentPassword', 'oldpassword')
            ->set('newPassword', 'newpassword123')
            ->set('newPasswordConfirmation', 'newpassword123')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertDispatched('password-updated');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        $this->actingAs($user);

        Livewire::test(ProfileManager::class)
            ->set('currentPassword', 'oldpassword')
            ->set('newPassword', 'newpassword123')
            ->set('newPasswordConfirmation', 'different')
            ->call('updatePassword')
            ->assertHasErrors(['newPasswordConfirmation' => 'same']);
    }

    /** @test */
    public function current_password_must_be_correct()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        $this->actingAs($user);

        Livewire::test(ProfileManager::class)
            ->set('currentPassword', 'wrongpassword')
            ->set('newPassword', 'newpassword123')
            ->set('newPasswordConfirmation', 'newpassword123')
            ->call('updatePassword')
            ->assertHasErrors(['currentPassword']);
    }

    /** @test */
    public function name_is_required()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ProfileManager::class)
            ->set('name', '')
            ->set('email', 'test@example.com')
            ->call('updateProfile')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function email_must_be_valid()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ProfileManager::class)
            ->set('name', 'Test User')
            ->set('email', 'not-an-email')
            ->call('updateProfile')
            ->assertHasErrors(['email' => 'email']);
    }
}
