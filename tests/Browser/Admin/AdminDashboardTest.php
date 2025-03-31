<?php

namespace Tests\Browser\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\DashboardPage;
use Tests\Browser\Pages\Admin\LoginPage;
use Tests\DuskTestCase;

class AdminDashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test dashboard page loads after login
     *
     * @return void
     */
    public function testDashboardPageLoads()
    {
        $admin = Admin::factory()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->on(new DashboardPage)
                    ->assertSee('Dashboard')
                    ->assertSee('Users')
                    ->assertSee('Categories')
                    ->assertSee('Tags')
                    ->assertSee('Tasks');
        });
    }

    /**
     * Test navigation to users page
     *
     * @return void
     */
    public function testNavigationToUsers()
    {
        $admin = Admin::factory()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->on(new DashboardPage)
                    ->navigateToUsers()
                    ->assertPathIs('/admin/users')
                    ->assertSee('Users List');
        });
    }

    /**
     * Test navigation to categories page
     *
     * @return void
     */
    public function testNavigationToCategories()
    {
        $admin = Admin::factory()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->on(new DashboardPage)
                    ->navigateToCategories()
                    ->assertPathIs('/admin/categories')
                    ->assertSee('Categories List');
        });
    }

    /**
     * Test navigation to tags page
     *
     * @return void
     */
    public function testNavigationToTags()
    {
        $admin = Admin::factory()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->on(new DashboardPage)
                    ->navigateToTags()
                    ->assertPathIs('/admin/tags')
                    ->assertSee('Tags List');
        });
    }

    /**
     * Test navigation to tasks page
     *
     * @return void
     */
    public function testNavigationToTasks()
    {
        $admin = Admin::factory()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->on(new DashboardPage)
                    ->navigateToTasks()
                    ->assertPathIs('/admin/tasks')
                    ->assertSee('Tasks List');
        });
    }

    /**
     * Test admin logout
     *
     * @return void
     */
    public function testAdminLogout()
    {
        $admin = Admin::factory()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->on(new DashboardPage)
                    ->logout()
                    ->assertPathIs('/admin/login');
        });
    }
} 