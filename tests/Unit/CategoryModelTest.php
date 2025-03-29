<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Category model belongs to user relationship.
     */
    public function test_category_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Work',
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $category->user);
        $this->assertEquals($user->id, $category->user->id);
    }

    /**
     * Test Category model has many todos relationship.
     */
    public function test_category_has_many_todos(): void
    {
        $user = User::factory()->create();
        $category = new Category([
            'name' => 'Work',
        ]);
        $user->categories()->save($category);

        $todo1 = new Todo([
            'title' => 'Todo 1',
            'completed' => false,
            'priority' => 0,
        ]);
        $todo1->user()->associate($user);
        $todo1->category()->associate($category);
        $todo1->save();

        $todo2 = new Todo([
            'title' => 'Todo 2',
            'completed' => false,
            'priority' => 1,
        ]);
        $todo2->user()->associate($user);
        $todo2->category()->associate($category);
        $todo2->save();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $category->todos);
        $this->assertCount(2, $category->todos);
        $this->assertEquals('Todo 1', $category->todos[0]->title);
        $this->assertEquals('Todo 2', $category->todos[1]->title);
    }

    /**
     * Test Category model fillable attributes.
     */
    public function test_category_has_correct_fillable_attributes(): void
    {
        $category = new Category();
        $fillable = $category->getFillable();
        
        $this->assertContains('name', $fillable);
        $this->assertContains('color', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('user_id', $fillable);
    }

    /**
     * Test creating a category.
     */
    public function test_can_create_category(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Personal',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Personal',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test category deletion with todos.
     */
    public function test_deleting_category_cascades_todos(): void
    {
        $user = User::factory()->create();
        
        $category = new Category([
            'name' => 'Test Category',
        ]);
        $user->categories()->save($category);
        
        // Create todos for this category
        $todo1 = new Todo([
            'title' => 'Category Todo 1',
            'completed' => false,
            'priority' => 1,
        ]);
        $todo1->user()->associate($user);
        $todo1->category()->associate($category);
        $todo1->save();
        
        $todo2 = new Todo([
            'title' => 'Category Todo 2',
            'completed' => true,
            'priority' => 0,
        ]);
        $todo2->user()->associate($user);
        $todo2->category()->associate($category);
        $todo2->save();
        
        // Check todos exist
        $this->assertDatabaseHas('todos', [
            'title' => 'Category Todo 1',
            'category_id' => $category->id,
        ]);
        
        $this->assertDatabaseHas('todos', [
            'title' => 'Category Todo 2',
            'category_id' => $category->id,
        ]);
        
        // Delete the category
        $categoryId = $category->id;
        $category->delete();
        
        // Check category is deleted
        $this->assertDatabaseMissing('categories', [
            'id' => $categoryId,
        ]);
        
        // Check if todos are now without a category (null category_id)
        // or deleted depending on the cascade settings
        
        // If onDelete('set null') is defined in the migration:
        $this->assertDatabaseHas('todos', [
            'title' => 'Category Todo 1',
            'category_id' => null,
        ]);
        
        $this->assertDatabaseHas('todos', [
            'title' => 'Category Todo 2',
            'category_id' => null,
        ]);
        
        // If onDelete('cascade') is defined in the migration, use this instead:
        // $this->assertDatabaseMissing('todos', [
        //     'title' => 'Category Todo 1',
        // ]);
        // 
        // $this->assertDatabaseMissing('todos', [
        //     'title' => 'Category Todo 2',
        // ]);
    }
}
