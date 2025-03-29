<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpers;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

/**
 * Example test file to demonstrate TestHelpers usage.
 * 
 * NOTE: This is an EXAMPLE test file and won't pass without setting up the
 * appropriate routes and controllers. Use this as a template for your actual
 * tests with your existing API routes.
 * 
 * The expected API routes for this test are:
 * - GET /api/todos - List authenticated user's todos
 * - POST /api/todos - Create a new todo
 * - PUT /api/todos/{id} - Update a todo
 * - DELETE /api/todos/{id} - Delete a todo
 * 
 * @see /docs/test-helpers-guide.md for more examples and documentation
 */
class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_view_their_own_todos()
    {
        // Create test environment with a user and 3 todos
        [$user, $todos] = TestHelpers::createTestEnvironment(3);
        
        // Create another user with 2 todos
        [$otherUser, $otherTodos] = TestHelpers::createTestEnvironment(2);
        
        // Test authenticated API access
        $response = $this->actingAs($user)
                         ->getJson('/api/todos');
        
        // Check response status and content
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonCount(3, 'data');
        
        // Verify that only user's todos are returned
        foreach ($todos as $todo) {
            $response->assertJsonFragment(['id' => $todo->id]);
        }
        
        // Verify other user's todos are not included
        foreach ($otherTodos as $todo) {
            $response->assertJsonMissing(['id' => $todo->id]);
        }
    }

    /** @test */
    public function users_can_create_a_todo()
    {
        // Create a user with a guaranteed unique email
        $user = TestHelpers::createUserWithUniqueEmail([
            'name' => 'Test User',
        ]);
        
        // Request data
        $todoData = [
            'title' => 'Test Todo',
            'description' => 'This is a test todo',
            'due_date' => now()->addDays(1)->toDateTimeString(),
            'completed' => false,
        ];
        
        // Test API request
        $response = $this->actingAs($user)
                         ->postJson('/api/todos', $todoData);
        
        // Check response
        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJsonFragment([
                     'title' => $todoData['title'],
                     'description' => $todoData['description'],
                 ]);
        
        // Verify todo exists in database
        $this->assertDatabaseHas('todos', [
            'title' => $todoData['title'],
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function guest_users_cannot_access_todos()
    {
        // Test unauthenticated access
        $response = $this->getJson('/api/todos');
        
        // Should return unauthorized
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function users_can_update_their_todos()
    {
        // Create a todo with an associated user
        $todo = TestHelpers::createTodoWithUser([
            'title' => 'Original Title',
        ]);
        
        // Get the user
        $user = User::find($todo->user_id);
        
        // Update data
        $updateData = [
            'title' => 'Updated Title',
            'completed' => true,
        ];
        
        // Test API request
        $response = $this->actingAs($user)
                         ->putJson("/api/todos/{$todo->id}", $updateData);
        
        // Check response
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonFragment($updateData);
        
        // Verify database update
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
            'completed' => true,
        ]);
    }

    /** @test */
    public function users_cannot_update_others_todos()
    {
        // Create a todo with an associated user
        $todo = TestHelpers::createTodoWithUser();
        
        // Create another user
        $otherUser = TestHelpers::createUserWithUniqueEmail();
        
        // Update data
        $updateData = [
            'title' => 'Unauthorized Update',
        ];
        
        // Test API request with wrong user
        $response = $this->actingAs($otherUser)
                         ->putJson("/api/todos/{$todo->id}", $updateData);
        
        // Should return forbidden
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        
        // Verify database was not updated
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
            'title' => 'Unauthorized Update',
        ]);
    }

    /** @test */
    public function authenticated_users_can_delete_their_own_todos()
    {
        // Create a todo with an associated user
        $todo = TestHelpers::createTodoWithUser();
        
        // Get the user
        $user = User::find($todo->user_id);
        
        // Test API request
        $response = $this->actingAs($user)
                         ->deleteJson("/api/todos/{$todo->id}");
        
        // Check response
        $response->assertStatus(Response::HTTP_OK);
        
        // Verify todo was deleted
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }

    /** @test */
    public function api_returns_validation_errors_for_invalid_todo_data()
    {
        // Create a user
        $user = TestHelpers::createUserWithUniqueEmail();
        
        // Invalid data (missing required title)
        $invalidData = [
            'description' => 'No title provided',
        ];
        
        // Test API request
        $response = $this->actingAs($user)
                         ->postJson('/api/todos', $invalidData);
        
        // Check validation error response
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['title']);
    }
}
