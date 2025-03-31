<?php

namespace Tests\Feature\Api;

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users for testing
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function can_get_tags()
    {
        // Create some tags for the user
        $tags = [];
        for ($i = 0; $i < 3; $i++) {
            $tags[] = Tag::create([
                'name' => "Tag {$i}",
                'color' => '#' . dechex(mt_rand(0, 0xFFFFFF)),
                'user_id' => $this->user->id,
            ]);
        }

        $response = $this->getJson('/api/tags');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'color',
                        'user_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data')
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function can_create_tag()
    {
        $tagData = [
            'name' => 'Test Tag',
            'color' => '#FF5733',
        ];

        $response = $this->postJson('/api/tags', $tagData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'color',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $tagData['name'],
                    'color' => $tagData['color'],
                    'user_id' => $this->user->id,
                ],
            ]);

        $this->assertDatabaseHas('tags', [
            'name' => $tagData['name'],
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function can_get_single_tag()
    {
        $tag = Tag::create([
            'name' => 'Test Tag',
            'color' => '#FF5733',
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/tags/{$tag->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'color',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ],
            ]);
    }

    /** @test */
    public function can_update_tag()
    {
        $tag = Tag::create([
            'name' => 'Original Tag',
            'color' => '#FF5733',
            'user_id' => $this->user->id,
        ]);

        $updatedData = [
            'name' => 'Updated Tag',
            'color' => '#33FF57',
        ];

        $response = $this->putJson("/api/tags/{$tag->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'color',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $tag->id,
                    'name' => $updatedData['name'],
                    'color' => $updatedData['color'],
                ],
            ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => $updatedData['name'],
            'color' => $updatedData['color'],
        ]);
    }

    /** @test */
    public function can_delete_tag()
    {
        $tag = Tag::create([
            'name' => 'Tag to Delete',
            'color' => '#FF5733',
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/tags/{$tag->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }

    /** @test */
    public function cannot_access_another_users_tags()
    {
        // Create a tag for another user
        $otherUserTag = Tag::create([
            'name' => 'Other User Tag',
            'color' => '#FF5733',
            'user_id' => $this->otherUser->id,
        ]);

        // Try to view another user's tag
        $response = $this->getJson("/api/tags/{$otherUserTag->id}");
        $response->assertStatus(404);

        // Try to update another user's tag
        $response = $this->putJson("/api/tags/{$otherUserTag->id}", [
            'name' => 'Updated Name',
        ]);
        $response->assertStatus(404);

        // Try to delete another user's tag
        $response = $this->deleteJson("/api/tags/{$otherUserTag->id}");
        $response->assertStatus(404);

        // Verify the tag wasn't modified
        $this->assertDatabaseHas('tags', [
            'id' => $otherUserTag->id,
            'name' => $otherUserTag->name,
            'user_id' => $this->otherUser->id,
        ]);
    }

    /** @test */
    public function validation_errors_when_creating_tag()
    {
        // Test name validation (required)
        $response = $this->postJson('/api/tags', [
            'color' => '#FF5733',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');

        // Test name validation (max length)
        $response = $this->postJson('/api/tags', [
            'name' => str_repeat('a', 51), // More than 50 characters
            'color' => '#FF5733',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');

        // Test color validation (max length)
        $response = $this->postJson('/api/tags', [
            'name' => 'Valid Name',
            'color' => str_repeat('#', 30), // More than 20 characters
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('color');

        // Test unique validation
        Tag::create([
            'name' => 'Unique Test',
            'color' => '#FF5733',
            'user_id' => $this->user->id,
        ]);

        $response = $this->postJson('/api/tags', [
            'name' => 'Unique Test',
            'color' => '#33FF57',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function can_get_popular_tags()
    {
        // Create some tags with different usage counts
        $tag1 = Tag::create([
            'name' => 'Popular Tag',
            'color' => '#FF5733',
            'user_id' => $this->user->id,
        ]);

        $tag2 = Tag::create([
            'name' => 'Less Popular Tag',
            'color' => '#33FF57',
            'user_id' => $this->user->id,
        ]);

        // Create some tasks and attach tags
        $task1 = Task::factory()->create(['user_id' => $this->user->id]);
        $task2 = Task::factory()->create(['user_id' => $this->user->id]);
        $task3 = Task::factory()->create(['user_id' => $this->user->id]);

        $task1->tags()->attach($tag1);
        $task2->tags()->attach($tag1);
        $task3->tags()->attach($tag2);

        $response = $this->getJson('/api/tags/popular');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'color',
                        'user_id',
                    ],
                ],
            ]);
    }

    /** @test */
    public function can_get_tasks_for_tag()
    {
        // Create a tag
        $tag = Tag::create([
            'name' => 'Test Tag',
            'color' => '#FF5733',
            'user_id' => $this->user->id,
        ]);

        // Create some tasks and attach the tag
        $task1 = Task::factory()->create(['user_id' => $this->user->id]);
        $task2 = Task::factory()->create(['user_id' => $this->user->id]);

        $tag->tasks()->attach([$task1->id, $task2->id]);

        $response = $this->getJson("/api/tags/{$tag->id}/tasks");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'tag',
                    'tasks',
                ],
            ])
            ->assertJsonCount(2, 'data.tasks');
    }
} 