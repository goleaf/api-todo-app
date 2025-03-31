<?php

namespace Tests\Browser\Admin;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Admin\LoginPage;
use Tests\Browser\Pages\Admin\TaskFormPage;
use Tests\Browser\Pages\Admin\TasksPage;
use Tests\DuskTestCase;

class AdminTasksTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test tasks page loads
     *
     * @return void
     */
    public function testTasksPageLoads()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Task::factory()->count(5)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TasksPage)
                    ->assertSee('Tasks List')
                    ->assertSee('Create Task');
        });
    }

    /**
     * Test creating a new task
     *
     * @return void
     */
    public function testCreateTask()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        
        $taskData = [
            'title' => 'Test Task ' . time(),
            'description' => 'This is a test task description',
            'status' => TaskStatus::TODO->value,
            'priority' => TaskPriority::MEDIUM->value,
            'category_id' => $category->id,
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('+1 week')),
            'tags' => $tags->pluck('id')->toArray(),
        ];

        $this->browse(function (Browser $browser) use ($admin, $taskData) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TasksPage)
                    ->navigateToCreate()
                    ->on(new TaskFormPage)
                    ->fillForm($taskData)
                    ->submit()
                    ->on(new TasksPage)
                    ->assertSee('Task created successfully')
                    ->assertSee($taskData['title']);
        });
    }

    /**
     * Test viewing a task
     *
     * @return void
     */
    public function testViewTask()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $task) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TasksPage)
                    ->viewFirstTask()
                    ->assertPathContains('/admin/tasks')
                    ->assertSee($task->title)
                    ->assertSee($task->description);
        });
    }

    /**
     * Test editing a task
     *
     * @return void
     */
    public function testEditTask()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $newTitle = 'Updated Task Title ' . time();

        $this->browse(function (Browser $browser) use ($admin, $task, $newTitle) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TasksPage)
                    ->editFirstTask()
                    ->on(new TaskFormPage)
                    ->type('@title-input', $newTitle)
                    ->submit()
                    ->on(new TasksPage)
                    ->assertSee('Task updated successfully')
                    ->assertSee($newTitle);
        });
    }

    /**
     * Test deleting a task
     *
     * @return void
     */
    public function testDeleteTask()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Task To Delete ' . time()
        ]);

        $this->browse(function (Browser $browser) use ($admin, $task) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TasksPage)
                    ->search($task->title)
                    ->assertSee($task->title)
                    ->deleteFirstTask()
                    ->assertSee('Task deleted successfully')
                    ->search($task->title)
                    ->assertDontSee($task->title);
        });
    }

    /**
     * Test searching for tasks
     *
     * @return void
     */
    public function testSearchTasks()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $uniqueTitle = 'Unique Task ' . time();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => $uniqueTitle
        ]);
        Task::factory()->count(5)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $uniqueTitle) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TasksPage)
                    ->search($uniqueTitle)
                    ->assertSee($uniqueTitle)
                    ->assertDontSee('No tasks found');
        });
    }

    /**
     * Test filtering tasks by category
     *
     * @return void
     */
    public function testFilterTasksByCategory()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);
        
        $task1 = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category1->id,
            'title' => 'Task in Category 1 ' . time()
        ]);
        
        $task2 = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category2->id,
            'title' => 'Task in Category 2 ' . time()
        ]);

        $this->browse(function (Browser $browser) use ($admin, $category1, $task1, $task2) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TasksPage)
                    ->filterByCategory($category1->id)
                    ->assertSee($task1->title)
                    ->assertDontSee($task2->title);
        });
    }

    /**
     * Test filtering tasks by status
     *
     * @return void
     */
    public function testFilterTasksByStatus()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        $todoTask = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'To Do Task ' . time(),
            'status' => TaskStatus::TODO->value
        ]);
        
        $doneTask = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Done Task ' . time(),
            'status' => TaskStatus::DONE->value
        ]);

        $this->browse(function (Browser $browser) use ($admin, $todoTask, $doneTask) {
            $browser->visit(new LoginPage)
                    ->loginAsAdmin($admin->email, 'password')
                    ->visit(new TasksPage)
                    ->filterByStatus(TaskStatus::TODO->value)
                    ->assertSee($todoTask->title)
                    ->assertDontSee($doneTask->title);
        });
    }
} 