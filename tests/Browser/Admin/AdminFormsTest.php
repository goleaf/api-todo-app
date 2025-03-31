<?php

namespace Tests\Browser\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminFormsTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->tag = Tag::factory()->create();
    }

    public function testCategoryFormWorks()
    {
        $this->browse(function (Browser $browser) {
            // Login first
            $browser->visit(route('admin.login'))
                    ->type('email', 'admin@example.com')
                    ->type('password', 'password')
                    ->press('Login');

            // Create a new category
            $browser->visit(route('admin.categories.create'))
                    ->assertSee('Create Category')
                    ->type('name', 'Test Category')
                    ->type('description', 'Test Description')
                    ->select('color', '#3498db')
                    ->check('is_active')
                    ->press('Save')
                    ->assertRouteIs('admin.categories.index')
                    ->assertSee('Test Category')
                    ->assertSee('Category created successfully');

            // Edit the category
            $categoryId = Category::where('name', 'Test Category')->first()->id;
            $browser->visit(route('admin.categories.edit', $categoryId))
                    ->assertSee('Edit Category')
                    ->assertInputValue('name', 'Test Category')
                    ->type('name', 'Updated Category')
                    ->press('Save')
                    ->assertRouteIs('admin.categories.index')
                    ->assertSee('Updated Category')
                    ->assertSee('Category updated successfully');
        });
    }

    public function testTagFormWorks()
    {
        $this->browse(function (Browser $browser) {
            // Login first
            $browser->visit(route('admin.login'))
                    ->type('email', 'admin@example.com')
                    ->type('password', 'password')
                    ->press('Login');

            // Create a new tag
            $browser->visit(route('admin.tags.create'))
                    ->assertSee('Create Tag')
                    ->type('name', 'Test Tag')
                    ->type('description', 'Test Description')
                    ->select('color', '#3498db')
                    ->check('is_active')
                    ->press('Save')
                    ->assertRouteIs('admin.tags.index')
                    ->assertSee('Test Tag')
                    ->assertSee('Tag created successfully');

            // Edit the tag
            $tagId = Tag::where('name', 'Test Tag')->first()->id;
            $browser->visit(route('admin.tags.edit', $tagId))
                    ->assertSee('Edit Tag')
                    ->assertInputValue('name', 'Test Tag')
                    ->type('name', 'Updated Tag')
                    ->press('Save')
                    ->assertRouteIs('admin.tags.index')
                    ->assertSee('Updated Tag')
                    ->assertSee('Tag updated successfully');
        });
    }

    public function testUserFormWorks()
    {
        $this->browse(function (Browser $browser) {
            // Login first
            $browser->visit(route('admin.login'))
                    ->type('email', 'admin@example.com')
                    ->type('password', 'password')
                    ->press('Login');

            // Create a new user
            $browser->visit(route('admin.users.create'))
                    ->assertSee('Create User')
                    ->type('name', 'Test User')
                    ->type('email', 'testuser@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->select('role', 'USER')
                    ->press('Save')
                    ->assertRouteIs('admin.users.index')
                    ->assertSee('Test User')
                    ->assertSee('User created successfully');

            // Edit the user
            $userId = User::where('email', 'testuser@example.com')->first()->id;
            $browser->visit(route('admin.users.edit', $userId))
                    ->assertSee('Edit User')
                    ->assertInputValue('name', 'Test User')
                    ->type('name', 'Updated User')
                    ->press('Save')
                    ->assertRouteIs('admin.users.index')
                    ->assertSee('Updated User')
                    ->assertSee('User updated successfully');
        });
    }

    public function testTaskFormWorks()
    {
        $this->browse(function (Browser $browser) {
            // Login first
            $browser->visit(route('admin.login'))
                    ->type('email', 'admin@example.com')
                    ->type('password', 'password')
                    ->press('Login');

            // Create a new task
            $browser->visit(route('admin.tasks.create'))
                    ->assertSee('Create Task')
                    ->type('title', 'Test Task')
                    ->type('description', 'Test Description')
                    ->select('user_id', $this->user->id)
                    ->select('priority', 'MEDIUM')
                    ->select('status', 'TODO')
                    ->press('Save')
                    ->assertPathBeginsWith('/admin/tasks')
                    ->assertSee('Task created successfully');
        });
    }
} 