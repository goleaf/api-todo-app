<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TodoDetailTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test viewing a todo detail page.
     */
    public function test_view_todo_detail(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Work', 'user_id' => $user->id]);

        $todo = Todo::create([
            'title' => 'Detailed Todo',
            'description' => 'This is a detailed description for testing',
            'completed' => false,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 2,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'progress' => 30,
        ]);

        $this->browse(function (Browser $browser) use ($user, $todo) {
            $browser->loginAs($user)
                ->visit('/todos/'.$todo->id)
                ->assertSee('Detailed Todo')
                ->assertSee('This is a detailed description for testing')
                ->assertSee('Work')
                ->assertSee('30%')
                ->assertPresent('.bg-red-')  // High priority indicator
                ->assertSee('Active');
        });
    }

    /**
     * Test editing a todo from the detail page.
     */
    public function test_edit_todo_from_detail_page(): void
    {
        $user = User::factory()->create();

        $todo = Todo::create([
            'title' => 'Todo to Edit',
            'description' => 'This todo will be edited',
            'completed' => false,
            'user_id' => $user->id,
            'priority' => 0,
            'progress' => 0,
        ]);

        $this->browse(function (Browser $browser) use ($user, $todo) {
            $browser->loginAs($user)
                ->visit('/todos/'.$todo->id)
                ->press('Edit')
                ->waitFor('#editTitle')
                ->type('#editTitle', 'Updated Todo Title')
                ->type('#editDescription', 'Updated description text')
                ->select('#editPriority', '1')  // Change to medium priority
                ->press('Save')
                ->waitForText('Updated Todo Title')
                ->assertSee('Updated Todo Title')
                ->assertSee('Updated description text')
                ->assertPresent('.bg-yellow-');  // Medium priority indicator
        });
    }

    /**
     * Test todo completion toggle on detail page.
     */
    public function test_toggle_completion_on_detail_page(): void
    {
        $user = User::factory()->create();

        $todo = Todo::create([
            'title' => 'Todo to Toggle',
            'description' => 'This todo will be toggled',
            'completed' => false,
            'user_id' => $user->id,
        ]);

        $this->browse(function (Browser $browser) use ($user, $todo) {
            $browser->loginAs($user)
                ->visit('/todos/'.$todo->id)
                ->assertSee('Active')
                ->check('#completeSwitch')
                ->waitForText('Completed')
                ->assertSee('Completed')
                ->assertPresent('.line-through');

            // Toggle back to active
            $browser->uncheck('#completeSwitch')
                ->waitForText('Active')
                ->assertSee('Active')
                ->assertMissing('.line-through');
        });
    }

    /**
     * Test deleting a todo from the detail page.
     */
    public function test_delete_todo_from_detail_page(): void
    {
        $user = User::factory()->create();

        $todo = Todo::create([
            'title' => 'Todo to Delete',
            'description' => 'This todo will be deleted',
            'completed' => false,
            'user_id' => $user->id,
        ]);

        $this->browse(function (Browser $browser) use ($user, $todo) {
            $browser->loginAs($user)
                ->visit('/todos/'.$todo->id)
                ->press('Delete')
                ->waitForDialog()
                ->acceptDialog()
                ->waitForLocation('/')
                ->assertPathIs('/')
                ->assertDontSee('Todo to Delete');
        });
    }
}
