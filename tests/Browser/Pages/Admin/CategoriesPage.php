<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class CategoriesPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/admin/categories';
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
                ->assertSee('Categories');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@create-button' => 'a[href$="/admin/categories/create"]',
            '@search-input' => 'input[name="search"]',
            '@search-button' => 'button[type="submit"]',
            '@first-view-button' => 'table tbody tr:first-child a.btn-info',
            '@first-edit-button' => 'table tbody tr:first-child a.btn-primary',
            '@first-delete-button' => 'table tbody tr:first-child button.btn-danger',
        ];
    }

    /**
     * Navigate to create category page
     *
     * @param Browser $browser
     * @return void
     */
    public function navigateToCreate(Browser $browser)
    {
        $browser->click('@create-button');
    }

    /**
     * Search for a category
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
     * View first category in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function viewFirstCategory(Browser $browser)
    {
        $browser->click('@first-view-button');
    }

    /**
     * Edit first category in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function editFirstCategory(Browser $browser)
    {
        $browser->click('@first-edit-button');
    }

    /**
     * Delete first category in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function deleteFirstCategory(Browser $browser)
    {
        $browser->click('@first-delete-button')
                ->waitForDialog()
                ->acceptDialog();
    }
} 