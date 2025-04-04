<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function user_can_create_tag()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/tags', [
                'name' => 'Test Tag',
                'color' => '#FF0000',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'color',
                    'user_id',
                ],
            ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'Test Tag',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function user_can_list_their_tags()
    {
        $tags = Tag::factory(5)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/tags');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'color',
                        'user_id',
                        'tasks_count',
                    ],
                ],
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    #[Test]
    public function user_can_update_tag()
    {
        $tag = Tag::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tags/{$tag->id}", [
                'name' => 'Updated Tag',
                'color' => '#00FF00',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Tag updated successfully',
            ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Tag',
            'color' => '#00FF00',
        ]);
    }

    #[Test]
    public function user_can_delete_tag()
    {
        $tag = Tag::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tags/{$tag->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Tag deleted successfully',
            ]);

        $this->assertSoftDeleted('tags', [
            'id' => $tag->id,
        ]);
    }

    #[Test]
    public function user_cannot_access_other_users_tags()
    {
        $otherUser = User::factory()->create();
        $tag = Tag::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/tags/{$tag->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function tag_deletion_removes_task_relationships()
    {
        $tag = Tag::factory()->create(['user_id' => $this->user->id]);
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $task->tags()->attach($tag->id);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tags/{$tag->id}");

        $response->assertStatus(200);

        // Check that the relationship is removed
        $this->assertDatabaseMissing('task_tag', [
            'tag_id' => $tag->id,
            'task_id' => $task->id,
        ]);
    }

    #[Test]
    public function tag_validation_rules_are_enforced()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/tags', [
                'name' => '', // Empty name
                'color' => 'invalid-color', // Invalid color format
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'color']);
    }

    #[Test]
    public function user_can_attach_tags_to_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $tags = Tag::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", [
                'tag_ids' => $tags->pluck('id')->toArray(),
            ]);

        $response->assertStatus(200);

        // Check that all tags are attached
        foreach ($tags as $tag) {
            $this->assertDatabaseHas('task_tag', [
                'task_id' => $task->id,
                'tag_id' => $tag->id,
            ]);
        }
    }

    #[Test]
    public function user_can_detach_tags_from_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $tags = Tag::factory(3)->create(['user_id' => $this->user->id]);
        $task->tags()->attach($tags->pluck('id')->toArray());

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", [
                'tag_ids' => [],
            ]);

        $response->assertStatus(200);

        // Check that all tags are detached
        foreach ($tags as $tag) {
            $this->assertDatabaseMissing('task_tag', [
                'task_id' => $task->id,
                'tag_id' => $tag->id,
            ]);
        }
    }
}
