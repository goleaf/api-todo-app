<?php

namespace Tests\Browser\Admin;

use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;

class DashboardTest extends AdminTestCase
{
    use WithFaker;

    /**
     * Test if the admin can view the dashboard
     *
     * @return void
     */
    public function test_admin_can_view_dashboard()
    {
        $admin = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($admin) {
            $this->loginAdmin($browser)
                ->assertPathIs('/admin/dashboard')
                ->assertSee('Dashboard')
                ->assertSee('Tasks')
                ->assertSee('Categories')
                ->assertSee('Tags')
                ->assertSee('Users');
        });
    }

    /**
     * Test if dashboard statistics are loaded
     *
     * @return void
     */
    public function test_dashboard_statistics_are_loaded()
    {
        $admin = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($admin) {
            $this->loginAdmin($browser)
                ->waitFor('.card-stats')
                ->assertVisible('.card-stats')
                ->assertSee('Total Tasks')
                ->assertSee('Total Categories')
                ->assertSee('Total Tags')
                ->assertSee('Total Users');
        });
    }

    /**
     * Test if dashboard chart is loaded
     *
     * @return void
     */
    public function test_dashboard_chart_is_loaded()
    {
        $admin = $this->createAdminUser();

        $this->browse(function (Browser $browser) use ($admin) {
            $this->loginAdmin($browser)
                ->waitFor('.chart-container')
                ->assertVisible('.chart-container');
        });
    }
} 