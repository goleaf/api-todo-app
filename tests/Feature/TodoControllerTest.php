<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test todo index page can be rendered.
     */
    public function test_todo_index_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        Todo::factory(3)->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index'));

        $response->assertStatus(200);
        $response->assertViewIs('todos.index');
        $response->assertViewHas('todos');
    }

    /**
     * Test todo create page can be rendered.
     */
    public function test_todo_create_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('todos.create'));

        $response->assertStatus(200);
        $response->assertViewIs('todos.create');
    }

    /**
     * Test todo can be stored.
     */
    public function test_todo_can_be_stored(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $todoData = [
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'due_date' => now()->addDays(7)->toDateString(),
            'category_id' => $category->id,
        ];

        $response = $this
            ->actingAs($user)
            ->post(route('todos.store'), $todoData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('todos.index'));

        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test todo show page can be rendered.
     */
    public function test_todo_show_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.show', $todo));

        $response->assertStatus(200);
        $response->assertViewIs('todos.show');
        $response->assertViewHas('todo', $todo);
    }

    /**
     * Test todo edit page can be rendered.
     */
    public function test_todo_edit_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.edit', $todo));

        $response->assertStatus(200);
        $response->assertViewIs('todos.edit');
        $response->assertViewHas('todo', $todo);
    }

    /**
     * Test todo can be updated.
     */
    public function test_todo_can_be_updated(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'title' => 'Updated Todo',
            'description' => 'Updated Description',
            'due_date' => now()->addDays(14)->toDateString(),
            'category_id' => $category->id,
        ];

        $response = $this
            ->actingAs($user)
            ->put(route('todos.update', $todo), $updatedData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('todos.index'));

        $todo->refresh();
        $this->assertEquals('Updated Todo', $todo->title);
        $this->assertEquals('Updated Description', $todo->description);
    }

    /**
     * Test todo can be deleted.
     */
    public function test_todo_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->delete(route('todos.destroy', $todo));

        $response->assertRedirect(route('todos.index'));
        $this->assertSoftDeleted($todo);
    }

    /**
     * Test user cannot view todos of other users.
     */
    public function test_user_cannot_view_todos_of_other_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user1->id]);

        $response = $this
            ->actingAs($user2)
            ->get(route('todos.show', $todo));

        $response->assertStatus(403);
    }

    /**
     * Test user cannot edit todos of other users.
     */
    public function test_user_cannot_edit_todos_of_other_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user1->id]);

        $response = $this
            ->actingAs($user2)
            ->get(route('todos.edit', $todo));

        $response->assertStatus(403);
    }

    /**
     * Test user cannot update todos of other users.
     */
    public function test_user_cannot_update_todos_of_other_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user1->id]);

        $response = $this
            ->actingAs($user2)
            ->put(route('todos.update', $todo), [
                'title' => 'Updated Todo',
                'description' => 'Updated Description',
            ]);

        $response->assertStatus(403);
        $todo->refresh();
        $this->assertNotEquals('Updated Todo', $todo->title);
    }

    /**
     * Test user cannot delete todos of other users.
     */
    public function test_user_cannot_delete_todos_of_other_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user1->id]);

        $response = $this
            ->actingAs($user2)
            ->delete(route('todos.destroy', $todo));

        $response->assertStatus(403);
        $this->assertDatabaseHas('todos', ['id' => $todo->id]);
    }
}
