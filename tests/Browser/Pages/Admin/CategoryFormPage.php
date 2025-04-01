<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class CategoryFormPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/admin/categories/create';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathContains('/admin/categories')
                ->assertSee('Category Form');
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
            '@description-textarea' => 'textarea[name="description"]',
            '@color-input' => 'input[name="color"]',
            '@submit-button' => 'button[type="submit"]',
            '@back-button' => 'a.btn-secondary',
        ];
    }

    /**
     * Fill category form
     *
     * @param Browser $browser
     * @param array $categoryData
     * @return void
     */
    public function fillForm(Browser $browser, array $categoryData)
    {
        $browser->type('@name-input', $categoryData['name'] ?? '')
                ->type('@description-textarea', $categoryData['description'] ?? '');
                
        if (isset($categoryData['color'])) {
            $browser->type('@color-input', $categoryData['color']);
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
     * Go back to categories list
     *
     * @param Browser $browser
     * @return void
     */
    public function goBack(Browser $browser)
    {
        $browser->click('@back-button');
    }
} 