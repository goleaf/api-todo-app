<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers;

class TodoApiTestExample extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_fetch_their_todos()
    {
        // Use the helper to create a user with unique email and todos
        [$user, $todos] = TestHelpers::createTestEnvironment(3);
        
        // Create another user with their own todos - these should not be returned
        [$otherUser, $otherTodos] = TestHelpers::createTestEnvironment(2);

        // Act as the first user and fetch todos
        $response = $this->actingAs($user)->getJson('/api/todos');

        // Verify the response
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'completed', 'created_at', 'updated_at']
                ]
            ]);
            
        // Verify that we only got the user's todos, not the other user's
        foreach ($todos as $todo) {
            $response->assertJsonFragment(['id' => $todo->id]);
        }
        
        foreach ($otherTodos as $todo) {
            $response->assertJsonMissing(['id' => $todo->id]);
        }
    }

    /** @test */
    public function a_user_can_create_a_todo()
    {
        // Create a user with unique email
        $user = TestHelpers::createUserWithUniqueEmail();
        
        // Prepare todo data
        $todoData = [
            'title' => 'New Todo',
            'description' => 'This is a test todo',
            'completed' => false,
        ];

        // Act as the user and create a todo
        $response = $this->actingAs($user)
            ->postJson('/api/todos', $todoData);

        // Verify the response
        $response->assertStatus(201)
            ->assertJson(['data' => $todoData]);
            
        // Verify the todo was created in the database
        $this->assertDatabaseHas('todos', [
            'title' => 'New Todo',
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function a_user_can_update_their_todo()
    {
        // Create a user with a todo
        $user = TestHelpers::createUserWithUniqueEmail();
        $todo = TestHelpers::createTodoWithUser(['title' => 'Original Title'], $user);
        
        // Update data
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'completed' => true
        ];

        // Act as the user and update the todo
        $response = $this->actingAs($user)
            ->putJson("/api/todos/{$todo->id}", $updateData);

        // Verify the response
        $response->assertStatus(200)
            ->assertJson(['data' => $updateData]);
            
        // Verify the database was updated
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
            'completed' => true
        ]);
    }

    /** @test */
    public function a_user_cannot_update_another_users_todo()
    {
        // Create a user with a todo
        $user = TestHelpers::createUserWithUniqueEmail();
        $todo = TestHelpers::createTodoWithUser(['title' => 'Original Title'], $user);
        
        // Create another user
        $otherUser = TestHelpers::createUserWithUniqueEmail();
        
        // Update data
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'completed' => true
        ];

        // Act as the other user and try to update the first user's todo
        $response = $this->actingAs($otherUser)
            ->putJson("/api/todos/{$todo->id}", $updateData);

        // Verify the response is forbidden
        $response->assertStatus(403);
            
        // Verify the database was not updated
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Original Title',
            'completed' => false
        ]);
    }

    /** @test */
    public function a_user_can_delete_their_todo()
    {
        // Create a user with a todo
        $user = TestHelpers::createUserWithUniqueEmail();
        $todo = TestHelpers::createTodoWithUser(['title' => 'Todo to Delete'], $user);

        // Act as the user and delete the todo
        $response = $this->actingAs($user)
            ->deleteJson("/api/todos/{$todo->id}");

        // Verify the response
        $response->assertStatus(200);
            
        // Verify the todo was removed from the database
        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }
} 