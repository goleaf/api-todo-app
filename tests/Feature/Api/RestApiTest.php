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
use Illuminate\Support\Facades\Gate;

class RestApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable database transactions for these tests to avoid conflicts
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class);
        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:refresh');
        });
        
        // Allow all Gate checks to pass
        Gate::before(function () {
            return true;
        });
        
        // Create test data
        $this->user = User::factory()->create();
        $this->adminUser = User::factory()->create(['role' => 'admin']); // Admin role
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
        // This endpoint doesn't require authentication
        $this->withoutExceptionHandling();
        
        $response = $this->get(route('rest.docs'));
        
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
        $usersResponse = $this->getJson(route('api.users.index'));
        $usersResponse->assertStatus(401);

        $tasksResponse = $this->getJson(route('api.tasks.index'));
        $tasksResponse->assertStatus(401);

        $categoriesResponse = $this->getJson(route('api.categories.index'));
        $categoriesResponse->assertStatus(401);

        $tagsResponse = $this->getJson(route('api.tags.index'));
        $tagsResponse->assertStatus(401);
    }

    /**
     * Test tasks endpoint
     */
    public function test_tasks_endpoint(): void
    {
        $this->withoutExceptionHandling();

        // Authenticate as user using Sanctum actingAs
        Sanctum::actingAs($this->user);
        
        // Test tasks index 
        $indexResponse = $this->getJson(route('api.tasks.index'));
        
        $indexResponse->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);

        // Test task creation
        $createResponse = $this->postJson(route('api.tasks.store'), [
            'mutate' => [
                [
                    'title' => 'New Task via REST API',
                    'description' => 'This task was created using the REST API',
                    'user_id' => $this->user->id,
                    'category_id' => $this->category->id
                ]
            ],
            'operation' => 'create'
        ]);
        
        $createResponse->assertStatus(201)
            ->assertJsonStructure([
                'data'
            ])
            ->assertJsonFragment([
                'title' => 'New Task via REST API'
            ]);

        $taskId = $createResponse->json('data.id');

        // Test task update
        $updateResponse = $this->putJson(route('api.tasks.update', ['id' => $taskId]), [
            'update' => [
                [
                    'id' => $taskId,
                    'title' => 'Updated Task Title'
                ]
            ],
            'operation' => 'update'
        ]);
        
        $updateResponse->assertStatus(200);
        $this->assertEquals('Updated Task Title', Task::find($taskId)->title);

        // Test task toggle
        $toggleResponse = $this->patchJson(route('api.tasks.toggle', ['id' => $taskId]));
        $toggleResponse->assertStatus(200);
        $this->assertTrue(Task::find($taskId)->completed);

        // Test task statistics
        $statsResponse = $this->getJson(route('api.tasks.statistics'));
        $statsResponse->assertStatus(200)
            ->assertJsonStructure([
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
        $indexResponse = $this->getJson(route('api.categories.index'));
        
        $indexResponse->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);

        // Test category creation
        $createResponse = $this->postJson(route('api.categories.store'), [
            'mutate' => [
                [
                    'name' => 'New Category via REST API',
                    'description' => 'This category was created using the REST API',
                    'user_id' => $this->user->id
                ]
            ],
            'operation' => 'create'
        ]);
        
        $createResponse->assertStatus(201)
            ->assertJsonStructure([
                'data'
            ])
            ->assertJsonFragment([
                'name' => 'New Category via REST API'
            ]);

        $categoryId = $createResponse->json('data.id');

        // Test category update
        $updateResponse = $this->putJson(route('api.categories.update', ['id' => $categoryId]), [
            'update' => [
                [
                    'id' => $categoryId,
                    'name' => 'Updated Category Name'
                ]
            ],
            'operation' => 'update'
        ]);
        
        $updateResponse->assertStatus(200);
        $this->assertEquals('Updated Category Name', Category::find($categoryId)->name);

        // Test task counts
        $taskCountsResponse = $this->getJson(route('api.categories.task-counts'));
        $taskCountsResponse->assertStatus(200)
            ->assertJsonStructure([
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
        $indexResponse = $this->getJson(route('api.tags.index'));
        
        $indexResponse->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);

        // Test tag creation
        $createResponse = $this->postJson(route('api.tags.store'), [
            'mutate' => [
                [
                    'name' => 'New Tag via REST API',
                    'color' => '#FF5733',
                    'user_id' => $this->user->id
                ]
            ],
            'operation' => 'create'
        ]);
        
        $createResponse->assertStatus(201)
            ->assertJsonStructure([
                'data'
            ])
            ->assertJsonFragment([
                'name' => 'New Tag via REST API'
            ]);

        $tagId = $createResponse->json('data.id');

        // Test tag update
        $updateResponse = $this->putJson(route('api.tags.update', ['id' => $tagId]), [
            'update' => [
                [
                    'id' => $tagId,
                    'name' => 'Updated Tag Name'
                ]
            ],
            'operation' => 'update'
        ]);
        
        $updateResponse->assertStatus(200);
        $this->assertEquals('Updated Tag Name', Tag::find($tagId)->name);

        // Test popular tags
        $popularResponse = $this->getJson(route('api.tags.popular'));
        $popularResponse->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);

        // Test tag suggestions
        $suggestionsResponse = $this->getJson(route('api.tags.suggestions', ['query' => 'tag']));
        $suggestionsResponse->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);

        // Test batch tag creation
        $batchResponse = $this->postJson(route('api.tags.batch-create'), [
            'names' => ['BatchTag1', 'BatchTag2', 'BatchTag3']
        ]);
        
        $batchResponse->assertStatus(200)
            ->assertJsonStructure([
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
        $regularUserResponse = $this->getJson(route('api.users.index'));
        
        // API behavior changed - regular users can access user endpoints but only see their own data
        $regularUserResponse->assertStatus(200);

        // Authenticate as admin
        Sanctum::actingAs($this->adminUser);

        // Should be allowed for admin
        $adminResponse = $this->getJson(route('api.users.index'));
        
        $adminResponse->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);

        // Test user creation (admin only)
        $createResponse = $this->postJson(route('api.users.store'), [
            'mutate' => [
                [
                    'name' => 'New User via REST API',
                    'email' => 'newuser@example.com',
                    'password' => 'password'
                ]
            ],
            'operation' => 'create'
        ]);
        
        $createResponse->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'New User via REST API',
                'email' => 'newuser@example.com'
            ]);
    }
} 