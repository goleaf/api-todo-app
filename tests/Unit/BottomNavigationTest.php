<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Tests\TestCase;

class BottomNavigationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_have_four_navigation_items()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/')
                ->assertVisible('@bottom-navigation')
                ->assertSeeIn('@bottom-navigation', 'Home')
                ->assertSeeIn('@bottom-navigation', 'Calendar')
                ->assertSeeIn('@bottom-navigation', 'Stats')
                ->assertSeeIn('@bottom-navigation', 'Profile');
        });
    }

    /** @test */
    public function it_should_highlight_active_route()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/')
                ->assertHasClass('@home-nav-item', 'text-[var(--primary)]')
                ->visit('/calendar')
                ->assertHasClass('@calendar-nav-item', 'text-[var(--primary)]')
                ->visit('/stats')
                ->assertHasClass('@stats-nav-item', 'text-[var(--primary)]')
                ->visit('/profile')
                ->assertHasClass('@profile-nav-item', 'text-[var(--primary)]');
        });
    }

    /** @test */
    public function it_should_navigate_to_correct_routes()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/')
                ->click('@calendar-nav')
                ->assertPathIs('/calendar')
                ->click('@stats-nav')
                ->assertPathIs('/stats')
                ->click('@profile-nav')
                ->assertPathIs('/profile')
                ->click('@home-nav')
                ->assertPathIs('/');
        });
    }

    /** @test */
    public function it_should_only_be_visible_when_authenticated()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertMissing('@bottom-navigation')
                ->visit('/register')
                ->assertMissing('@bottom-navigation');

            $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);

            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->assertVisible('@bottom-navigation');
        });
    }

    /** @test */
    public function it_should_add_padding_to_main_content()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/')
                ->assertHasClass('@main-content', 'pb-20')
                ->visit('/todos/1')
                ->assertHasClass('@todo-detail', 'pb-20')
                ->visit('/stats')
                ->assertHasClass('@stats-view', 'pb-20');
        });
    }
}
