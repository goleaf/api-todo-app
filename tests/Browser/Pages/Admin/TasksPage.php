<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class TasksPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('admin.tasks.index');
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
                ->assertSee('Tasks');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@create-button' => 'a[href$="' . route('admin.tasks.create', [], false) . '"]',
            '@search-input' => 'input[name="search"]',
            '@search-button' => 'button[type="submit"]',
            '@first-view-button' => 'table tbody tr:first-child a.btn-info',
            '@first-edit-button' => 'table tbody tr:first-child a.btn-primary',
            '@first-delete-button' => 'table tbody tr:first-child button.btn-danger',
            '@category-filter' => 'select[name="category_id"]',
            '@status-filter' => 'select[name="status"]',
        ];
    }

    /**
     * Navigate to create task page
     *
     * @param Browser $browser
     * @return void
     */
    public function navigateToCreate(Browser $browser)
    {
        $browser->click('@create-button');
    }

    /**
     * Search for a task
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
     * Filter tasks by category
     *
     * @param Browser $browser
     * @param string $categoryId
     * @return void
     */
    public function filterByCategory(Browser $browser, $categoryId)
    {
        $browser->select('@category-filter', $categoryId)
                ->click('@search-button');
    }

    /**
     * Filter tasks by status
     *
     * @param Browser $browser
     * @param string $status
     * @return void
     */
    public function filterByStatus(Browser $browser, $status)
    {
        $browser->select('@status-filter', $status)
                ->click('@search-button');
    }

    /**
     * View first task in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function viewFirstTask(Browser $browser)
    {
        $browser->click('@first-view-button');
    }

    /**
     * Edit first task in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function editFirstTask(Browser $browser)
    {
        $browser->click('@first-edit-button');
    }

    /**
     * Delete first task in the list
     *
     * @param Browser $browser
     * @return void
     */
    public function deleteFirstTask(Browser $browser)
    {
        $browser->click('@first-delete-button')
                ->waitForDialog()
                ->acceptDialog();
    }
} 