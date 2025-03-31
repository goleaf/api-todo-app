<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class TaskFormPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/admin/tasks/create';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathContains('/admin/tasks')
                ->assertSee('Task Form');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@title-input' => 'input[name="title"]',
            '@description-textarea' => 'textarea[name="description"]',
            '@status-select' => 'select[name="status"]',
            '@category-select' => 'select[name="category_id"]',
            '@user-select' => 'select[name="user_id"]',
            '@due-date-input' => 'input[name="due_date"]',
            '@priority-select' => 'select[name="priority"]',
            '@tags-select' => 'select[name="tags[]"]',
            '@submit-button' => 'button[type="submit"]',
            '@back-button' => 'a.btn-secondary',
        ];
    }

    /**
     * Fill task form
     *
     * @param Browser $browser
     * @param array $taskData
     * @return void
     */
    public function fillForm(Browser $browser, array $taskData)
    {
        $browser->type('@title-input', $taskData['title'] ?? '')
                ->type('@description-textarea', $taskData['description'] ?? '');
                
        if (isset($taskData['status'])) {
            $browser->select('@status-select', $taskData['status']);
        }
        
        if (isset($taskData['category_id'])) {
            $browser->select('@category-select', $taskData['category_id']);
        }
        
        if (isset($taskData['user_id'])) {
            $browser->select('@user-select', $taskData['user_id']);
        }
        
        if (isset($taskData['due_date'])) {
            $browser->type('@due-date-input', $taskData['due_date']);
        }
        
        if (isset($taskData['priority'])) {
            $browser->select('@priority-select', $taskData['priority']);
        }
        
        if (isset($taskData['tags']) && is_array($taskData['tags'])) {
            foreach ($taskData['tags'] as $tagId) {
                $browser->select('@tags-select', $tagId);
            }
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
     * Go back to tasks list
     *
     * @param Browser $browser
     * @return void
     */
    public function goBack(Browser $browser)
    {
        $browser->click('@back-button');
    }
} 