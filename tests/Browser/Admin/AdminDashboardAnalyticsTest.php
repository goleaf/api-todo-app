<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\Dashboard;
use Tests\DuskTestCase;

class AdminDashboardAnalyticsTest extends DuskTestCase
{
    /**
     * Test dashboard statistics cards are displayed.
     *
     * @return void
     */
    public function testDashboardStatisticsCardsAreDisplayed()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(new Dashboard)
                    ->assertVisible('@users-card')
                    ->assertVisible('@tasks-card')
                    ->assertVisible('@categories-card')
                    ->assertVisible('@tags-card');
        });
    }

    /**
     * Test dashboard chart data endpoint returns valid JSON.
     *
     * @return void
     */
    public function testDashboardChartDataEndpoint()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard/chart-data')
                    ->assertSee('tasksByStatus')
                    ->assertSee('tasksByPriority')
                    ->assertSee('taskCreation')
                    ->assertSee('topUsers');
        });
    }

    /**
     * Test dashboard chart data with different period parameters.
     *
     * @return void
     */
    public function testDashboardChartDataWithPeriodParameter()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            // Test with week period
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard/chart-data?period=week')
                    ->assertSee('tasksByStatus')
                    ->assertSee('taskCreation')
                    ->assertSee('labels');

            // Test with month period
            $browser->visit('/admin/dashboard/chart-data?period=month')
                    ->assertSee('tasksByStatus')
                    ->assertSee('taskCreation')
                    ->assertSee('labels');

            // Test with year period
            $browser->visit('/admin/dashboard/chart-data?period=year')
                    ->assertSee('tasksByStatus')
                    ->assertSee('taskCreation')
                    ->assertSee('labels');
        });
    }

    /**
     * Test refresh dashboard button functionality.
     *
     * @return void
     */
    public function testRefreshDashboardButton()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(new Dashboard)
                    ->assertVisible('@refresh-button')
                    ->click('@refresh-button')
                    ->pause(1000) // Wait for refresh animation
                    ->assertVisible('@users-card'); // Check that the dashboard is still visible
        });
    }
} 