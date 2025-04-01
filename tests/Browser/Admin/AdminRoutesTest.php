<?php

namespace Tests\Browser\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminRoutesTest extends DuskTestCase
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
        $this->task = Task::factory()->create();
    }

    public function testAllAdminRoutes()
    {
        $this->browse(function (Browser $browser) {
            // Login
            $browser->visit(route('admin.login'))
                    ->assertSee('Login')
                    ->type('email', 'admin@example.com')
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertRouteIs('admin.dashboard');

            // Dashboard
            $browser->visit(route('admin.dashboard'))
                    ->assertSee('Dashboard')
                    ->assertSee('Recent Activity');

            // Users
            $browser->visit(route('admin.users.index'))
                    ->assertSee('Users')
                    ->click('@create-user')
                    ->assertRouteIs('admin.users.create')
                    ->assertSee('Create User')
                    ->visit(route('admin.users.edit', $this->user))
                    ->assertSee('Edit User')
                    ->visit(route('admin.users.show', $this->user))
                    ->assertSee($this->user->name);

            // Categories
            $browser->visit(route('admin.categories.index'))
                    ->assertSee('Categories')
                    ->click('@create-category')
                    ->assertRouteIs('admin.categories.create')
                    ->assertSee('Create Category')
                    ->visit(route('admin.categories.edit', $this->category))
                    ->assertSee('Edit Category')
                    ->visit(route('admin.categories.show', $this->category))
                    ->assertSee($this->category->name);

            // Tags
            $browser->visit(route('admin.tags.index'))
                    ->assertSee('Tags')
                    ->click('@create-tag')
                    ->assertRouteIs('admin.tags.create')
                    ->assertSee('Create Tag')
                    ->visit(route('admin.tags.edit', $this->tag))
                    ->assertSee('Edit Tag')
                    ->visit(route('admin.tags.show', $this->tag))
                    ->assertSee($this->tag->name);

            // Tasks
            $browser->visit(route('admin.tasks.index'))
                    ->assertSee('Tasks')
                    ->click('@create-task')
                    ->assertRouteIs('admin.tasks.create')
                    ->assertSee('Create Task')
                    ->visit(route('admin.tasks.edit', $this->task))
                    ->assertSee('Edit Task')
                    ->visit(route('admin.tasks.show', $this->task))
                    ->assertSee($this->task->title);

            // Logout
            $browser->click('@logout-button')
                    ->assertRouteIs('admin.login');
        });
    }
} 