<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\LivewireTestHelpers;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_page_contains_livewire_component()
    {
        $this->get('/login')
            ->assertSuccessful()
            ->assertSeeLivewire('auth.login');
    }

    /** @test */
    public function it_can_render_login_form()
    {
        Livewire::test(Login::class)
            ->assertViewIs('livewire.auth.login')
            ->assertSee('Email')
            ->assertSee('Password')
            ->assertSee('Login');
    }

    /** @test */
    public function it_validates_email_field()
    {
        // Using the form submission helper
        LivewireTestHelpers::testFormSubmission(
            Login::class,
            'login',
            [
                'email' => 'not-an-email',
                'password' => 'password123',
            ],
            [
                'email' => fn($value) => filter_var($value, FILTER_VALIDATE_EMAIL),
            ]
        )
        ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        Livewire::test(Login::class)
            ->set('email', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['email' => 'required', 'password' => 'required']);
    }

    /** @test */
    public function it_displays_error_for_invalid_credentials()
    {
        // Create a user
        $user = LivewireTestHelpers::createUserWithUniqueEmail([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);
        
        // Test with invalid credentials
        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('email')
            ->assertSee('These credentials do not match our records');
    }

    /** @test */
    public function it_logs_in_user_with_correct_credentials()
    {
        // Create a user with known credentials
        $user = LivewireTestHelpers::createUserWithUniqueEmail([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);
        
        // Assert not logged in
        $this->assertFalse(Auth::check());
        
        // Test login with correct credentials
        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'correct-password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect('/dashboard');
        
        // Assert now logged in
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    /** @test */
    public function it_redirects_to_intended_url_after_login()
    {
        // Create user
        $user = LivewireTestHelpers::createUserWithUniqueEmail([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);
        
        // Simulate accessing a protected route
        session(['url.intended' => '/tasks']);
        
        // Test login with redirection
        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'correct-password')
            ->call('login')
            ->assertRedirect('/tasks');
    }

    /** @test */
    public function it_remembers_user_when_remember_option_is_selected()
    {
        // Create user
        $user = LivewireTestHelpers::createUserWithUniqueEmail([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);
        
        // Test login with remember option
        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'correct-password')
            ->set('remember', true)
            ->call('login');
        
        // Check for remember token cookie
        $this->assertNotNull($user->fresh()->remember_token);
    }

    /** @test */
    public function it_can_navigate_to_register_page()
    {
        // Test navigation links
        Livewire::test(Login::class)
            ->call('navigateToRegister')
            ->assertRedirect('/register');
    }

    /** @test */
    public function it_can_navigate_to_forgot_password_page()
    {
        Livewire::test(Login::class)
            ->call('navigateToForgotPassword')
            ->assertRedirect('/forgot-password');
    }
} 