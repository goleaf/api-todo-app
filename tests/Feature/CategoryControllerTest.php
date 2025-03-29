<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user
        $this->user = User::factory()->create();
        
        // Create a token for authenticated requests
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function unauthenticated_users_cannot_access_category_endpoints()
    {
        // Index endpoint
        $this->getJson('/api/categories')
            ->assertStatus(401);
        
        // Show endpoint
        $this->getJson('/api/categories/1')
            ->assertStatus(401);
        
        // Store endpoint
        $this->postJson('/api/categories', [])
            ->assertStatus(401);
        
        // Update endpoint
        $this->putJson('/api/categories/1', [])
            ->assertStatus(401);
        
        // Delete endpoint
        $this->deleteJson('/api/categories/1')
            ->assertStatus(401);
    }

    /** @test */
    public function it_lists_all_categories_for_authenticated_user()
    {
        // Create categories for the user
        Category::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);
        
        // Create categories for another user
        $otherUser = User::factory()->create();
        Category::factory()->count(2)->create([
            'user_id' => $otherUser->id
        ]);
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/categories');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'color',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_shows_a_specific_category()
    {
        // Create a category for the user
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Category',
            'color' => '#FF5733'
        ]);
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/categories/' . $category->id);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $category->id,
                    'name' => 'Test Category',
                    'color' => '#FF5733',
                    'user_id' => $this->user->id
                ]
            ]);
    }

    /** @test */
    public function it_cannot_show_categories_belonging_to_other_users()
    {
        // Create a category for another user
        $otherUser = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $otherUser->id
        ]);
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/categories/' . $category->id);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function it_creates_a_new_category()
    {
        $categoryData = [
            'name' => 'New Test Category',
            'color' => '#00FF00'
        ];
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/categories', $categoryData);
        
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'New Test Category',
                    'color' => '#00FF00',
                    'user_id' => $this->user->id
                ]
            ]);
        
        // Check database
        $this->assertDatabaseHas('categories', [
            'name' => 'New Test Category',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_a_category()
    {
        // Missing name
        $categoryData = [
            'color' => '#00FF00'
        ];
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/categories', $categoryData);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_updates_a_category()
    {
        // Create a category for the user
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'color' => '#000000'
        ]);
        
        $updateData = [
            'name' => 'Updated Name',
            'color' => '#FFFFFF'
        ];
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/categories/' . $category->id, $updateData);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $category->id,
                    'name' => 'Updated Name',
                    'color' => '#FFFFFF'
                ]
            ]);
        
        // Check database
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'color' => '#FFFFFF'
        ]);
    }

    /** @test */
    public function it_cannot_update_categories_belonging_to_other_users()
    {
        // Create a category for another user
        $otherUser = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Category'
        ]);
        
        $updateData = [
            'name' => 'Updated Name'
        ];
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/categories/' . $category->id, $updateData);
        
        $response->assertStatus(403);
        
        // Database should be unchanged
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Other User Category'
        ]);
    }

    /** @test */
    public function it_deletes_a_category()
    {
        // Create a category for the user
        $category = Category::factory()->create([
            'user_id' => $this->user->id
        ]);
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/categories/' . $category->id);
        
        $response->assertStatus(204);
        
        // Check database
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }

    /** @test */
    public function it_cannot_delete_categories_belonging_to_other_users()
    {
        // Create a category for another user
        $otherUser = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $otherUser->id
        ]);
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/categories/' . $category->id);
        
        $response->assertStatus(403);
        
        // Database should be unchanged
        $this->assertDatabaseHas('categories', [
            'id' => $category->id
        ]);
    }

    /** @test */
    public function it_validates_unique_category_name_per_user()
    {
        // Create a category for the user
        Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Work'
        ]);
        
        // Try to create another category with the same name
        $categoryData = [
            'name' => 'Work',
            'color' => '#00FF00'
        ];
        
        // Make authenticated request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/categories', $categoryData);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
        
        // Another user should be able to use the same name
        $otherUser = User::factory()->create();
        $otherToken = $otherUser->createToken('test-token')->plainTextToken;
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $otherToken,
        ])->postJson('/api/categories', $categoryData);
        
        $response->assertStatus(201);
    }

    /** @test */
    public function it_returns_categories_with_task_count()
    {
        // Create categories for the user
        $category1 = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Category 1'
        ]);
        
        $category2 = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Category 2'
        ]);
        
        // Create tasks for the first category
        \App\Models\Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $category1->id
        ]);
        
        // Create tasks for the second category
        \App\Models\Task::factory()->count(1)->create([
            'user_id' => $this->user->id,
            'category_id' => $category2->id
        ]);
        
        // Make authenticated request with include=tasks_count parameter
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/categories?include=tasks_count');
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.tasks_count', 3)
            ->assertJsonPath('data.1.tasks_count', 1);
    }
} 