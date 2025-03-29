<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test successful login.
     */
    public function test_successful_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->assertRouteIs('/')
                ->assertSee('My Tasks');
        });
    }

    /**
     * Test unsuccessful login with invalid credentials.
     */
    public function test_unsuccessful_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'wrong@example.com')
                ->type('password', 'wrongpassword')
                ->press('Login')
                ->assertSee('Invalid credentials');
        });
    }

    /**
     * Test login form validation errors.
     */
    public function test_login_validation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'not-an-email')
                ->press('Login')
                ->assertSee('valid email');

            $browser->visit('/login')
                ->type('email', 'valid@example.com')
                ->press('Login')
                ->assertSee('password field is required');
        });
    }
}
