<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class DashboardPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/admin/dashboard';
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
                ->assertSee('Dashboard');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@users-link' => 'a[href$="/admin/users"]',
            '@categories-link' => 'a[href$="/admin/categories"]',
            '@tags-link' => 'a[href$="/admin/tags"]',
            '@tasks-link' => 'a[href$="/admin/tasks"]',
            '@logout-button' => 'button.btn-logout',
        ];
    }

    /**
     * Navigate to users page
     *
     * @param Browser $browser
     * @return void
     */
    public function navigateToUsers(Browser $browser)
    {
        $browser->click('@users-link');
    }

    /**
     * Navigate to categories page
     *
     * @param Browser $browser
     * @return void
     */
    public function navigateToCategories(Browser $browser)
    {
        $browser->click('@categories-link');
    }

    /**
     * Navigate to tags page
     *
     * @param Browser $browser
     * @return void
     */
    public function navigateToTags(Browser $browser)
    {
        $browser->click('@tags-link');
    }

    /**
     * Navigate to tasks page
     *
     * @param Browser $browser
     * @return void
     */
    public function navigateToTasks(Browser $browser)
    {
        $browser->click('@tasks-link');
    }

    /**
     * Logout
     *
     * @param Browser $browser
     * @return void
     */
    public function logout(Browser $browser)
    {
        $browser->click('@logout-button');
    }
} 