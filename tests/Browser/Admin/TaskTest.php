<?php

namespace Tests\Browser\Admin;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;

class TaskTest extends AdminTestCase
{
    use WithFaker;

    /**
     * Test if admin can view tasks list
     *
     * @return void
     */
    public function test_admin_can_view_tasks_list()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Task',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $task) {
            $this->loginAdmin($browser)
                ->clickLink('Tasks')
                ->assertPathIs('/admin/tasks')
                ->assertSee('Task Management')
                ->assertSee('Test Task');
        });
    }

    /**
     * Test if admin can view task details
     *
     * @return void
     */
    public function test_admin_can_view_task_details()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Task Details',
            'description' => 'Test task description',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $task) {
            $this->loginAdmin($browser)
                ->clickLink('Tasks')
                ->assertPathIs('/admin/tasks')
                ->click('@view-task-' . $task->id)
                ->assertPathIs('/admin/tasks/' . $task->id)
                ->assertSee('Test Task Details')
                ->assertSee('Test task description');
        });
    }

    /**
     * Test if admin can create a new task
     *
     * @return void
     */
    public function test_admin_can_create_task()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create(['name' => 'Test User']);
        $category = Category::factory()->create(['user_id' => $user->id, 'name' => 'Test Category']);
        
        $taskTitle = 'New Task ' . $this->faker->word;
        $taskDescription = $this->faker->sentence;

        $this->browse(function (Browser $browser) use ($admin, $user, $category, $taskTitle, $taskDescription) {
            $this->loginAdmin($browser)
                ->clickLink('Tasks')
                ->assertPathIs('/admin/tasks')
                ->clickLink('Create Task')
                ->assertPathIs('/admin/tasks/create')
                ->select('user_id', $user->id)
                ->waitFor('[name=category_id]')
                ->select('category_id', $category->id)
                ->type('title', $taskTitle)
                ->type('description', $taskDescription)
                ->check('is_important')
                ->press('Save')
                ->waitForLocation('/admin/tasks')
                ->assertSee($taskTitle);
        });
    }

    /**
     * Test if admin can edit a task
     *
     * @return void
     */
    public function test_admin_can_edit_task()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Task to Edit',
        ]);
        
        $updatedTitle = 'Updated Task ' . $this->faker->word;

        $this->browse(function (Browser $browser) use ($admin, $task, $updatedTitle) {
            $this->loginAdmin($browser)
                ->clickLink('Tasks')
                ->assertPathIs('/admin/tasks')
                ->click('@edit-task-' . $task->id)
                ->assertPathIs('/admin/tasks/' . $task->id . '/edit')
                ->assertInputValue('title', 'Task to Edit')
                ->type('title', $updatedTitle)
                ->press('Save')
                ->waitForLocation('/admin/tasks')
                ->assertSee($updatedTitle);
        });
    }

    /**
     * Test if admin can delete a task
     *
     * @return void
     */
    public function test_admin_can_delete_task()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Task to Delete',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $task) {
            $this->loginAdmin($browser)
                ->clickLink('Tasks')
                ->assertPathIs('/admin/tasks')
                ->assertSee('Task to Delete')
                ->click('@delete-task-' . $task->id)
                ->waitForDialog()
                ->acceptDialog()
                ->waitUntilMissing('@delete-task-' . $task->id)
                ->assertDontSee('Task to Delete');
        });
    }
} 