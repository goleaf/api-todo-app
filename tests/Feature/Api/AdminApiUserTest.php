<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminApiUserTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin and test user
        $this->admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'active' => true,
        ]);
        
        // Create some data for the user
        Task::factory(3)->create(['user_id' => $this->user->id]);
        Category::factory(2)->create(['user_id' => $this->user->id]);
        Tag::factory(2)->create(['user_id' => $this->user->id]);
    }

    /**
     * Test admin can view their own user info via API.
     */
    public function test_admin_can_view_user_info(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        $response = $this->getJson('/api/admin/user');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.email', 'admin@example.com');
    }

    /**
     * Test admin can view users list via API.
     */
    public function test_admin_can_view_users_list(): void
    {
        // Create additional users
        User::factory(5)->create();
        
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data',
                    'total',
                ],
            ]);
            
        // Verify we have at least 6 users (5 + 1 created in setup)
        $this->assertGreaterThanOrEqual(6, count($response->json('data.data')));
    }

    /**
     * Test admin can filter users by search term.
     */
    public function test_admin_can_filter_users_by_search(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        // Search by name
        $response = $this->getJson('/api/admin/users?search=Test User');
        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data.data')));
        
        // Search by email
        $response = $this->getJson('/api/admin/users?search=test@example');
        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data.data')));
    }

    /**
     * Test admin can view user details via API.
     */
    public function test_admin_can_view_user_details(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        $response = $this->getJson("/api/admin/users/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.id', $this->user->id)
            ->assertJsonPath('data.name', 'Test User')
            ->assertJsonPath('data.email', 'test@example.com');
            
        // Verify the response includes related data
        $this->assertArrayHasKey('tasks', $response->json('data'));
        $this->assertArrayHasKey('categories', $response->json('data'));
        $this->assertArrayHasKey('tags', $response->json('data'));
    }
    
    /**
     * Test admin can create a new user.
     */
    public function test_admin_can_create_user(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'active' => true,
        ];

        $response = $this->postJson('/api/admin/users', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User created successfully',
            ])
            ->assertJsonPath('data.name', 'New User')
            ->assertJsonPath('data.email', 'newuser@example.com');
            
        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
        ]);
    }
    
    /**
     * Test admin can update a user.
     */
    public function test_admin_can_update_user(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson("/api/admin/users/{$this->user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User updated successfully',
            ])
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.email', 'updated@example.com');
            
        // Verify user was updated in database
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }
    
    /**
     * Test admin can toggle user active status.
     */
    public function test_admin_can_toggle_user_active_status(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        // Initial state is active
        $this->assertTrue($this->user->active);
        
        // Toggle to inactive
        $response = $this->postJson("/api/admin/users/{$this->user->id}/toggle-active");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User deactivated successfully',
            ])
            ->assertJsonPath('data.active', false);
            
        // Verify user status was updated in database
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'active' => false,
        ]);
        
        // Toggle back to active
        $response = $this->postJson("/api/admin/users/{$this->user->id}/toggle-active");
        
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User activated successfully',
            ])
            ->assertJsonPath('data.active', true);
    }
    
    /**
     * Test admin can view user statistics.
     */
    public function test_admin_can_view_user_statistics(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        $response = $this->getJson("/api/admin/users/{$this->user->id}/statistics");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'task_counts',
                    'category_count',
                    'tag_count',
                    'recent_activity',
                ],
            ]);
            
        // Verify correct counts
        $this->assertEquals(3, $response->json('data.task_counts.total'));
        $this->assertEquals(2, $response->json('data.category_count'));
        $this->assertEquals(2, $response->json('data.tag_count'));
    }
    
    /**
     * Test admin cannot delete a user with related data.
     */
    public function test_admin_cannot_delete_user_with_related_data(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        $response = $this->deleteJson("/api/admin/users/{$this->user->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot delete user with related data. Please delete or reassign their tasks, categories, and tags first.',
            ]);
            
        // Verify user still exists
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
        ]);
    }
    
    /**
     * Test admin can delete a user without related data.
     */
    public function test_admin_can_delete_user_without_related_data(): void
    {
        // Create a user without related data
        $emptyUser = User::factory()->create();
        
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        $response = $this->deleteJson("/api/admin/users/{$emptyUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
            
        // Verify user was deleted
        $this->assertDatabaseMissing('users', [
            'id' => $emptyUser->id,
        ]);
    }
    
    /**
     * Test validation for user creation.
     */
    public function test_validation_for_user_creation(): void
    {
        // Authenticate as admin with admin ability
        Sanctum::actingAs($this->admin, ['admin']);

        // Test with invalid data
        $response = $this->postJson('/api/admin/users', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
} 