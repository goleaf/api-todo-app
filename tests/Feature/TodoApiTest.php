<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function a_user_can_get_all_their_todos()
    {
        // Create todos for this user
        Todo::factory()->count(3)->create(['user_id' => $this->user->id]);

        // Create todos for another user
        Todo::factory()->count(2)->create();

        $response = $this->actingAs($this->user)->getJson('/api/todos');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function a_user_can_create_a_todo()
    {
        $todoData = [
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'priority' => 1,
            'due_date' => now()->addDay()->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($this->user)->postJson('/api/todos', $todoData);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Todo']);

        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function a_user_can_update_their_todo()
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'completed' => true,
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/todos/{$todo->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Title']);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
            'completed' => true,
        ]);
    }

    /** @test */
    public function a_user_can_delete_their_todo()
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/todos/{$todo->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    /** @test */
    public function a_user_cannot_access_todos_of_another_user()
    {
        $anotherUser = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAs($this->user)->getJson("/api/todos/{$todo->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function a_user_cannot_update_todos_of_another_user()
    {
        $anotherUser = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/todos/{$todo->id}", ['title' => 'Hacked Todo']);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
            'title' => 'Hacked Todo',
        ]);
    }

    /** @test */
    public function todos_require_a_title()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/todos', [
                'description' => 'Test Description',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function it_validates_due_date_format()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/todos', [
                'title' => 'Test Todo',
                'due_date' => 'invalid-date',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('due_date');
    }
}
