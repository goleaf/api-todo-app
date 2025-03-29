<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Todo model relationships.
     */
    public function test_todo_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $todo = Todo::create([
            'title' => 'Test Todo',
            'user_id' => $user->id,
            'completed' => false,
            'priority' => 0,
        ]);

        $this->assertInstanceOf(User::class, $todo->user);
        $this->assertEquals($user->id, $todo->user->id);
    }

    /**
     * Test Todo model belongs to category relationship.
     */
    public function test_todo_belongs_to_category(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Work',
            'user_id' => $user->id,
        ]);

        $todo = Todo::create([
            'title' => 'Test Todo',
            'user_id' => $user->id,
            'completed' => false,
            'priority' => 0,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Category::class, $todo->category);
        $this->assertEquals($category->id, $todo->category->id);
    }

    /**
     * Test Todo model casts.
     */
    public function test_todo_has_proper_casts(): void
    {
        $todo = new Todo;

        $this->assertEquals('boolean', $todo->getCasts()['completed']);
        $this->assertEquals('datetime', $todo->getCasts()['due_date']);
        $this->assertEquals('datetime', $todo->getCasts()['reminder_at']);
        $this->assertEquals('integer', $todo->getCasts()['priority']);
        $this->assertEquals('integer', $todo->getCasts()['progress']);
    }

    /**
     * Test Todo model fillable attributes.
     */
    public function test_todo_has_correct_fillable_attributes(): void
    {
        $todo = new Todo;
        $fillable = $todo->getFillable();

        $this->assertContains('title', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('completed', $fillable);
        $this->assertContains('due_date', $fillable);
        $this->assertContains('reminder_at', $fillable);
        $this->assertContains('priority', $fillable);
        $this->assertContains('progress', $fillable);
        $this->assertContains('category_id', $fillable);
    }

    /**
     * Test creating a todo with all attributes.
     */
    public function test_can_create_todo_with_all_attributes(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Work',
            'user_id' => $user->id,
        ]);

        $dueDate = now()->addDays(7);
        $reminderDate = now()->addDays(6);

        $todo = Todo::create([
            'title' => 'Complete project',
            'description' => 'Finish the project by next week',
            'completed' => false,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 2,
            'progress' => 50,
            'due_date' => $dueDate,
            'reminder_at' => $reminderDate,
        ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Complete project',
            'description' => 'Finish the project by next week',
            'completed' => 0,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 2,
            'progress' => 50,
        ]);

        // Check dates separately due to format conversion
        $this->assertEquals(
            $dueDate->setMicroseconds(0)->toDateTimeString(),
            $todo->due_date->setMicroseconds(0)->toDateTimeString()
        );

        $this->assertEquals(
            $reminderDate->setMicroseconds(0)->toDateTimeString(),
            $todo->reminder_at->setMicroseconds(0)->toDateTimeString()
        );
    }
}
