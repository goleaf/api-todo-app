<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\Dashboard;
use Tests\DuskTestCase;

class AdminLoginTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    /**
     * Test admin login page loads.
     */
    public function testAdminLoginPageLoads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('admin.login'))
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
            $browser->visit(route('admin.login'))
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
        // Create an admin user for testing
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(route('admin.login'))
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->waitForLocation(route('admin.dashboard'))
                    ->assertPathIs('/admin/dashboard');
        });
    }

    /**
     * Test admin logout functionality.
     */
    public function testAdminLogout()
    {
        // Create an admin user for testing
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(new Dashboard)
                    ->logout()
                    ->assertPathIs('/admin/login');
        });
    }
} 