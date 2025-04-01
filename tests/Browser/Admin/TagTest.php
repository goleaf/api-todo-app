<?php

namespace Tests\Browser\Admin;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;

class TagTest extends AdminTestCase
{
    use WithFaker;

    /**
     * Test if admin can view tags list
     *
     * @return void
     */
    public function test_admin_can_view_tags_list()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Tag',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $this->loginAdmin($browser)
                ->clickLink('Tags')
                ->assertPathIs('/admin/tags')
                ->assertSee('Tag Management')
                ->assertSee('Test Tag');
        });
    }

    /**
     * Test if admin can view tag details
     *
     * @return void
     */
    public function test_admin_can_view_tag_details()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Tag Details',
            'description' => 'Test tag description',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $tag) {
            $this->loginAdmin($browser)
                ->clickLink('Tags')
                ->assertPathIs('/admin/tags')
                ->click('@view-tag-' . $tag->id)
                ->assertPathIs('/admin/tags/' . $tag->id)
                ->assertSee('Test Tag Details')
                ->assertSee('Test tag description');
        });
    }

    /**
     * Test if admin can create a new tag
     *
     * @return void
     */
    public function test_admin_can_create_tag()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create(['name' => 'Test User']);
        
        $tagName = 'New Tag ' . $this->faker->word;
        $tagDescription = $this->faker->sentence;

        $this->browse(function (Browser $browser) use ($admin, $user, $tagName, $tagDescription) {
            $this->loginAdmin($browser)
                ->clickLink('Tags')
                ->assertPathIs('/admin/tags')
                ->clickLink('Create Tag')
                ->assertPathIs('/admin/tags/create')
                ->select('user_id', $user->id)
                ->type('name', $tagName)
                ->type('description', $tagDescription)
                ->press('Save')
                ->waitForLocation('/admin/tags')
                ->assertSee($tagName);
        });
    }

    /**
     * Test if admin can edit a tag
     *
     * @return void
     */
    public function test_admin_can_edit_tag()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
            'name' => 'Tag to Edit',
        ]);
        
        $updatedName = 'Updated Tag ' . $this->faker->word;

        $this->browse(function (Browser $browser) use ($admin, $tag, $updatedName) {
            $this->loginAdmin($browser)
                ->clickLink('Tags')
                ->assertPathIs('/admin/tags')
                ->click('@edit-tag-' . $tag->id)
                ->assertPathIs('/admin/tags/' . $tag->id . '/edit')
                ->assertInputValue('name', 'Tag to Edit')
                ->type('name', $updatedName)
                ->press('Save')
                ->waitForLocation('/admin/tags')
                ->assertSee($updatedName);
        });
    }

    /**
     * Test if admin can delete a tag
     *
     * @return void
     */
    public function test_admin_can_delete_tag()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $tag = Tag::factory()->create([
            'user_id' => $user->id,
            'name' => 'Tag to Delete',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $tag) {
            $this->loginAdmin($browser)
                ->clickLink('Tags')
                ->assertPathIs('/admin/tags')
                ->assertSee('Tag to Delete')
                ->click('@delete-tag-' . $tag->id)
                ->waitForDialog()
                ->acceptDialog()
                ->waitUntilMissing('@delete-tag-' . $tag->id)
                ->assertDontSee('Tag to Delete');
        });
    }
} 