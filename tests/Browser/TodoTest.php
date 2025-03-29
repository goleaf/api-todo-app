<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TodoTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test viewing the todo list.
     */
    public function test_view_todo_list(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Work', 'user_id' => $user->id]);

        Todo::create([
            'title' => 'Test Todo Item',
            'description' => 'This is a test todo description',
            'completed' => false,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 1,
            'due_date' => now()->addDays(3)->format('Y-m-d'),
            'progress' => 25,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->assertSee('My Tasks')
                ->assertSee('Test Todo Item')
                ->assertSee('Work');
        });
    }

    /**
     * Test creating a new todo.
     */
    public function test_create_todo(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Personal', 'user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->click('@add-todo-button')
                ->waitFor('#title')
                ->type('title', 'New Todo Test')
                ->type('description', 'This is a test description')
                ->select('category_id', '1')
                ->select('priority', '2')
                ->type('due_date', now()->addWeek()->format('Y-m-d'))
                ->press('Save')
                ->waitForText('New Todo Test')
                ->assertSee('New Todo Test')
                ->assertSee('Personal');
        });
    }

    /**
     * Test completing a todo.
     */
    public function test_complete_todo(): void
    {
        $user = User::factory()->create();
        $todo = Todo::create([
            'title' => 'Todo to Complete',
            'description' => 'This todo will be completed',
            'completed' => false,
            'user_id' => $user->id,
            'priority' => 0,
        ]);

        $this->browse(function (Browser $browser) use ($user, $todo) {
            $browser->loginAs($user)
                ->visit('/')
                ->waitForText('Todo to Complete')
                ->click('#todo-'.$todo->id)
                ->waitUntil('!!document.querySelector("#todo-'.$todo->id + ':checked")')
                ->assertPresent('#todo-'.$todo->id.':checked');

            // Verify the todo is now showing as completed (strikethrough text)
            $browser->assertPresent('.line-through');
        });
    }

    /**
     * Test filtering todos by status.
     */
    public function test_filter_todos(): void
    {
        $user = User::factory()->create();

        // Create completed todo
        Todo::create([
            'title' => 'Completed Todo',
            'completed' => true,
            'user_id' => $user->id,
        ]);

        // Create active todo
        Todo::create([
            'title' => 'Active Todo',
            'completed' => false,
            'user_id' => $user->id,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->assertSee('Completed Todo')
                ->assertSee('Active Todo')

                    // Filter by completed
                ->select('filterOption', 'completed')
                ->waitUntilMissing('@active-todo')
                ->assertSee('Completed Todo')
                ->assertDontSee('Active Todo')

                    // Filter by active
                ->select('filterOption', 'active')
                ->waitUntilMissing('@completed-todo')
                ->assertSee('Active Todo')
                ->assertDontSee('Completed Todo');
        });
    }
}
