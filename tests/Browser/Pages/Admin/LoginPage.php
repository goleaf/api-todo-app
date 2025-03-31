<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class LoginPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/admin/login';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@email' => 'input[name=email]',
            '@password' => 'input[name=password]',
            '@login' => 'button[type=submit]',
        ];
    }

    /**
     * Login as admin
     *
     * @param Browser $browser
     * @param string $email
     * @param string $password
     * @return void
     */
    public function login(Browser $browser, $email, $password)
    {
        $browser->type('@email', $email)
                ->type('@password', $password)
                ->click('@login');
    }
} 