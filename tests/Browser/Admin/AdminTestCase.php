<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

abstract class AdminTestCase extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Create a user with admin role for testing
     *
     * @return User
     */
    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * Login the admin user
     *
     * @param Browser $browser
     * @return Browser
     */
    protected function loginAdmin(Browser $browser): Browser
    {
        return $browser->visit('/admin/login')
            ->type('email', 'admin@example.com')
            ->type('password', 'password')
            ->press('Log in')
            ->waitForLocation('/admin/dashboard');
    }
} 