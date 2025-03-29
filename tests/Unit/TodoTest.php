<?php

namespace Tests\Unit;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $todo->user);
        $this->assertEquals($user->id, $todo->user->id);
    }

    /** @test */
    public function it_can_be_completed()
    {
        $todo = Todo::factory()->create(['completed' => false]);

        $this->assertFalse($todo->completed);

        $todo->update(['completed' => true]);

        $this->assertTrue($todo->fresh()->completed);
    }

    /** @test */
    public function it_can_be_filtered_by_completion_status()
    {
        // Create completed todos
        Todo::factory()->count(3)->create(['completed' => true]);

        // Create incomplete todos
        Todo::factory()->count(2)->create(['completed' => false]);

        $this->assertEquals(3, Todo::where('completed', true)->count());
        $this->assertEquals(2, Todo::where('completed', false)->count());
    }

    /** @test */
    public function it_can_have_a_due_date()
    {
        $dueDate = now()->addDays(5)->format('Y-m-d H:i:s');
        $todo = Todo::factory()->create(['due_date' => $dueDate]);

        $this->assertEquals($dueDate, $todo->due_date);
    }

    /** @test */
    public function it_can_have_a_priority_level()
    {
        $todo = Todo::factory()->create(['priority' => 2]); // High priority

        $this->assertEquals(2, $todo->priority);
    }
}
