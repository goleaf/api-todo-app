<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class DashboardPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/dashboard';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs('/dashboard')
            ->assertSee('My Tasks');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@searchBox' => '#taskSearch',
            '@taskTable' => 'table',
            '@addTaskButton' => 'button[data-bs-target="#addTaskModal"]',
            '@viewAllButton' => 'a[href*="tasks"]',
            '@clearSearchButton' => '#clearSearch',
        ];
    }

    /**
     * Search for a task
     *
     * @param  string  $search
     * @return void
     */
    public function searchTask(Browser $browser, $search)
    {
        $browser->type('@searchBox', $search)
            ->pause(500); // Wait for search to process
    }

    /**
     * Clear the search field
     *
     * @return void
     */
    public function clearSearch(Browser $browser)
    {
        $browser->click('@clearSearchButton')
            ->pause(200); // Short pause for UI to update
    }

    /**
     * Toggle task completion status
     *
     * @param  int  $taskId
     * @return void
     */
    public function toggleTaskCompletion(Browser $browser, $taskId)
    {
        $browser->check("input#task-{$taskId}")
            ->pause(1000); // Wait for AJAX to complete
    }

    /**
     * Click the "Add New Task" button
     *
     * @return void
     */
    public function clickAddTask(Browser $browser)
    {
        $browser->click('@addTaskButton');
    }

    /**
     * Click the "View All Tasks" button
     *
     * @return void
     */
    public function clickViewAllTasks(Browser $browser)
    {
        $browser->click('@viewAllButton');
    }
}
