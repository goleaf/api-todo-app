<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class WelcomePage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertSee('Welcome to Taskify')
            ->assertSee('Get Started');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@getStartedButton' => '.btn.btn-lg',
            '@loginLink' => 'a[href*="login"]',
            '@registerLink' => 'a[href*="register"]',
            '@appLogo' => '.rounded-circle.bg-purple',
        ];
    }

    /**
     * Click the Get Started button.
     *
     * @return void
     */
    public function clickGetStarted(Browser $browser)
    {
        $browser->click('@getStartedButton');
    }

    /**
     * Click the Login link.
     *
     * @return void
     */
    public function clickLogin(Browser $browser)
    {
        $browser->click('@loginLink');
    }

    /**
     * Click the Register link.
     *
     * @return void
     */
    public function clickRegister(Browser $browser)
    {
        $browser->click('@registerLink');
    }
}
