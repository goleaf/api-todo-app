<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task_with_due_date()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'pending',
            'due_date' => now()->addDays(7)->toDateString(),
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Test Task',
                'description' => 'This is a test task',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id,
        ]);

        // Get the created task and check due_date
        $task = Task::where('title', 'Test Task')->first();
        $this->assertEquals(
            $taskData['due_date'],
            date('Y-m-d', strtotime($task->due_date))
        );
    }

    public function test_can_update_task_due_date()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'due_date' => now()->addDays(5)->toDateString(),
        ]);

        $this->actingAs($user);

        $updatedData = [
            'due_date' => now()->addDays(10)->toDateString(),
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'due_date' => $updatedData['due_date'].' 00:00:00',
        ]);
    }

    public function test_can_create_task_without_due_date()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $taskData = [
            'title' => 'Task Without Due Date',
            'description' => 'This task has no deadline',
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Task Without Due Date',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Task Without Due Date',
            'user_id' => $user->id,
            'due_date' => null,
        ]);
    }
}
