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
        return route('admin.dashboard');
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
                ->assertSee('Dashboard')
                ->assertSee('Welcome to Admin Panel');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@users-card' => '#users-card',
            '@tasks-card' => '#tasks-card',
            '@categories-card' => '#categories-card',
            '@tags-card' => '#tags-card',
            '@logout-button' => 'form[action$="logout"] button',
            '@users-link' => 'a[href$="' . route('admin.users.index', [], false) . '"]',
            '@categories-link' => 'a[href$="' . route('admin.categories.index', [], false) . '"]',
            '@tags-link' => 'a[href$="' . route('admin.tags.index', [], false) . '"]',
            '@tasks-link' => 'a[href$="' . route('admin.tasks.index', [], false) . '"]',
        ];
    }

    /**
     * Navigate to users page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function navigateToUsers(Browser $browser)
    {
        $browser->click('@users-link');
    }

    /**
     * Navigate to tasks page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function navigateToTasks(Browser $browser)
    {
        $browser->click('@tasks-link');
    }

    /**
     * Navigate to categories page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function navigateToCategories(Browser $browser)
    {
        $browser->click('@categories-link');
    }

    /**
     * Navigate to tags page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function navigateToTags(Browser $browser)
    {
        $browser->click('@tags-link');
    }
    
    /**
     * Logout the admin user.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function logout(Browser $browser)
    {
        $browser->click('@logout-button');
    }
} 