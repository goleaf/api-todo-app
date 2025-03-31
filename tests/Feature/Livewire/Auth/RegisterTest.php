<?php

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use Livewire\Livewire;
use Tests\Feature\Livewire\LivewireAuthTestCase;

class RegisterTest extends LivewireAuthTestCase
{
    /** @test */
    public function register_page_contains_livewire_component()
    {
        $this->assertGuestCanAccess('/register');
        $this->get('/register')->assertSeeLivewire('auth.register');
    }

    /** @test */
    public function authenticated_users_are_redirected_from_register_page()
    {
        $this->assertAuthUserRedirectedFrom('/register', '/dashboard');
    }

    /** @test */
    public function register_component_validates_required_fields()
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->set('email', '')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('register')
            ->assertHasErrors(['name' => 'required', 'email' => 'required', 'password' => 'required']);
    }

    /** @test */
    public function register_component_validates_email_format()
    {
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'not-an-email')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function register_component_validates_password_confirmation()
    {
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'different-password')
            ->call('register')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    /** @test */
    public function register_component_validates_password_length()
    {
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('register')
            ->assertHasErrors(['password' => 'min']);
    }

    /** @test */
    public function user_can_register_with_valid_information()
    {
        $this->assertUserCanRegister(
            Register::class,
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'terms' => true,
            ]
        );
    }

    /** @test */
    public function cannot_register_with_existing_email()
    {
        // Create a user with a known email
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // Try to register with the same email
        Livewire::test(Register::class)
            ->set('name', 'Another User')
            ->set('email', 'existing@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    public function register_dispatches_registered_event_on_success()
    {
        // Create a unique email to avoid conflicts
        $email = 'unique'.uniqid().'@example.com';

        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', $email)
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('terms', true)
            ->call('register')
            ->assertDispatched('registered');

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);

        $this->assertAuthenticated();
    }
}
