<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function user_can_create_task()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $tags = Tag::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'category_id' => $category->id,
                'due_date' => now()->addDays(7),
                'priority' => 'high',
                'tag_ids' => $tags->pluck('id')->toArray(),
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'category_id',
                    'due_date',
                    'priority',
                    'tags',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function user_can_list_their_tasks()
    {
        $tasks = Task::factory(5)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'category_id',
                        'due_date',
                        'priority',
                        'tags',
                    ],
                ],
                'meta',
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    #[Test]
    public function user_can_filter_tasks()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $tag = Tag::factory()->create(['user_id' => $this->user->id]);

        Task::factory(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'priority' => 'high',
        ]);

        Task::factory(2)->create([
            'user_id' => $this->user->id,
            'priority' => 'low',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks?category_id=' . $category->id . '&priority=high');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function user_can_update_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $newCategory = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Task',
                'category_id' => $newCategory->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task updated successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'category_id' => $newCategory->id,
        ]);
    }

    #[Test]
    public function user_can_delete_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task deleted successfully',
            ]);

        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);
    }

    #[Test]
    public function user_cannot_access_other_users_tasks()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function user_can_toggle_task_completion()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(200);
        $this->assertTrue($task->fresh()->completed);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/tasks/{$task->id}/toggle");

        $response->assertStatus(200);
        $this->assertFalse($task->fresh()->completed);
    }
}
