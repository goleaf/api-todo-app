<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test the complete registration flow.
     */
    public function test_complete_registration_flow(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->waitFor('form')
                // Fill in the registration form
                ->type('name', 'Test User')
                ->type('email', 'test_flow@example.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                // Submit the form
                ->press('Create Account')
                // Wait for redirect to home and verify auth state
                ->waitForLocation('/')
                ->assertPathIs('/')
                // Verify we're logged in by checking for user-related elements
                ->assertSee('My Tasks');
        });
    }

    /**
     * Test registration form validation errors.
     */
    public function test_registration_form_validation_errors(): void
    {
        $this->browse(function (Browser $browser) {
            // Create a user to test email uniqueness validation
            User::factory()->create([
                'email' => 'existing@example.com',
            ]);

            $browser->visit('/register')
                ->waitFor('form')
                // Test empty form submission
                ->press('Create Account')
                ->assertSee('The name field is required')
                
                // Test email format validation
                ->type('name', 'Test User')
                ->type('email', 'not-an-email')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press('Create Account')
                ->assertSee('valid email')
                
                // Test password mismatch validation
                ->type('name', 'Test User')
                ->type('email', 'test@example.com')
                ->type('password', 'password1')
                ->type('password_confirmation', 'password2')
                ->press('Create Account')
                ->assertSee('password field confirmation does not match')
                
                // Test existing email validation
                ->type('name', 'Test User')
                ->type('email', 'existing@example.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press('Create Account')
                ->assertSee('email has already been taken');
        });
    }

    /**
     * Test the complete login flow.
     */
    public function test_complete_login_flow(): void
    {
        // Create a user to test login
        $user = User::factory()->create([
            'email' => 'login_test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('form')
                // Fill in the login form
                ->type('email', 'login_test@example.com')
                ->type('password', 'password')
                // Submit the form
                ->press('Sign In')
                // Wait for redirect to home and verify auth state
                ->waitForLocation('/')
                ->assertPathIs('/')
                // Verify we're logged in by checking for user-related elements
                ->assertSee('My Tasks');
        });
    }

    /**
     * Test login form validation and error messages.
     */
    public function test_login_form_validation_and_errors(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('form')
                // Test empty form submission
                ->press('Sign In')
                ->assertSee('The email field is required')
                
                // Test invalid credentials
                ->type('email', 'wrong@example.com')
                ->type('password', 'wrongpassword')
                ->press('Sign In')
                ->assertSee('Invalid credentials')
                
                // Test invalid email format
                ->clear('email')
                ->type('email', 'not-an-email')
                ->press('Sign In')
                ->assertSee('valid email');
        });
    }

    /**
     * Test logout flow.
     */
    public function test_logout_flow(): void
    {
        // Create a user to test logout
        $user = User::factory()->create([
            'email' => 'logout_test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                // Verify we're logged in
                ->assertSee('My Tasks')
                // Find and click the logout button/link
                ->click('.user-dropdown-toggle') // This assumes there's a class to toggle user dropdown
                ->waitFor('.logout-button') // This assumes there's a class for the logout button
                ->click('.logout-button')
                // Wait for redirect and verify we're logged out
                ->waitForLocation('/login')
                ->assertPathIs('/login');
        });
    }

    /**
     * Test protected routes redirect to login when not authenticated.
     */
    public function test_protected_routes_redirect_to_login(): void
    {
        $this->browse(function (Browser $browser) {
            // Test a few protected routes
            $protectedRoutes = ['/', '/todos', '/profile', '/stats', '/calendar'];
            
            foreach ($protectedRoutes as $route) {
                $browser->visit($route)
                    ->waitForLocation('/login')
                    ->assertPathIs('/login');
            }
        });
    }

    /**
     * Test navigation between login and register pages.
     */
    public function test_navigation_between_login_and_register_pages(): void
    {
        $this->browse(function (Browser $browser) {
            // Start at login page and navigate to register
            $browser->visit('/login')
                ->waitFor('form')
                ->clickLink('Register')
                ->waitForLocation('/register')
                ->assertPathIs('/register')
                
                // Now navigate back to login
                ->clickLink('Login')
                ->waitForLocation('/login')
                ->assertPathIs('/login');
        });
    }
} 