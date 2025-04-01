<?php

namespace Tests\Feature\Admin;

use App\Enums\CategoryType;
use App\Models\Admin;
use App\Models\Category;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class CategoryTest extends TestCase
{
    use WithFaker;

    protected User $adminUser;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Refresh database for SQLite compatibility
        Artisan::call('migrate:fresh');
        
        // Create admin user
        $this->adminUser = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);
    }

    public function test_admin_can_view_categories_index()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $categories = Category::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
        $response->assertViewHas('categories');
        
        // Check that all categories are in the view
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }

    public function test_admin_can_create_a_category()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.form');

        // Submit the form to create a category
        $categoryData = [
            'name' => 'Test Category',
            'description' => 'This is a test category',
            'user_id' => $user->id,
            'color' => '#ff0000',
            'type' => CategoryType::WORK->value,
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.categories.store'), $categoryData);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        // Check that the category was created in the database
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_can_view_a_category()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Category',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.categories.show', $category));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.show');
        $response->assertSee('Test Category');
    }

    public function test_admin_can_update_a_category()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Original Name',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.categories.edit', $category));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.form');

        // Submit the form to update the category
        $updatedData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'user_id' => $user->id,
            'color' => '#00ff00',
            'type' => CategoryType::PERSONAL->value,
        ];

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.categories.update', $category), $updatedData);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        // Check that the category was updated in the database
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_a_category()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        // Check that the category was deleted from the database
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_cannot_delete_category_with_tasks()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Create a task assigned to this category
        $task = \App\Models\Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('error');

        // Check that the category still exists in the database
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }
} 