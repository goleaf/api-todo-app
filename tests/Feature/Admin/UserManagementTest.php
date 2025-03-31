<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');
    }

    /**
     * Test admin can view users list.
     */
    public function test_admin_can_view_users_list(): void
    {
        // Create some test users
        User::factory(5)->create();

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        
        // Test that the view has the users data
        $response->assertViewHas('users');
        
        // Check that we have all the test users in the view
        $viewUsers = $response->viewData('users');
        $this->assertEquals(5, $viewUsers->count());
    }

    /**
     * Test admin can view user creation form.
     */
    public function test_admin_can_view_user_creation_form(): void
    {
        $response = $this->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
    }

    /**
     * Test admin can create a new user.
     */
    public function test_admin_can_create_user(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User created successfully.');

        // Check that the user was actually created in the database
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test admin can view a user's details.
     */
    public function test_admin_can_view_user_details(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.users.show', $user->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.show');
        $response->assertViewHas('user');
        
        // Check that the view has the correct user
        $viewUser = $response->viewData('user');
        $this->assertEquals($user->id, $viewUser->id);
    }

    /**
     * Test admin can view user edit form.
     */
    public function test_admin_can_view_user_edit_form(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.users.edit', $user->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
        $response->assertViewHas('user');
        
        // Check that the view has the correct user
        $viewUser = $response->viewData('user');
        $this->assertEquals($user->id, $viewUser->id);
    }

    /**
     * Test admin can update a user.
     */
    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create();

        $updatedData = [
            'name' => 'Updated Name',
            'email' => $user->email,
        ];

        $response = $this->put(route('admin.users.update', $user->id), $updatedData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User updated successfully.');

        // Check that the user was actually updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test admin can delete a user.
     */
    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->delete(route('admin.users.destroy', $user->id));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User deleted successfully.');

        // Check that the user was actually deleted from the database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
} 