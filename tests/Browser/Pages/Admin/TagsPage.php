<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class TagsPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/admin/tags';
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
                ->assertSee('Tags');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@create-button' => 'a[href$="/admin/tags/create"]',
            '@search-input' => 'input[name="search"]',
            '@search-button' => 'button[type="submit"]',
            '@first-view-button' => 'table tbody tr:first-child a.btn-info',
            '@first-edit-button' => 'table tbody tr:first-child a.btn-primary',
            '@first-delete-button' => 'table tbody tr:first-child button.btn-danger',
        ];
    }

    /**
     * Navigate to create tag page
     *
     * @param Browser $browser
     * @return void
     */
    public function navigateToCreate(Browser $browser)
    {
        $browser->click('@create-button');
    }

    /**
     * Search for a tag
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
     * View first tag in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function viewFirstTag(Browser $browser)
    {
        $browser->click('@first-view-button');
    }

    /**
     * Edit first tag in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function editFirstTag(Browser $browser)
    {
        $browser->click('@first-edit-button');
    }

    /**
     * Delete first tag in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function deleteFirstTag(Browser $browser)
    {
        $browser->click('@first-delete-button')
                ->waitForDialog()
                ->acceptDialog();
    }
} 