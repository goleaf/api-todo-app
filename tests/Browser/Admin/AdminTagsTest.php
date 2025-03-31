<?php

namespace Tests\Browser\Admin;

use App\Models\Admin;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\LoginPage;
use Tests\Browser\Pages\Admin\TagFormPage;
use Tests\Browser\Pages\Admin\TagsPage;
use Tests\DuskTestCase;

class AdminTagsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test tags page loads
     *
     * @return void
     */
    public function testTagsPageLoads()
    {
        $admin = Admin::factory()->create();
        Tag::factory()->count(5)->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TagsPage)
                    ->assertSee('Tags List')
                    ->assertSee('Create Tag');
        });
    }

    /**
     * Test creating a new tag
     *
     * @return void
     */
    public function testCreateTag()
    {
        $admin = Admin::factory()->create();
        $tagData = [
            'name' => 'Test Tag ' . time(),
            'color' => '#' . dechex(rand(0x000000, 0xFFFFFF)),
        ];

        $this->browse(function (Browser $browser) use ($admin, $tagData) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TagsPage)
                    ->navigateToCreate()
                    ->on(new TagFormPage)
                    ->fillForm($tagData)
                    ->submit()
                    ->on(new TagsPage)
                    ->assertSee('Tag created successfully')
                    ->assertSee($tagData['name']);
        });
    }

    /**
     * Test viewing a tag
     *
     * @return void
     */
    public function testViewTag()
    {
        $admin = Admin::factory()->create();
        $tag = Tag::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $tag) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TagsPage)
                    ->viewFirstTag()
                    ->assertPathContains('/admin/tags')
                    ->assertSee($tag->name);
        });
    }

    /**
     * Test editing a tag
     *
     * @return void
     */
    public function testEditTag()
    {
        $admin = Admin::factory()->create();
        $tag = Tag::factory()->create();
        $newName = 'Updated Tag Name ' . time();

        $this->browse(function (Browser $browser) use ($admin, $tag, $newName) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TagsPage)
                    ->editFirstTag()
                    ->on(new TagFormPage)
                    ->type('@name-input', $newName)
                    ->submit()
                    ->on(new TagsPage)
                    ->assertSee('Tag updated successfully')
                    ->assertSee($newName);
        });
    }

    /**
     * Test deleting a tag
     *
     * @return void
     */
    public function testDeleteTag()
    {
        $admin = Admin::factory()->create();
        $tag = Tag::factory()->create([
            'name' => 'Tag To Delete ' . time()
        ]);

        $this->browse(function (Browser $browser) use ($admin, $tag) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TagsPage)
                    ->search($tag->name)
                    ->assertSee($tag->name)
                    ->deleteFirstTag()
                    ->assertSee('Tag deleted successfully')
                    ->search($tag->name)
                    ->assertDontSee($tag->name);
        });
    }

    /**
     * Test searching for tags
     *
     * @return void
     */
    public function testSearchTags()
    {
        $admin = Admin::factory()->create();
        $uniqueName = 'Unique Tag ' . time();
        $tag = Tag::factory()->create(['name' => $uniqueName]);
        Tag::factory()->count(5)->create();

        $this->browse(function (Browser $browser) use ($admin, $uniqueName) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TagsPage)
                    ->search($uniqueName)
                    ->assertSee($uniqueName)
                    ->assertDontSee('No tags found');
        });
    }
} 