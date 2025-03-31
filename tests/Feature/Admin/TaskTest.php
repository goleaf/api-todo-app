<?php

namespace Tests\Feature\Admin;

use App\Enums\TaskPriority;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_tasks_index()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $tasks = Task::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.tasks.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tasks.index');
        $response->assertViewHas('tasks');
        
        // Check that all tasks are in the view
        foreach ($tasks as $task) {
            $response->assertSee($task->title);
        }
    }

    public function test_admin_can_create_a_task()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.tasks.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tasks.create');

        // Submit the form to create a task
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => TaskPriority::MEDIUM->value,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'progress' => 0,
            'completed' => false,
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.tasks.store'), $taskData);

        $response->assertRedirect(route('admin.tasks.index'));
        $response->assertSessionHas('success');

        // Check that the task was created in the database
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_admin_can_view_a_task()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Task',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.tasks.show', $task));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tasks.show');
        $response->assertSee('Test Task');
    }

    public function test_admin_can_update_a_task()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Original Title',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.tasks.edit', $task));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tasks.edit');

        // Submit the form to update the task
        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => TaskPriority::HIGH->value,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'progress' => 50,
            'completed' => true,
        ];

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.tasks.update', $task), $updatedData);

        $response->assertRedirect(route('admin.tasks.index'));
        $response->assertSessionHas('success');

        // Check that the task was updated in the database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'progress' => 50,
            'completed' => true,
        ]);
    }

    public function test_admin_can_delete_a_task()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->delete(route('admin.tasks.destroy', $task));

        $response->assertRedirect(route('admin.tasks.index'));
        $response->assertSessionHas('success');

        // Check that the task was deleted from the database
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_admin_can_toggle_task_completion()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'completed' => false,
        ]);

        // First toggle - from incomplete to complete
        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.tasks.toggle', $task));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check that the task was marked as completed
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => true,
        ]);

        // Refresh the task from database
        $task->refresh();

        // Second toggle - from complete to incomplete
        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.tasks.toggle', $task));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check that the task was marked as incomplete again
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => false,
        ]);
    }

    public function test_admin_can_attach_tags_to_task()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $tags = Tag::factory(3)->create([
            'user_id' => $user->id,
        ]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        // Create task with tags
        $taskData = [
            'title' => $task->title,
            'description' => $task->description,
            'user_id' => $user->id,
            'category_id' => $task->category_id,
            'priority' => $task->priority,
            'tags' => $tags->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.tasks.update', $task), $taskData);

        $response->assertRedirect(route('admin.tasks.index'));
        $response->assertSessionHas('success');

        // Check that the tags were attached to the task
        foreach ($tags as $tag) {
            $this->assertDatabaseHas('task_tag', [
                'task_id' => $task->id,
                'tag_id' => $tag->id,
            ]);
        }
    }

    public function test_admin_can_get_categories_for_user()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $categories = Category::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->getJson(route('admin.users.categories', $user));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        
        // Check that all categories for the user are returned
        foreach ($categories as $category) {
            $response->assertJsonFragment([
                'id' => $category->id,
                'name' => $category->name,
            ]);
        }
    }

    public function test_admin_can_get_tags_for_user()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $tags = Tag::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->getJson(route('admin.users.tags', $user));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        
        // Check that all tags for the user are returned
        foreach ($tags as $tag) {
            $response->assertJsonFragment([
                'id' => $tag->id,
                'name' => $tag->name,
            ]);
        }
    }
} 