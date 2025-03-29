<?php

namespace Tests\Browser;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TaskTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create some tasks for the user
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test that tasks are displayed on the dashboard
     *
     * @return void
     */
    public function test_tasks_displayed_on_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->waitFor('table')
                ->assertSee('My Tasks')
                ->assertPresent('table tbody tr');
        });
    }

    /**
     * Test task completion checkbox functionality
     *
     * @return void
     */
    public function test_task_completion_checkbox()
    {
        $task = Task::where('user_id', $this->user->id)->first();

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->waitFor('table')
                ->check("input#task-{$task->id}")
                ->pause(1000) // Wait for AJAX to complete
                ->assertPresent("input#task-{$task->id}:checked");

            // Refresh and check if state persisted
            $browser->refresh()
                ->waitFor('table')
                ->assertPresent("input#task-{$task->id}:checked");
        });
    }

    /**
     * Test tasks search functionality
     *
     * @return void
     */
    public function test_task_search()
    {
        $task = Task::where('user_id', $this->user->id)->first();
        $searchTerm = substr($task->title, 0, 5); // Use part of the title as search term

        $this->browse(function (Browser $browser) use ($searchTerm) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->waitFor('#taskSearch')
                ->type('#taskSearch', $searchTerm)
                ->pause(500) // Wait for search to process
                ->assertPresent('tr.task-row:not(.d-none)'); // At least one visible task
        });
    }

    /**
     * Test dedicated tasks page
     *
     * @return void
     */
    public function test_tasks_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/tasks')
                ->waitFor('table')
                ->assertSee('All Tasks')
                ->assertPresent('table tbody tr');
        });
    }
}
