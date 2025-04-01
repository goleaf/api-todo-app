<?php

namespace Tests\Browser\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;

class CategoryTest extends AdminTestCase
{
    use WithFaker;

    /**
     * Test if admin can view categories list
     *
     * @return void
     */
    public function test_admin_can_view_categories_list()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Category',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $this->loginAdmin($browser)
                ->clickLink('Categories')
                ->assertPathIs('/admin/categories')
                ->assertSee('Category Management')
                ->assertSee('Test Category');
        });
    }

    /**
     * Test if admin can view category details
     *
     * @return void
     */
    public function test_admin_can_view_category_details()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Category Details',
            'description' => 'Test category description',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $category) {
            $this->loginAdmin($browser)
                ->clickLink('Categories')
                ->assertPathIs('/admin/categories')
                ->click('@view-category-' . $category->id)
                ->assertPathIs('/admin/categories/' . $category->id)
                ->assertSee('Test Category Details')
                ->assertSee('Test category description');
        });
    }

    /**
     * Test if admin can create a new category
     *
     * @return void
     */
    public function test_admin_can_create_category()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create(['name' => 'Test User']);
        
        $categoryName = 'New Category ' . $this->faker->word;
        $categoryDescription = $this->faker->sentence;

        $this->browse(function (Browser $browser) use ($admin, $user, $categoryName, $categoryDescription) {
            $this->loginAdmin($browser)
                ->clickLink('Categories')
                ->assertPathIs('/admin/categories')
                ->clickLink('Create Category')
                ->assertPathIs('/admin/categories/create')
                ->select('user_id', $user->id)
                ->type('name', $categoryName)
                ->type('description', $categoryDescription)
                ->press('Save')
                ->waitForLocation('/admin/categories')
                ->assertSee($categoryName);
        });
    }

    /**
     * Test if admin can edit a category
     *
     * @return void
     */
    public function test_admin_can_edit_category()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Category to Edit',
        ]);
        
        $updatedName = 'Updated Category ' . $this->faker->word;

        $this->browse(function (Browser $browser) use ($admin, $category, $updatedName) {
            $this->loginAdmin($browser)
                ->clickLink('Categories')
                ->assertPathIs('/admin/categories')
                ->click('@edit-category-' . $category->id)
                ->assertPathIs('/admin/categories/' . $category->id . '/edit')
                ->assertInputValue('name', 'Category to Edit')
                ->type('name', $updatedName)
                ->press('Save')
                ->waitForLocation('/admin/categories')
                ->assertSee($updatedName);
        });
    }

    /**
     * Test if admin can delete a category
     *
     * @return void
     */
    public function test_admin_can_delete_category()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Category to Delete',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $category) {
            $this->loginAdmin($browser)
                ->clickLink('Categories')
                ->assertPathIs('/admin/categories')
                ->assertSee('Category to Delete')
                ->click('@delete-category-' . $category->id)
                ->waitForDialog()
                ->acceptDialog()
                ->waitUntilMissing('@delete-category-' . $category->id)
                ->assertDontSee('Category to Delete');
        });
    }
} 