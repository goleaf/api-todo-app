<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test login functionality
     *
     * @return void
     */
    public function test_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Log in')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard');
        });
    }

    /**
     * Test failed login with incorrect credentials
     *
     * @return void
     */
    public function test_failed_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'wrong@example.com')
                ->type('password', 'wrongpassword')
                ->press('Log in')
                ->assertSee('These credentials do not match our records');
        });
    }

    /**
     * Test registration functionality
     *
     * @return void
     */
    public function test_registration()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Register')
                ->type('name', 'Test User')
                ->type('email', 'newuser@example.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press('Register')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard');
        });
    }

    /**
     * Test authentication check redirects
     *
     * @return void
     */
    public function test_auth_redirects()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/dashboard')
                ->assertPathIs('/login')
                ->visit('/tasks')
                ->assertPathIs('/login');
        });
    }
}
