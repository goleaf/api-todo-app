<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class UsersPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/admin/users';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
                ->assertSee('Users');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@create-button' => 'a[href$="/admin/users/create"]',
            '@search-input' => 'input[name="search"]',
            '@search-button' => 'button[type="submit"]',
            '@first-view-button' => 'table tbody tr:first-child a.btn-info',
            '@first-edit-button' => 'table tbody tr:first-child a.btn-primary',
            '@first-delete-button' => 'table tbody tr:first-child button.btn-danger',
        ];
    }

    /**
     * Navigate to create user page
     *
     * @param Browser $browser
     * @return void
     */
    public function navigateToCreate(Browser $browser)
    {
        $browser->click('@create-button');
    }

    /**
     * Search for a user
     *
     * @param Browser $browser
     * @param string $query
     * @return void
     */
    public function search(Browser $browser, $query)
    {
        $browser->type('@search-input', $query)
                ->click('@search-button');
    }

    /**
     * View first user in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function viewFirstUser(Browser $browser)
    {
        $browser->click('@first-view-button');
    }

    /**
     * Edit first user in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function editFirstUser(Browser $browser)
    {
        $browser->click('@first-edit-button');
    }

    /**
     * Delete first user in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function deleteFirstUser(Browser $browser)
    {
        $browser->click('@first-delete-button')
                ->waitForDialog()
                ->acceptDialog();
    }
} 