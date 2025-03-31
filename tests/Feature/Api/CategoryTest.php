<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Task;
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
            ])
            ->assertJson([
                'success' => true,
            ]);
            
        // Verify we got a response with data
        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('data', $responseData);
    }

    /** @test */
    public function can_create_category()
    {
        $categoryData = [
            'name' => 'Test Category',
            'color' => '#FF5733',
            'icon' => 'fa-tasks', // Required field
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
                    'icon',
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
                    'icon' => $categoryData['icon'],
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
            'icon' => 'fa-tag',
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
        $response->assertStatus(404);

        // Try to update another user's category
        $response = $this->putJson("/api/categories/{$otherUserCategory->id}", [
            'name' => 'Attempted Update',
        ]);
        $response->assertStatus(404);

        // Try to delete another user's category
        $response = $this->deleteJson("/api/categories/{$otherUserCategory->id}");
        $response->assertStatus(404);

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
            'icon' => 'fa-tasks',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');

        // Test color format validation
        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
            'color' => 'not-a-color',
            'icon' => 'fa-tasks',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('color');
        
        // Test icon validation (required)
        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
            'color' => '#FF5733',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('icon');
    }

    /** @test */
    public function can_get_category_task_counts()
    {
        // Create a category
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create some tasks for the category
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/categories/task-counts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
            ]);
            
        // Verify we have data
        $data = $response->json('data');
        $this->assertIsArray($data);
        $this->assertGreaterThan(0, count($data));
    }
} 