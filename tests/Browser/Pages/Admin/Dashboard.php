<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class Dashboard extends Page
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
            '@refresh-button' => '.btn-refresh',
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
        $browser->clickLink('Users');
    }

    /**
     * Navigate to tasks page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function navigateToTasks(Browser $browser)
    {
        $browser->clickLink('Tasks');
    }

    /**
     * Navigate to categories page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function navigateToCategories(Browser $browser)
    {
        $browser->clickLink('Categories');
    }

    /**
     * Navigate to tags page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function navigateToTags(Browser $browser)
    {
        $browser->clickLink('Tags');
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
    
    /**
     * Refresh the dashboard.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function refreshDashboard(Browser $browser)
    {
        $browser->click('@refresh-button')
                ->pause(1000); // Wait for refresh animation
    }
} 