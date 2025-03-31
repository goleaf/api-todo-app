<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\Dashboard;
use Tests\Browser\Pages\Admin\LoginPage;
use Tests\DuskTestCase;

class AdminDashboardTest extends DuskTestCase
{
    /**
     * Test dashboard page loads after login
     *
     * @return void
     */
    public function testDashboardPageLoads()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->on(new Dashboard)
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
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(new Dashboard)
                    ->navigateToUsers()
                    ->assertPathIs('/admin/users')
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
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(new Dashboard)
                    ->navigateToCategories()
                    ->assertPathIs('/admin/categories')
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
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(new Dashboard)
                    ->navigateToTags()
                    ->assertPathIs('/admin/tags')
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
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(new Dashboard)
                    ->navigateToTasks()
                    ->assertPathIs('/admin/tasks')
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
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(new Dashboard)
                    ->logout()
                    ->assertPathIs('/admin/login');
        });
    }
    
    /**
     * Test dashboard chart data API.
     */
    public function testDashboardChartDataApi()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->pause(1000) // Wait for any JavaScript to load
                    
                    // Directly test the API endpoint
                    ->visit('/admin/dashboard/chart-data')
                    ->assertSee('tasksByStatus')
                    ->assertSee('tasksByPriority')
                    ->assertSee('taskCreation')
                    ->assertSee('topUsers');
        });
    }
} 