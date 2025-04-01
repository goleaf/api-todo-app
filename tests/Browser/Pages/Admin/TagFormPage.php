<?php

namespace Tests\Browser\Pages\Admin;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class TagFormPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/admin/tags/create';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathContains('/admin/tags')
                ->assertSee('Tag Form');
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
            '@color-input' => 'input[name="color"]',
            '@submit-button' => 'button[type="submit"]',
            '@back-button' => 'a.btn-secondary',
        ];
    }

    /**
     * Fill tag form
     *
     * @param Browser $browser
     * @param array $tagData
     * @return void
     */
    public function fillForm(Browser $browser, array $tagData)
    {
        $browser->type('@name-input', $tagData['name'] ?? '');
                
        if (isset($tagData['color'])) {
            $browser->type('@color-input', $tagData['color']);
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
     * Go back to tags list
     *
     * @param Browser $browser
     * @return void
     */
    public function goBack(Browser $browser)
    {
        $browser->click('@back-button');
    }
} 