<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\File;

class TranslationManagementTest extends DuskTestCase
{
    /**
     * Test translation listing page.
     */
    public function testTranslationListingPage()
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.translations.index'))
                    ->assertSee('Translation Management')
                    ->assertSee('Available Locales')
                    ->assertSee('Translation Tools')
                    ->assertSee('Find Missing Translations')
                    ->assertSee('Check Unused Translations')
                    ->assertSee('Add New Translation');
        });
    }

    /**
     * Test translation create form.
     */
    public function testTranslationCreateForm()
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.translations.index'))
                    ->click('.btn-primary')
                    ->assertPathIs(route('admin.translations.create', [], false))
                    ->assertSee('Create New Translation')
                    ->assertSee('Locale')
                    ->assertSee('File Name')
                    ->assertSee('Translation Content (JSON)')
                    ->assertSee('Create Translation');
        });
    }

    /**
     * Test translation creation.
     */
    public function testTranslationCreation()
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $locale = 'test_' . time();
        $fileName = 'test';
        $content = '{"hello": "Hello World", "welcome": "Welcome to our app"}';
        $filePath = resource_path("lang/{$locale}/{$fileName}.php");

        $this->browse(function (Browser $browser) use ($admin, $locale, $fileName, $content) {
            $browser->loginAs($admin)
                    ->visit(route('admin.translations.create'))
                    ->type('locale', $locale)
                    ->type('file', $fileName)
                    ->type('content', $content)
                    ->press('Create Translation')
                    ->waitForLocation(route('admin.translations.edit', ['locale' => $locale, 'file' => $fileName], false))
                    ->assertSee('Translation file \'' . $fileName . '.php\' created successfully')
                    ->assertSee($locale)
                    ->assertSee('hello')
                    ->assertSee('Hello World')
                    ->assertSee('welcome')
                    ->assertSee('Welcome to our app');
        });

        // Clean up
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
        if (File::exists(resource_path("lang/{$locale}"))) {
            File::deleteDirectory(resource_path("lang/{$locale}"));
        }
    }

    /**
     * Test editing translations.
     */
    public function testEditingTranslations()
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        // Ensure English locale exists
        if (!File::exists(resource_path('lang/en'))) {
            $this->markTestSkipped('English locale directory not found');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.translations.index'))
                    ->clickLink('Edit', 0) // Click first Edit link
                    ->assertPathContains('/admin/translations/en/edit')
                    ->assertSee('Edit Translations: en');
                    
            // Now test file selection if a file exists
            $browser->whenAvailable('.list-group-item-action', function($element) {
                $element->click();
            }, function() {
                // Skip if no files are available
            });
            
            // If we have translation fields, try to edit one
            $browser->whenAvailable('input[name^="translations"]', function($element) {
                $currentValue = $element->getAttribute('value');
                $element->clear()->type('', $currentValue . ' (edited)');
            }, function() {
                // Skip if no translation fields
            });
            
            // Try to save if we have a save button
            $browser->whenAvailable('.btn-success', function($element) {
                $element->click();
                $this->assertSee('Translations updated successfully');
            }, function() {
                // Skip if no save button
            });
        });
    }

    /**
     * Test missing translations page.
     */
    public function testMissingTranslationsPage()
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.translations.index'))
                    ->select('reference', 'en')
                    ->press('Find')
                    ->assertPathIs(route('admin.translations.missing', [], false))
                    ->assertSee('Missing Translations');
        });
    }

    /**
     * Test unused translations page.
     */
    public function testUnusedTranslationsPage()
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.translations.index'))
                    ->clickLink('Find Unused')
                    ->assertPathIs(route('admin.translations.unused', [], false))
                    ->assertSee('Unused Translations');
        });
    }
} 