<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all categories via API.
     */
    public function test_user_can_get_their_categories(): void
    {
        $user = User::factory()->create();
        $categories = Category::factory(3)->create(['user_id' => $user->id]);

        // Create categories for another user (should not be returned)
        $otherUser = User::factory()->create();
        Category::factory(2)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson('/api/categories');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'color',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * Test getting a specific category via API.
     */
    public function test_user_can_get_specific_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'color' => $category->color,
            ],
        ]);
    }

    /**
     * Test creating a category via API.
     */
    public function test_user_can_create_category(): void
    {
        $user = User::factory()->create();
        $categoryData = [
            'name' => 'Test Category',
            'color' => '#FF5733',
        ];

        $response = $this->actingAs($user)->postJson('/api/categories', $categoryData);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'name' => 'Test Category',
                'color' => '#FF5733',
            ],
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'color' => '#FF5733',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test updating a category via API.
     */
    public function test_user_can_update_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $updatedData = [
            'name' => 'Updated Category',
            'color' => '#33FF57',
        ];

        $response = $this->actingAs($user)->putJson("/api/categories/{$category->id}", $updatedData);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $category->id,
                'name' => 'Updated Category',
                'color' => '#33FF57',
            ],
        ]);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'color' => '#33FF57',
        ]);
    }

    /**
     * Test deleting a category via API.
     */
    public function test_user_can_delete_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($category);
    }

    /**
     * Test user cannot access another user's category.
     */
    public function test_user_cannot_access_another_users_category(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->getJson("/api/categories/{$category->id}");

        $response->assertStatus(403);
    }

    /**
     * Test user cannot update another user's category.
     */
    public function test_user_cannot_update_another_users_category(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->putJson("/api/categories/{$category->id}", [
            'name' => 'Hacked Category',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test user cannot delete another user's category.
     */
    public function test_user_cannot_delete_another_users_category(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(403);
    }

    /**
     * Test category validation rules.
     */
    public function test_category_validation_rules(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/categories', [
            // Missing name and color
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'color']);
    }
}
