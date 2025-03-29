<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new category.
     */
    public function test_user_can_create_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/categories', [
                'name' => 'Work',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Work',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Work',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test fetching categories for a user.
     */
    public function test_user_can_fetch_their_categories(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create categories for the main user
        Category::create([
            'name' => 'Work',
            'user_id' => $user->id,
        ]);

        Category::create([
            'name' => 'Personal',
            'user_id' => $user->id,
        ]);

        // Create a category for another user
        Category::create([
            'name' => 'Other User Category',
            'user_id' => $otherUser->id,
        ]);

        // Test listing categories
        $response = $this->actingAs($user)->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Work'])
            ->assertJsonFragment(['name' => 'Personal'])
            ->assertJsonMissing(['name' => 'Other User Category']);
    }

    /**
     * Test updating a category.
     */
    public function test_user_can_update_category(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Original Name',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->putJson("/api/categories/{$category->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Name',
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test deleting a category.
     */
    public function test_user_can_delete_category(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Category to Delete',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    /**
     * Test authorization - users cannot modify other users' categories.
     */
    public function test_user_cannot_modify_others_categories(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $category = Category::create([
            'name' => 'User 1 Category',
            'user_id' => $user1->id,
        ]);

        // User 2 tries to update User 1's category
        $response = $this->actingAs($user2)
            ->putJson("/api/categories/{$category->id}", [
                'name' => 'Unauthorized Update',
            ]);

        $response->assertStatus(403);

        // User 2 tries to delete User 1's category
        $response = $this->actingAs($user2)
            ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(403);

        // Verify category wasn't modified
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'User 1 Category',
            'user_id' => $user1->id,
        ]);
    }
}
