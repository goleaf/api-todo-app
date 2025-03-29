<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(rand(4, 8)),
            'description' => $this->faker->paragraphs(rand(1, 3), true),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['todo', 'in_progress', 'done']),
            'completed' => $this->faker->boolean(30),
            'completed_at' => function (array $attributes) {
                return $attributes['completed'] ? $this->faker->dateTimeBetween('-30 days', 'now') : null;
            },
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Task $task) {
            // Any after making configurations
        })->afterCreating(function (Task $task) {
            // Ensure progress is 100 if completed
            if ($task->completed) {
                $task->progress = 100;
                $task->save();
            }
        });
    }

    /**
     * Set the task as completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'completed' => true,
                'progress' => 100,
                'completed_at' => $this->faker->dateTimeBetween($attributes['created_at'], 'now'),
            ];
        });
    }

    /**
     * Set the task as not completed.
     */
    public function notCompleted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'completed' => false,
                'progress' => $this->faker->numberBetween(0, 90),
                'completed_at' => null,
            ];
        });
    }

    /**
     * Set the task with high priority.
     */
    public function highPriority(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 3,
            ];
        });
    }

    /**
     * Set the task with medium priority.
     */
    public function mediumPriority(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 2,
            ];
        });
    }

    /**
     * Set the task with low priority.
     */
    public function lowPriority(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 1,
            ];
        });
    }

    /**
     * Set the task with a due date in the past.
     */
    public function overdue(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'due_date' => $this->faker->dateTimeBetween('-10 days', '-1 day'),
                'completed' => false,
            ];
        });
    }

    /**
     * Set the task with a due date for today.
     */
    public function dueToday(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'due_date' => now()->format('Y-m-d'),
            ];
        });
    }
}
