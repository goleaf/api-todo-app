<?php

namespace Tests\Browser\Admin;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\CategoriesPage;
use Tests\Browser\Pages\Admin\CategoryFormPage;
use Tests\Browser\Pages\Admin\LoginPage;
use Tests\DuskTestCase;

class AdminCategoriesTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test categories page loads
     *
     * @return void
     */
    public function testCategoriesPageLoads()
    {
        $admin = Admin::factory()->create();
        Category::factory()->count(5)->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new CategoriesPage)
                    ->assertSee('Categories List')
                    ->assertSee('Create Category');
        });
    }

    /**
     * Test creating a new category
     *
     * @return void
     */
    public function testCreateCategory()
    {
        $admin = Admin::factory()->create();
        $categoryData = [
            'name' => 'Test Category ' . time(),
            'description' => 'This is a test category description',
            'color' => '#' . dechex(rand(0x000000, 0xFFFFFF)),
        ];

        $this->browse(function (Browser $browser) use ($admin, $categoryData) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new CategoriesPage)
                    ->navigateToCreate()
                    ->on(new CategoryFormPage)
                    ->fillForm($categoryData)
                    ->submit()
                    ->on(new CategoriesPage)
                    ->assertSee('Category created successfully')
                    ->assertSee($categoryData['name']);
        });
    }

    /**
     * Test viewing a category
     *
     * @return void
     */
    public function testViewCategory()
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $category) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new CategoriesPage)
                    ->viewFirstCategory()
                    ->assertPathContains('/admin/categories')
                    ->assertSee($category->name)
                    ->assertSee($category->description);
        });
    }

    /**
     * Test editing a category
     *
     * @return void
     */
    public function testEditCategory()
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create();
        $newName = 'Updated Category Name ' . time();

        $this->browse(function (Browser $browser) use ($admin, $category, $newName) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new CategoriesPage)
                    ->editFirstCategory()
                    ->on(new CategoryFormPage)
                    ->type('@name-input', $newName)
                    ->submit()
                    ->on(new CategoriesPage)
                    ->assertSee('Category updated successfully')
                    ->assertSee($newName);
        });
    }

    /**
     * Test deleting a category
     *
     * @return void
     */
    public function testDeleteCategory()
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create([
            'name' => 'Category To Delete ' . time()
        ]);

        $this->browse(function (Browser $browser) use ($admin, $category) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new CategoriesPage)
                    ->search($category->name)
                    ->assertSee($category->name)
                    ->deleteFirstCategory()
                    ->assertSee('Category deleted successfully')
                    ->search($category->name)
                    ->assertDontSee($category->name);
        });
    }

    /**
     * Test searching for categories
     *
     * @return void
     */
    public function testSearchCategories()
    {
        $admin = Admin::factory()->create();
        $uniqueName = 'Unique Category ' . time();
        $category = Category::factory()->create(['name' => $uniqueName]);
        Category::factory()->count(5)->create();

        $this->browse(function (Browser $browser) use ($admin, $uniqueName) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new CategoriesPage)
                    ->search($uniqueName)
                    ->assertSee($uniqueName)
                    ->assertDontSee('No categories found');
        });
    }
} 