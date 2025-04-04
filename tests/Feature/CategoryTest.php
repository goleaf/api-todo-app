<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function user_can_create_category()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/categories', [
                'name' => 'Test Category',
                'description' => 'Test Description',
                'color' => '#FF0000',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'color',
                    'user_id',
                ],
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function user_can_list_their_categories()
    {
        $categories = Category::factory(5)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'color',
                        'user_id',
                        'tasks_count',
                    ],
                ],
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    #[Test]
    public function user_can_update_category()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/categories/{$category->id}", [
                'name' => 'Updated Category',
                'color' => '#00FF00',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category updated successfully',
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'color' => '#00FF00',
        ]);
    }

    #[Test]
    public function user_can_delete_category()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category deleted successfully',
            ]);

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }

    #[Test]
    public function user_cannot_access_other_users_categories()
    {
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/categories/{$category->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function category_deletion_cascade_affects_tasks()
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $tasks = Task::factory(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200);

        // Check that tasks' category_id is set to null
        foreach ($tasks as $task) {
            $this->assertNull($task->fresh()->category_id);
        }
    }

    #[Test]
    public function category_validation_rules_are_enforced()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/categories', [
                'name' => '', // Empty name
                'color' => 'invalid-color', // Invalid color format
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'color']);
    }
}
