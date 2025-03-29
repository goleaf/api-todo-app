<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new todo.
     */
    public function test_user_can_create_todo(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Work',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/todos', [
                'title' => 'Test Todo',
                'description' => 'This is a test todo description',
                'completed' => false,
                'priority' => 1,
                'category_id' => $category->id,
                'due_date' => now()->addDays(3)->format('Y-m-d'),
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Test Todo',
                'description' => 'This is a test todo description',
            ]);

        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test fetching todos for a user.
     */
    public function test_user_can_fetch_their_todos(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create todos for the main user
        Todo::create([
            'title' => 'User Todo 1',
            'description' => 'This is user todo 1',
            'completed' => false,
            'user_id' => $user->id,
            'priority' => 0,
        ]);

        Todo::create([
            'title' => 'User Todo 2',
            'description' => 'This is user todo 2',
            'completed' => true,
            'user_id' => $user->id,
            'priority' => 2,
        ]);

        // Create a todo for another user
        Todo::create([
            'title' => 'Other User Todo',
            'description' => 'This is another user todo',
            'completed' => false,
            'user_id' => $otherUser->id,
            'priority' => 1,
        ]);

        // Test listing todos
        $response = $this->actingAs($user)->getJson('/api/todos');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['title' => 'User Todo 1'])
            ->assertJsonFragment(['title' => 'User Todo 2'])
            ->assertJsonMissing(['title' => 'Other User Todo']);
    }

    /**
     * Test updating a todo.
     */
    public function test_user_can_update_todo(): void
    {
        $user = User::factory()->create();
        $todo = Todo::create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'completed' => false,
            'user_id' => $user->id,
            'priority' => 0,
        ]);

        $response = $this->actingAs($user)
            ->putJson("/api/todos/{$todo->id}", [
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'completed' => true,
                'priority' => 1,
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'completed' => true,
                'priority' => 1,
            ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'completed' => 1,
            'priority' => 1,
        ]);
    }

    /**
     * Test deleting a todo.
     */
    public function test_user_can_delete_todo(): void
    {
        $user = User::factory()->create();
        $todo = Todo::create([
            'title' => 'Todo to Delete',
            'description' => 'This todo will be deleted',
            'completed' => false,
            'user_id' => $user->id,
            'priority' => 0,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/todos/{$todo->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }

    /**
     * Test authorization - users cannot modify other users' todos.
     */
    public function test_user_cannot_modify_others_todos(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $todo = Todo::create([
            'title' => 'User 1 Todo',
            'description' => 'This belongs to user 1',
            'completed' => false,
            'user_id' => $user1->id,
            'priority' => 0,
        ]);

        // User 2 tries to update User 1's todo
        $response = $this->actingAs($user2)
            ->putJson("/api/todos/{$todo->id}", [
                'title' => 'Unauthorized Update',
            ]);

        $response->assertStatus(403);

        // User 2 tries to delete User 1's todo
        $response = $this->actingAs($user2)
            ->deleteJson("/api/todos/{$todo->id}");

        $response->assertStatus(403);

        // Verify todo wasn't modified
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'User 1 Todo',
            'user_id' => $user1->id,
        ]);
    }
}
