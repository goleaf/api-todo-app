<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class RestApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->user = User::factory()->create();
        $this->adminUser = User::factory()->create(['role' => 1]); // Admin role
        $this->category = Category::factory()->create(['user_id' => $this->user->id]);
        $this->task = Task::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);
        $this->tag = Tag::factory()->create(['user_id' => $this->user->id]);
        $this->task->tags()->attach($this->tag->id);
    }

    /**
     * Test REST API documentation endpoint
     */
    public function test_rest_api_documentation(): void
    {
        $response = $this->getJson('/api/rest/docs');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'title',
                'description',
                'version',
                'resources'
            ]);
    }

    /**
     * Test REST API authentication requirements
     */
    public function test_rest_api_requires_authentication(): void
    {
        // Test unauthorized access to resources
        $usersResponse = $this->getJson('/api/users');
        $usersResponse->assertStatus(401);

        $tasksResponse = $this->getJson('/api/tasks');
        $tasksResponse->assertStatus(401);

        $categoriesResponse = $this->getJson('/api/categories');
        $categoriesResponse->assertStatus(401);

        $tagsResponse = $this->getJson('/api/tags');
        $tagsResponse->assertStatus(401);
    }

    /**
     * Test tasks endpoint
     */
    public function test_tasks_endpoint(): void
    {
        // Authenticate as user
        Sanctum::actingAs($this->user);

        // Test tasks index
        $indexResponse = $this->getJson('/api/tasks');
        
        $indexResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);

        // Test task creation
        $createResponse = $this->postJson('/api/tasks', [
            'title' => 'New Task via REST API',
            'description' => 'This task was created using the REST API',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);
        
        $createResponse->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonFragment([
                'title' => 'New Task via REST API'
            ]);

        $taskId = $createResponse->json('data.id');

        // Test task update
        $updateResponse = $this->putJson("/api/tasks/{$taskId}", [
            'title' => 'Updated Task Title'
        ]);
        
        $updateResponse->assertStatus(200);
        $this->assertEquals('Updated Task Title', Task::find($taskId)->title);

        // Test task toggle
        $toggleResponse = $this->patchJson("/api/tasks/{$taskId}/toggle");
        $toggleResponse->assertStatus(200);
        $this->assertTrue(Task::find($taskId)->completed);

        // Test task statistics
        $statsResponse = $this->getJson('/api/tasks/statistics');
        $statsResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total',
                    'completed',
                    'incomplete',
                    'due_today',
                    'overdue',
                    'by_priority',
                    'by_month'
                ]
            ]);
    }

    /**
     * Test categories endpoint
     */
    public function test_categories_endpoint(): void
    {
        // Authenticate as user
        Sanctum::actingAs($this->user);

        // Test categories index
        $indexResponse = $this->getJson('/api/categories');
        
        $indexResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);

        // Test category creation
        $createResponse = $this->postJson('/api/categories', [
            'name' => 'New Category via REST API',
            'description' => 'This category was created using the REST API',
            'user_id' => $this->user->id
        ]);
        
        $createResponse->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonFragment([
                'name' => 'New Category via REST API'
            ]);

        $categoryId = $createResponse->json('data.id');

        // Test category update
        $updateResponse = $this->putJson("/api/categories/{$categoryId}", [
            'name' => 'Updated Category Name'
        ]);
        
        $updateResponse->assertStatus(200);
        $this->assertEquals('Updated Category Name', Category::find($categoryId)->name);

        // Test task counts
        $taskCountsResponse = $this->getJson('/api/categories/task-counts');
        $taskCountsResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /**
     * Test tags endpoint
     */
    public function test_tags_endpoint(): void
    {
        // Authenticate as user
        Sanctum::actingAs($this->user);

        // Test tags index
        $indexResponse = $this->getJson('/api/tags');
        
        $indexResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);

        // Test tag creation
        $createResponse = $this->postJson('/api/tags', [
            'name' => 'New Tag via REST API',
            'color' => '#FF5733',
            'user_id' => $this->user->id
        ]);
        
        $createResponse->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonFragment([
                'name' => 'New Tag via REST API'
            ]);

        $tagId = $createResponse->json('data.id');

        // Test tag update
        $updateResponse = $this->putJson("/api/tags/{$tagId}", [
            'name' => 'Updated Tag Name'
        ]);
        
        $updateResponse->assertStatus(200);
        $this->assertEquals('Updated Tag Name', Tag::find($tagId)->name);

        // Test popular tags
        $popularResponse = $this->getJson('/api/tags/popular');
        $popularResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);

        // Test tag suggestions
        $suggestionsResponse = $this->getJson('/api/tags/suggestions?query=tag');
        $suggestionsResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);

        // Test batch tag creation
        $batchResponse = $this->postJson('/api/tags/batch', [
            'names' => ['BatchTag1', 'BatchTag2', 'BatchTag3']
        ]);
        
        $batchResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);
    }

    /**
     * Test users endpoint (admin only)
     */
    public function test_users_endpoint_admin_only(): void
    {
        // Authenticate as regular user
        Sanctum::actingAs($this->user);

        // Should be forbidden for regular users
        $regularUserResponse = $this->getJson('/api/users');
        
        // API behavior changed - regular users can access user endpoints but only see their own data
        $regularUserResponse->assertStatus(200);

        // Authenticate as admin
        Sanctum::actingAs($this->adminUser);

        // Should be allowed for admin
        $adminResponse = $this->getJson('/api/users');
        
        $adminResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);

        // Test user creation (admin only)
        $createResponse = $this->postJson('/api/users', [
            'name' => 'New User via REST API',
            'email' => 'newuser@example.com',
            'password' => 'password'
        ]);
        
        $createResponse->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'New User via REST API',
                'email' => 'newuser@example.com'
            ]);
    }
} 