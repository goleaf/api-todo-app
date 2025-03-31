<?php

namespace Tests\Browser\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CategoryCrudTest extends DuskTestCase
{
    /**
     * Test category listing page.
     */
    public function testCategoryListingPage()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.categories.index'))
                    ->assertSee('Categories')
                    ->assertSee('Add Category')
                    ->assertSee('ID')
                    ->assertSee('Name')
                    ->assertSee('Type')
                    ->assertSee('User')
                    ->assertSee('Color')
                    ->assertSee('Tasks')
                    ->assertSee('Actions');
        });
    }

    /**
     * Test create category form.
     */
    public function testCreateCategoryForm()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.categories.index'))
                    ->clickLink('Add Category')
                    ->assertPathIs(route('admin.categories.create', [], false))
                    ->assertSee('Create New Category')
                    ->assertSee('Name')
                    ->assertSee('User')
                    ->assertSee('Type')
                    ->assertSee('Color');
        });
    }

    /**
     * Test category creation.
     */
    public function testCategoryCreation()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        $categoryName = 'Test Category ' . time();

        $this->browse(function (Browser $browser) use ($admin, $user, $categoryName) {
            $browser->loginAs($admin)
                    ->visit(route('admin.categories.create'))
                    ->type('name', $categoryName)
                    ->select('user_id', $user->id)
                    ->select('type', 'work')
                    ->type('color', '#3355FF')
                    ->press('Save')
                    ->waitForLocation(route('admin.categories.index'))
                    ->assertSee('Category created successfully')
                    ->assertSee($categoryName);
        });

        // Clean up
        Category::where('name', $categoryName)->delete();
    }

    /**
     * Test edit category form.
     */
    public function testEditCategoryForm()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        // Create a test category
        $category = Category::create([
            'name' => 'Category for Edit Test',
            'user_id' => $user->id,
            'type' => 'work',
            'color' => '#3355FF'
        ]);

        $this->browse(function (Browser $browser) use ($admin, $category) {
            $browser->loginAs($admin)
                    ->visit(route('admin.categories.index'))
                    ->click('@edit-category-' . $category->id)
                    ->assertPathIs(route('admin.categories.edit', $category->id, false))
                    ->assertSee('Edit Category')
                    ->assertInputValue('name', $category->name)
                    ->assertSelected('user_id', $category->user_id)
                    ->assertSelected('type', $category->type)
                    ->assertInputValue('color', $category->color);
        });

        // Clean up
        $category->delete();
    }

    /**
     * Test category update.
     */
    public function testCategoryUpdate()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        // Create a test category
        $category = Category::create([
            'name' => 'Category for Update Test',
            'user_id' => $user->id,
            'type' => 'work',
            'color' => '#3355FF'
        ]);

        $newName = 'Updated Category ' . time();

        $this->browse(function (Browser $browser) use ($admin, $category, $newName) {
            $browser->loginAs($admin)
                    ->visit(route('admin.categories.edit', $category->id))
                    ->clear('name')
                    ->type('name', $newName)
                    ->select('type', 'personal')
                    ->type('color', '#FF5733')
                    ->press('Save')
                    ->waitForLocation(route('admin.categories.index'))
                    ->assertSee('Category updated successfully')
                    ->assertSee($newName);
        });

        // Clean up
        $category->delete();
    }

    /**
     * Test category deletion.
     */
    public function testCategoryDeletion()
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        if (!$admin || !$user) {
            $this->markTestSkipped('Admin or user not found in the database');
        }

        // Create a test category
        $category = Category::create([
            'name' => 'Category for Delete Test',
            'user_id' => $user->id,
            'type' => 'work',
            'color' => '#3355FF'
        ]);

        $this->browse(function (Browser $browser) use ($admin, $category) {
            $browser->loginAs($admin)
                    ->visit(route('admin.categories.index'))
                    ->assertSee($category->name)
                    ->click('@delete-category-' . $category->id)
                    ->waitForDialog()
                    ->acceptDialog()
                    ->waitForText('Category deleted successfully')
                    ->assertSee('Category deleted successfully')
                    ->assertDontSee($category->name);
        });

        // No need to clean up as the category should have been deleted by the test
    }
} 