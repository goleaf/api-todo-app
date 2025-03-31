<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
    }

    /** @test */
    public function it_belongs_to_a_category()
    {
        $category = Category::factory()->create();
        $task = Task::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $task->category);
        $this->assertEquals($category->id, $task->category->id);
    }

    /** @test */
    public function it_can_be_marked_as_complete()
    {
        $task = Task::factory()->create(['completed' => false]);

        $task->markAsComplete();

        $this->assertTrue($task->completed);
        $this->assertNotNull($task->completed_at);
        $this->assertEquals(100, $task->progress);
    }

    /** @test */
    public function it_can_be_marked_as_incomplete()
    {
        $task = Task::factory()->create(['completed' => true, 'completed_at' => now()]);

        $task->markAsIncomplete();

        $this->assertFalse($task->completed);
        $this->assertNull($task->completed_at);
    }

    /** @test */
    public function it_can_scope_tasks_by_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task1 = Task::factory()->create(['user_id' => $user1->id]);
        Task::factory()->create(['user_id' => $user2->id]);

        $tasks = Task::forUser($user1->id)->get();

        $this->assertCount(1, $tasks);
        $this->assertEquals($task1->id, $tasks->first()->id);
    }

    /** @test */
    public function it_can_scope_tasks_by_category()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $task1 = Task::factory()->create(['category_id' => $category1->id]);
        Task::factory()->create(['category_id' => $category2->id]);

        $tasks = Task::inCategory($category1->id)->get();

        $this->assertCount(1, $tasks);
        $this->assertEquals($task1->id, $tasks->first()->id);
    }

    /** @test */
    public function it_can_scope_completed_tasks()
    {
        Task::factory()->create(['completed' => false]);
        $completedTask = Task::factory()->create(['completed' => true]);

        $tasks = Task::completed()->get();

        $this->assertCount(1, $tasks);
        $this->assertEquals($completedTask->id, $tasks->first()->id);
    }

    /** @test */
    public function it_can_scope_incomplete_tasks()
    {
        $incompleteTask = Task::factory()->create(['completed' => false]);
        Task::factory()->create(['completed' => true]);

        $tasks = Task::incomplete()->get();

        $this->assertCount(1, $tasks);
        $this->assertEquals($incompleteTask->id, $tasks->first()->id);
    }

    /** @test */
    public function it_can_scope_tasks_due_today()
    {
        Task::factory()->create(['due_date' => Carbon::yesterday(), 'completed' => false]);
        $todayTask = Task::factory()->create(['due_date' => Carbon::today(), 'completed' => false]);
        Task::factory()->create(['due_date' => Carbon::tomorrow(), 'completed' => false]);

        $tasks = Task::dueToday()->get();

        $this->assertCount(1, $tasks);
        $this->assertEquals($todayTask->id, $tasks->first()->id);
    }

    /** @test */
    public function it_has_the_correct_priority_label()
    {
        $lowTask = Task::factory()->create(['priority' => 0]);
        $mediumTask = Task::factory()->create(['priority' => 1]);
        $highTask = Task::factory()->create(['priority' => 2]);

        $this->assertEquals('Low', $lowTask->priority_label);
        $this->assertEquals('Medium', $mediumTask->priority_label);
        $this->assertEquals('High', $highTask->priority_label);
    }

    /** @test */
    public function it_has_the_correct_priority_color()
    {
        $lowTask = Task::factory()->create(['priority' => 0]);
        $mediumTask = Task::factory()->create(['priority' => 1]);
        $highTask = Task::factory()->create(['priority' => 2]);

        $this->assertEquals('success', $lowTask->priority_color);
        $this->assertEquals('warning', $mediumTask->priority_color);
        $this->assertEquals('danger', $highTask->priority_color);
    }
}
