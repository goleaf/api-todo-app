<?php

namespace Tests\Browser\Admin;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use App\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TaskCrudTest extends DuskTestCase
{
    /**
     * Test task listing page.
     */
    public function testTaskListingPage()
    {
        $admin = User::withRole(UserRole::ADMIN)->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.tasks.index'))
                    ->assertSee('Tasks')
                    ->assertSee('Add Task')
                    ->assertSee('ID')
                    ->assertSee('Title')
                    ->assertSee('User')
                    ->assertSee('Category')
                    ->assertSee('Priority')
                    ->assertSee('Due Date')
                    ->assertSee('Status')
                    ->assertSee('Progress')
                    ->assertSee('Actions');
        });
    }

    /**
     * Test create task form.
     */
    public function testCreateTaskForm()
    {
        $admin = User::withRole(UserRole::ADMIN)->first();

        if (!$admin) {
            $this->markTestSkipped('No admin user found in the database');
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit(route('admin.tasks.index'))
                    ->clickLink('Add Task')
                    ->assertPathIs(route('admin.tasks.create', [], false))
                    ->assertSee('Create New Task')
                    ->assertSee('Title')
                    ->assertSee('Description')
                    ->assertSee('User')
                    ->assertSee('Category')
                    ->assertSee('Priority')
                    ->assertSee('Due Date')
                    ->assertSee('Progress');
        });
    }

    /**
     * Test task creation.
     */
    public function testTaskCreation()
    {
        $admin = User::withRole(UserRole::ADMIN)->first();
        $user = User::withRole(UserRole::USER)->first();
        $category = Category::first();

        if (!$admin || !$user || !$category) {
            $this->markTestSkipped('Admin, user, or category not found in the database');
        }

        $taskTitle = 'Test Task ' . time();
        $dueDate = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function (Browser $browser) use ($admin, $user, $category, $taskTitle, $dueDate) {
            $browser->loginAs($admin)
                    ->visit(route('admin.tasks.create'))
                    ->type('title', $taskTitle)
                    ->type('description', 'This is a test task description')
                    ->select('user_id', $user->id)
                    ->select('category_id', $category->id)
                    ->select('priority', 'medium')
                    ->type('due_date', $dueDate)
                    ->type('progress', '50')
                    ->press('Save')
                    ->waitForLocation(route('admin.tasks.index', [], false))
                    ->assertSee('Task created successfully')
                    ->assertSee($taskTitle);
        });

        // Clean up
        Task::where('title', $taskTitle)->delete();
    }

    /**
     * Test edit task form.
     */
    public function testEditTaskForm()
    {
        $admin = User::withRole(UserRole::ADMIN)->first();
        $user = User::withRole(UserRole::USER)->first();
        $category = Category::first();

        if (!$admin || !$user || !$category) {
            $this->markTestSkipped('Admin, user, or category not found in the database');
        }

        // Create a test task
        $task = Task::create([
            'title' => 'Task for Edit Test',
            'description' => 'This is a test task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 'medium',
            'due_date' => Carbon::now()->addDays(7),
            'progress' => 50
        ]);

        $this->browse(function (Browser $browser) use ($admin, $task) {
            $browser->loginAs($admin)
                    ->visit(route('admin.tasks.index'))
                    ->click('@edit-task-' . $task->id)
                    ->assertPathIs(route('admin.tasks.edit', $task->id, false))
                    ->assertSee('Edit Task')
                    ->assertInputValue('title', $task->title)
                    ->assertInputValue('description', $task->description)
                    ->assertSelected('user_id', $task->user_id)
                    ->assertSelected('category_id', $task->category_id)
                    ->assertSelected('priority', $task->priority)
                    ->assertInputValue('progress', $task->progress);
        });

        // Clean up
        $task->delete();
    }

    /**
     * Test task update.
     */
    public function testTaskUpdate()
    {
        $admin = User::withRole(UserRole::ADMIN)->first();
        $user = User::withRole(UserRole::USER)->first();
        $category = Category::first();

        if (!$admin || !$user || !$category) {
            $this->markTestSkipped('Admin, user, or category not found in the database');
        }

        // Create a test task
        $task = Task::create([
            'title' => 'Task for Update Test',
            'description' => 'This is a test task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 'medium',
            'due_date' => Carbon::now()->addDays(7),
            'progress' => 50
        ]);

        $newTitle = 'Updated Task ' . time();

        $this->browse(function (Browser $browser) use ($admin, $task, $newTitle) {
            $browser->loginAs($admin)
                    ->visit(route('admin.tasks.edit', $task->id))
                    ->clear('title')
                    ->type('title', $newTitle)
                    ->select('priority', 'high')
                    ->type('progress', '75')
                    ->press('Save')
                    ->waitForLocation(route('admin.tasks.index', [], false))
                    ->assertSee('Task updated successfully')
                    ->assertSee($newTitle);
        });

        // Clean up
        $task->delete();
    }

    /**
     * Test task toggle completion.
     */
    public function testTaskToggleCompletion()
    {
        $admin = User::withRole(UserRole::ADMIN)->first();
        $user = User::withRole(UserRole::USER)->first();
        $category = Category::first();

        if (!$admin || !$user || !$category) {
            $this->markTestSkipped('Admin, user, or category not found in the database');
        }

        // Create a test task (incomplete)
        $task = Task::create([
            'title' => 'Task for Toggle Test',
            'description' => 'This is a test task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 'medium',
            'due_date' => Carbon::now()->addDays(7),
            'progress' => 50,
            'completed' => false
        ]);

        $this->browse(function (Browser $browser) use ($admin, $task) {
            $browser->loginAs($admin)
                    ->visit(route('admin.tasks.index'))
                    ->assertSee($task->title)
                    ->click('@toggle-task-' . $task->id)
                    ->waitForText('Task status updated successfully')
                    ->assertSee('Task status updated successfully');
                    
            // Refresh task from DB and verify it's now complete
            $task->refresh();
            $this->assertTrue($task->completed);
            
            // Now toggle it back to incomplete
            $browser->click('@toggle-task-' . $task->id)
                    ->waitForText('Task status updated successfully')
                    ->assertSee('Task status updated successfully');
        });

        // Clean up
        $task->delete();
    }

    /**
     * Test task deletion.
     */
    public function testTaskDeletion()
    {
        $admin = User::withRole(UserRole::ADMIN)->first();
        $user = User::withRole(UserRole::USER)->first();
        $category = Category::first();

        if (!$admin || !$user || !$category) {
            $this->markTestSkipped('Admin, user, or category not found in the database');
        }

        // Create a test task
        $task = Task::create([
            'title' => 'Task for Delete Test',
            'description' => 'This is a test task description',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 'medium',
            'due_date' => Carbon::now()->addDays(7),
            'progress' => 50
        ]);

        $this->browse(function (Browser $browser) use ($admin, $task) {
            $browser->loginAs($admin)
                    ->visit(route('admin.tasks.index'))
                    ->assertSee($task->title)
                    ->click('@delete-task-' . $task->id)
                    ->waitForDialog()
                    ->acceptDialog()
                    ->waitForText('Task deleted successfully')
                    ->assertSee('Task deleted successfully')
                    ->assertDontSee($task->title);
        });

        // No need to clean up as the task should have been deleted by the test
    }
} 