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
                    ->assertSee('Dashboard');
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
                    ->visit(new DashboardPage)
                    ->navigateToUsers()
                    ->assertPathIs(route('admin.users.index', [], false))
                    ->assertSee('Users');
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
                    ->visit(new DashboardPage)
                    ->navigateToCategories()
                    ->assertPathIs(route('admin.categories.index', [], false))
                    ->assertSee('Categories');
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
                    ->visit(new DashboardPage)
                    ->navigateToTags()
                    ->assertPathIs(route('admin.tags.index', [], false))
                    ->assertSee('Tags');
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
                    ->visit(new DashboardPage)
                    ->navigateToTasks()
                    ->assertPathIs(route('admin.tasks.index', [], false))
                    ->assertSee('Tasks');
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
                    ->visit(new DashboardPage)
                    ->logout()
                    ->assertPathIs(route('admin.login', [], false));
        });
    }
    
    /**
     * Test dashboard chart data API.
     */
    public function testDashboardChartDataApi()
    {
        $admin = Admin::factory()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(route('admin.dashboard'))
                    ->pause(1000) // Wait for any JavaScript to load
                    
                    // Directly test the API endpoint
                    ->visit(route('admin.dashboard.chart-data'))
                    ->assertSee('tasksByStatus')
                    ->assertSee('tasksByPriority')
                    ->assertSee('taskCreation')
                    ->assertSee('topUsers');
        });
    }
} 