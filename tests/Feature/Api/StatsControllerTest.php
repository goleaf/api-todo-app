<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Task;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can get their statistics.
     */
    public function test_user_can_get_their_statistics(): void
    {
        $user = User::factory()->create();
        $this->seedUserData($user);

        $response = $this->actingAs($user)->getJson('/api/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'todos_count',
                'completed_todos_count',
                'tasks_count',
                'completed_tasks_count',
                'categories_count',
                'todos_by_category',
                'todos_by_completion',
                'tasks_by_priority',
            ],
        ]);
    }

    /**
     * Test user gets only their own statistics.
     */
    public function test_user_gets_only_their_own_statistics(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Seed data for user1
        $this->seedUserData($user1);

        // Seed data for user2
        $this->seedUserData($user2);

        $response = $this->actingAs($user1)->getJson('/api/stats');

        $response->assertStatus(200);
        $response->assertJsonPath('data.todos_count', 3); // Only user1's todos
    }

    /**
     * Test user can get todos by date range.
     */
    public function test_user_can_get_todos_by_date_range(): void
    {
        $user = User::factory()->create();

        // Create todos with different due dates
        $pastTodo = Todo::factory()->create([
            'user_id' => $user->id,
            'due_date' => now()->subDays(7),
        ]);

        $currentTodo = Todo::factory()->create([
            'user_id' => $user->id,
            'due_date' => now(),
        ]);

        $futureTodo = Todo::factory()->create([
            'user_id' => $user->id,
            'due_date' => now()->addDays(7),
        ]);

        $response = $this->actingAs($user)->getJson('/api/stats/todos-by-date?start_date='.
            now()->subDays(1)->toDateString().'&end_date='.now()->addDays(1)->toDateString());

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data'); // Only the current todo
    }

    /**
     * Test user can get tasks completion rate.
     */
    public function test_user_can_get_tasks_completion_rate(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // Create 4 tasks: 2 completed, 2 incomplete
        Task::factory()->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
            'completed' => true,
            'completed_at' => now(),
        ]);

        Task::factory()->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
            'completed' => true,
            'completed_at' => now(),
        ]);

        Task::factory()->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
            'completed' => false,
            'completed_at' => null,
        ]);

        Task::factory()->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
            'completed' => false,
            'completed_at' => null,
        ]);

        $response = $this->actingAs($user)->getJson('/api/stats/completion-rate');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'completion_rate' => 50, // 2 out of 4 = 50%
            ],
        ]);
    }

    /**
     * Test user can get category distribution.
     */
    public function test_user_can_get_category_distribution(): void
    {
        $user = User::factory()->create();

        // Create 2 categories
        $category1 = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Work',
        ]);

        $category2 = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Personal',
        ]);

        // Create todos in different categories
        Todo::factory(3)->create([
            'user_id' => $user->id,
            'category_id' => $category1->id,
        ]);

        Todo::factory(2)->create([
            'user_id' => $user->id,
            'category_id' => $category2->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/stats/categories');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJson([
            'data' => [
                [
                    'category' => 'Work',
                    'count' => 3,
                ],
                [
                    'category' => 'Personal',
                    'count' => 2,
                ],
            ],
        ]);
    }

    /**
     * Test user can get productivity over time.
     */
    public function test_user_can_get_productivity_over_time(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // Create tasks completed on different dates
        Task::factory()->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
            'completed' => true,
            'completed_at' => now()->subDays(2),
        ]);

        Task::factory(2)->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
            'completed' => true,
            'completed_at' => now()->subDay(),
        ]);

        Task::factory(3)->create([
            'todo_id' => $todo->id,
            'user_id' => $user->id,
            'completed' => true,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/stats/productivity?days=7');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'date',
                    'completed_tasks',
                ],
            ],
        ]);
        $response->assertJsonCount(7, 'data'); // Last 7 days
    }

    /**
     * Helper method to seed user data for testing.
     */
    private function seedUserData(User $user): void
    {
        // Create categories
        $categories = Category::factory(2)->create(['user_id' => $user->id]);

        // Create todos in categories
        $todos = [];
        foreach ($categories as $category) {
            $todos[] = Todo::factory()->create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'completed' => false,
            ]);
        }

        // One completed todo
        $todos[] = Todo::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categories[0]->id,
            'completed' => true,
        ]);

        // Create tasks for each todo
        foreach ($todos as $todo) {
            // Some completed, some incomplete tasks
            Task::factory()->create([
                'todo_id' => $todo->id,
                'user_id' => $user->id,
                'completed' => true,
                'priority' => 1,
            ]);

            Task::factory()->create([
                'todo_id' => $todo->id,
                'user_id' => $user->id,
                'completed' => false,
                'priority' => 2,
            ]);
        }
    }
}
