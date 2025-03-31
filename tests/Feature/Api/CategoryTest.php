<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryTest extends TestCase
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
    public function can_get_categories()
    {
        // Create some categories for the user
        Category::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/categories');

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
    public function can_create_category()
    {
        $categoryData = [
            'name' => 'Test Category',
            'color' => '#FF5733',
        ];

        $response = $this->postJson('/api/categories', $categoryData);

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
                    'name' => $categoryData['name'],
                    'color' => $categoryData['color'],
                    'user_id' => $this->user->id,
                ],
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => $categoryData['name'],
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function can_get_single_category()
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/categories/{$category->id}");

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
                    'id' => $category->id,
                    'name' => $category->name,
                    'color' => $category->color,
                ],
            ]);
    }

    /** @test */
    public function can_update_category()
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $updatedData = [
            'name' => 'Updated Category Name',
            'color' => '#00FF00',
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $updatedData);

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
                    'id' => $category->id,
                    'name' => $updatedData['name'],
                    'color' => $updatedData['color'],
                ],
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $updatedData['name'],
            'color' => $updatedData['color'],
        ]);
    }

    /** @test */
    public function can_delete_category()
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    /** @test */
    public function cannot_access_another_users_categories()
    {
        // Create a category for another user
        $otherUserCategory = Category::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        // Try to get another user's category
        $response = $this->getJson("/api/categories/{$otherUserCategory->id}");
        $response->assertStatus(403);

        // Try to update another user's category
        $response = $this->putJson("/api/categories/{$otherUserCategory->id}", [
            'name' => 'Attempted Update',
        ]);
        $response->assertStatus(403);

        // Try to delete another user's category
        $response = $this->deleteJson("/api/categories/{$otherUserCategory->id}");
        $response->assertStatus(403);

        // Verify the category wasn't modified
        $this->assertDatabaseHas('categories', [
            'id' => $otherUserCategory->id,
            'name' => $otherUserCategory->name,
            'user_id' => $this->otherUser->id,
        ]);
    }

    /** @test */
    public function validation_errors_when_creating_category()
    {
        // Test name validation (required)
        $response = $this->postJson('/api/categories', [
            'color' => '#FF5733',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');

        // Test color format validation
        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
            'color' => 'not-a-color',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('color');
    }

    /** @test */
    public function can_get_category_with_tasks()
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create some tasks for the category
        $category->tasks()->createMany([
            [
                'title' => 'Task 1',
                'description' => 'Description 1',
                'priority' => 1,
                'user_id' => $this->user->id,
            ],
            [
                'title' => 'Task 2',
                'description' => 'Description 2',
                'priority' => 2,
                'user_id' => $this->user->id,
            ],
        ]);

        $response = $this->getJson("/api/categories/{$category->id}/tasks");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'category' => [
                        'id',
                        'name',
                        'color',
                    ],
                    'tasks' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'priority',
                        ],
                    ],
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name,
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data.tasks');
    }
} 