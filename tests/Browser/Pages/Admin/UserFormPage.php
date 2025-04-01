<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class UserFormPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('admin.users.create');
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathContains(parse_url(route('admin.users.create'), PHP_URL_PATH))
                ->assertSee('User Form');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@name-input' => 'input[name="name"]',
            '@email-input' => 'input[name="email"]',
            '@password-input' => 'input[name="password"]',
            '@password-confirm-input' => 'input[name="password_confirmation"]',
            '@role-select' => 'select[name="role"]',
            '@submit-button' => 'button[type="submit"]',
            '@back-button' => 'a[href$="' . route('admin.users.index', [], false) . '"]',
        ];
    }

    /**
     * Fill user form
     *
     * @param Browser $browser
     * @param array $userData
     * @return void
     */
    public function fillForm(Browser $browser, array $userData)
    {
        $browser->type('@name-input', $userData['name'] ?? '')
                ->type('@email-input', $userData['email'] ?? '');
                
        if (isset($userData['password'])) {
            $browser->type('@password-input', $userData['password'])
                    ->type('@password-confirm-input', $userData['password']);
        }
        
        if (isset($userData['role'])) {
            $browser->select('@role-select', $userData['role']);
        }
    }

    /**
     * Submit the form
     *
     * @param Browser $browser
     * @return void
     */
    public function submit(Browser $browser)
    {
        $browser->click('@submit-button');
    }

    /**
     * Go back to users list
     *
     * @param Browser $browser
     * @return void
     */
    public function goBack(Browser $browser)
    {
        $browser->click('@back-button');
    }
} 