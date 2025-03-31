<?php

namespace Tests\Browser\Admin;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TagCrudTest extends DuskTestCase
{
    /**
     * Test tag listing page.
     */
    public function testTagListingPage()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/tags')
                    ->assertSee('Tags')
                    ->assertSee('Add Tag')
                    ->assertSee('ID')
                    ->assertSee('Name')
                    ->assertSee('User')
                    ->assertSee('Color')
                    ->assertSee('Tasks')
                    ->assertSee('Actions');
        });
    }

    /**
     * Test create tag form.
     */
    public function testCreateTagForm()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/tags')
                    ->clickLink('Add Tag')
                    ->assertPathIs('/admin/tags/create')
                    ->assertSee('Create New Tag')
                    ->assertSee('Name')
                    ->assertSee('User')
                    ->assertSee('Color');
        });
    }

    /**
     * Test tag creation.
     */
    public function testTagCreation()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        $tagName = 'Test Tag ' . time();

        $this->browse(function (Browser $browser) use ($admin, $user, $tagName) {
            $browser->loginAs($admin)
                    ->visit('/admin/tags/create')
                    ->type('name', $tagName)
                    ->select('user_id', $user->id)
                    ->type('color', '#FF5733')
                    ->press('Save')
                    ->waitForLocation('/admin/tags')
                    ->assertSee('Tag created successfully')
                    ->assertSee($tagName);
        });

        // Clean up
        Tag::where('name', $tagName)->delete();
    }

    /**
     * Test edit tag form.
     */
    public function testEditTagForm()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        // Create a test tag
        $tag = Tag::create([
            'name' => 'Tag for Edit Test',
            'user_id' => $user->id,
            'color' => '#FF5733'
        ]);

        $this->browse(function (Browser $browser) use ($admin, $tag) {
            $browser->loginAs($admin)
                    ->visit('/admin/tags')
                    ->click('@edit-tag-' . $tag->id)
                    ->assertPathIs('/admin/tags/' . $tag->id . '/edit')
                    ->assertSee('Edit Tag')
                    ->assertInputValue('name', $tag->name)
                    ->assertSelected('user_id', $tag->user_id)
                    ->assertInputValue('color', $tag->color);
        });

        // Clean up
        $tag->delete();
    }

    /**
     * Test tag update.
     */
    public function testTagUpdate()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        // Create a test tag
        $tag = Tag::create([
            'name' => 'Tag for Update Test',
            'user_id' => $user->id,
            'color' => '#FF5733'
        ]);

        $newName = 'Updated Tag ' . time();

        $this->browse(function (Browser $browser) use ($admin, $tag, $newName) {
            $browser->loginAs($admin)
                    ->visit('/admin/tags/' . $tag->id . '/edit')
                    ->clear('name')
                    ->type('name', $newName)
                    ->type('color', '#33FF57')
                    ->press('Save')
                    ->waitForLocation('/admin/tags')
                    ->assertSee('Tag updated successfully')
                    ->assertSee($newName);
        });

        // Clean up
        $tag->delete();
    }

    /**
     * Test tag deletion.
     */
    public function testTagDeletion()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        // Create a test tag
        $tag = Tag::create([
            'name' => 'Tag for Delete Test',
            'user_id' => $user->id,
            'color' => '#FF5733'
        ]);

        $this->browse(function (Browser $browser) use ($admin, $tag) {
            $browser->loginAs($admin)
                    ->visit('/admin/tags')
                    ->assertSee($tag->name)
                    ->click('@delete-tag-' . $tag->id)
                    ->waitForDialog()
                    ->acceptDialog()
                    ->waitForText('Tag deleted successfully')
                    ->assertSee('Tag deleted successfully')
                    ->assertDontSee($tag->name);
        });

        // No need to clean up as the tag should have been deleted by the test
    }
} 