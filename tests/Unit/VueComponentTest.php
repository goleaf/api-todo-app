<?php

namespace Tests\Unit;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class VueComponentTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function it_shows_todos_list()
    {
        Todo::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'title' => 'Test Todo',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->waitForText('Test Todo')
                ->assertSee('Test Todo');
        });
    }

    /** @test */
    public function it_can_create_a_todo()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->click('@add-todo-button')
                ->waitForRoute('/todos/create')
                ->type('title', 'New Test Todo')
                ->type('description', 'Test Description')
                ->select('priority', '1')
                ->press('Save')
                ->waitForRoute('/')
                ->assertSee('New Test Todo');
        });
    }

    /** @test */
    public function it_can_mark_todo_as_completed()
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Complete Me',
            'completed' => false,
        ]);

        $this->browse(function (Browser $browser) use ($todo) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->waitForText('Complete Me')
                ->click("@complete-todo-{$todo->id}")
                ->waitForTextIn("@todo-{$todo->id}", 'Complete Me')
                ->assertPresent("@todo-completed-{$todo->id}");
        });
    }

    /** @test */
    public function it_shows_todo_details()
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'View Detail Test',
            'description' => 'This is a detailed description',
        ]);

        $this->browse(function (Browser $browser) use ($todo) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->waitForText('View Detail Test')
                ->click("@view-todo-{$todo->id}")
                ->waitForRoute("/todos/{$todo->id}")
                ->assertSee('View Detail Test')
                ->assertSee('This is a detailed description');
        });
    }

    /** @test */
    public function it_can_delete_todo()
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Delete Me',
        ]);

        $this->browse(function (Browser $browser) use ($todo) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->waitForText('Delete Me')
                ->click("@delete-todo-{$todo->id}")
                ->waitForDialog()
                ->acceptDialog()
                ->waitUntilMissing("@todo-{$todo->id}")
                ->assertDontSee('Delete Me');
        });
    }

    /** @test */
    public function it_shows_calendar_view()
    {
        $today = now();
        Todo::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Calendar Test',
            'due_date' => $today->format('Y-m-d H:i:s'),
        ]);

        $this->browse(function (Browser $browser) use ($today) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->click('@calendar-nav')
                ->waitForRoute('/calendar')
                ->assertSee('Task Calendar')
                ->assertSee($today->format('F Y')); // Should show current month/year
        });
    }

    /** @test */
    public function it_shows_stats_view()
    {
        // Create some todos for statistics
        Todo::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => true,
        ]);

        Todo::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->click('@stats-nav')
                ->waitForRoute('/stats')
                ->assertSee('Statistics Dashboard')
                ->assertSee('60%') // 3 out of 5 = 60% completion rate
                ->assertSee('5'); // Total tasks
        });
    }

    /** @test */
    public function it_shows_profile_view()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->click('@profile-nav')
                ->waitForRoute('/profile')
                ->assertSee('Profile')
                ->assertSee('test@example.com');
        });
    }

    /** @test */
    public function it_can_toggle_dark_mode()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->click('@profile-nav')
                ->waitForRoute('/profile')
                ->click('@dark-mode-toggle')
                ->waitFor('.dark')
                ->assertPresent('.dark');
        });
    }

    /** @test */
    public function it_can_log_out()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'test@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->waitForRoute('/')
                ->click('@profile-nav')
                ->waitForRoute('/profile')
                ->click('@logout-button')
                ->waitForRoute('/login')
                ->assertRouteIs('/login');
        });
    }
}
