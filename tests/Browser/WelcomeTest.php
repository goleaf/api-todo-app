<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WelcomeTest extends DuskTestCase
{
    /**
     * Test welcome page loads correctly
     *
     * @return void
     */
    public function test_welcome_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Welcome to Taskify')
                ->assertSee('Get Started')
                ->assertSee('Your simple yet powerful task management solution')
                ->assertPresent('.rounded-circle.bg-purple');
        });
    }

    /**
     * Test that clicking the Get Started button redirects to login
     *
     * @return void
     */
    public function test_get_started_button()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Get Started')
                ->assertPathIs('/login');
        });
    }

    /**
     * Test that login link works correctly
     *
     * @return void
     */
    public function test_login_link()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Log in')
                ->assertPathIs('/login');
        });
    }

    /**
     * Test that register link works correctly
     *
     * @return void
     */
    public function test_register_link()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Register')
                ->assertPathIs('/register');
        });
    }
}
