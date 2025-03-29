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
        $completed = $this->faker->boolean(30); // 30% chance of being completed
        $created_at = $this->faker->dateTimeBetween('-30 days', '-1 day');
        $completed_at = $completed ? $this->faker->dateTimeBetween($created_at, 'now') : null;
        $due_date = $this->faker->dateTimeBetween('now', '+30 days');

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'completed' => $completed,
            'completed_at' => $completed_at,
            'priority' => $this->faker->randomElement([1, 2, 3]), // Low, Medium, High
            'progress' => $completed ? 100 : $this->faker->numberBetween(0, 90),
            'due_date' => $due_date,
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'created_at' => $created_at,
            'updated_at' => $completed_at ?? $created_at,
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
