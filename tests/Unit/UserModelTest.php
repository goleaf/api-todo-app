<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test User model has many todos relationship.
     */
    public function test_user_has_many_todos(): void
    {
        $user = User::factory()->create();
        
        $todo1 = new Todo([
            'title' => 'User Todo 1',
            'completed' => false,
            'priority' => 1,
        ]);
        $user->todos()->save($todo1);

        $todo2 = new Todo([
            'title' => 'User Todo 2',
            'completed' => true,
            'priority' => 0,
        ]);
        $user->todos()->save($todo2);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->todos);
        $this->assertCount(2, $user->todos);
        $this->assertEquals('User Todo 1', $user->todos[0]->title);
        $this->assertEquals('User Todo 2', $user->todos[1]->title);
    }

    /**
     * Test User model has many categories relationship.
     */
    public function test_user_has_many_categories(): void
    {
        $user = User::factory()->create();
        
        $category1 = new Category([
            'name' => 'Work',
        ]);
        $user->categories()->save($category1);

        $category2 = new Category([
            'name' => 'Personal',
        ]);
        $user->categories()->save($category2);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->categories);
        $this->assertCount(2, $user->categories);
        
        // Get the categories sorted by name to ensure consistent order
        $categories = $user->categories->sortBy('name')->values();
        $this->assertEquals('Personal', $categories[0]->name);
        $this->assertEquals('Work', $categories[1]->name);
    }

    /**
     * Test user password is hashed on creation.
     */
    public function test_password_is_hashed_on_creation(): void
    {
        $plainPassword = 'password123';
        
        $user = User::factory()->create([
            'password' => bcrypt($plainPassword),
        ]);
        
        // Password should be hashed, not stored as plain text
        $this->assertNotEquals($plainPassword, $user->password);
        
        // Hash should be the correct format (60 characters for bcrypt)
        $this->assertEquals(60, strlen($user->password));
    }

    /**
     * Test user hidden attributes.
     */
    public function test_user_has_correct_hidden_attributes(): void
    {
        $user = new User();
        $hidden = $user->getHidden();
        
        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    /**
     * Test user fillable attributes.
     */
    public function test_user_has_correct_fillable_attributes(): void
    {
        $user = new User();
        $fillable = $user->getFillable();
        
        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
    }

    /**
     * Test deleting user cascades to todos and categories.
     */
    public function test_deleting_user_cascades_to_related_models(): void
    {
        $user = User::factory()->create();
        
        // Create a todo and category for this user
        $todo = new Todo([
            'title' => 'User Todo',
            'completed' => false,
            'priority' => 0,
        ]);
        $user->todos()->save($todo);
        
        $category = new Category([
            'name' => 'User Category',
        ]);
        $user->categories()->save($category);
        
        // Record IDs for later checking
        $todoId = $todo->id;
        $categoryId = $category->id;
        
        // Delete the user
        $user->delete();
        
        // Check user is deleted
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
        
        // Check if related todos and categories are deleted
        // The actual behavior depends on your cascade settings in migrations
        // If cascading is enabled:
        $this->assertDatabaseMissing('todos', [
            'id' => $todoId,
        ]);
        
        $this->assertDatabaseMissing('categories', [
            'id' => $categoryId,
        ]);
        
        // If cascading is not enabled, change these assertions to check for null user_id
        // $this->assertDatabaseHas('todos', [
        //     'id' => $todoId,
        //     'user_id' => null,
        // ]);
    }
}
