<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminLoginTest extends DuskTestCase
{
    /**
     * Test admin login page loads.
     */
    public function testAdminLoginPageLoads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                    ->assertSee('Login to Admin Panel')
                    ->assertSee('Email')
                    ->assertSee('Password');
        });
    }

    /**
     * Test login with invalid credentials.
     */
    public function testLoginWithInvalidCredentials()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                    ->type('email', 'invalid@example.com')
                    ->type('password', 'wrong-password')
                    ->press('Login')
                    ->assertSee('These credentials do not match our records');
        });
    }

    /**
     * Test login with valid admin credentials.
     */
    public function testLoginWithValidCredentials()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit('/admin/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password') // Assuming default password
                    ->press('Login')
                    ->waitForLocation('/admin/dashboard')
                    ->assertPathIs('/admin/dashboard');
        });
    }
} 