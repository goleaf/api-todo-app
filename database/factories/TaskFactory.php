<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
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
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'completed' => $this->faker->boolean(20),
            'priority' => $this->faker->randomElement([0, 1, 2]), // Low, Medium, High
            'progress' => $this->faker->numberBetween(0, 100), // Random progress
            'due_date' => $this->faker->randomElement([
                null,
                Carbon::today(),
                Carbon::yesterday(),
                Carbon::tomorrow(),
                Carbon::today()->addDays(rand(2, 14)),
                Carbon::today()->subDays(rand(2, 14)),
            ]),
            'reminder_at' => $this->faker->optional(40)->dateTimeBetween('now', '+1 week'), // 40% chance of having a reminder
            'user_id' => User::factory(),
            'category_id' => function () {
                return Category::factory()->create()->id;
            },
            'tags' => json_encode($this->faker->randomElements(['work', 'home', 'urgent', 'meeting', 'personal', 'shopping', 'health'], $this->faker->numberBetween(0, 3))),
            'completed_at' => function (array $attributes) {
                return $attributes['completed'] ? Carbon::now()->subDays(rand(0, 5)) : null;
            },
        ];
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'completed' => true,
            'completed_at' => Carbon::now()->subDays(rand(0, 5)),
            'progress' => 100,
        ]);
    }

    /**
     * Indicate that the task is not completed.
     */
    public function active(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'completed' => false,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is due today.
     */
    public function dueToday(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => Carbon::today(),
        ]);
    }

    /**
     * Indicate that the task is overdue.
     */
    public function overdue(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => Carbon::today()->subDays(rand(1, 7)),
        ]);
    }

    /**
     * Indicate that the task is upcoming.
     */
    public function upcoming(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => Carbon::today()->addDays(rand(1, 14)),
        ]);
    }

    /**
     * Indicate that the task has no due date.
     */
    public function noDueDate(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => null,
        ]);
    }

    /**
     * Indicate that the task is high priority.
     */
    public function highPriority(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 2,
            ];
        });
    }

    /**
     * Indicate that the task has medium priority.
     */
    public function mediumPriority(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 1,
            ];
        });
    }

    /**
     * Indicate that the task has low priority.
     */
    public function lowPriority(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 0,
            ];
        });
    }

    /**
     * Indicate that the task has low progress.
     */
    public function lowProgress(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'progress' => $this->faker->numberBetween(0, 30),
                'completed' => false,
            ];
        });
    }

    /**
     * Indicate that the task belongs to a specific category.
     */
    public function inCategory(Category $category): Factory
    {
        return $this->state(function (array $attributes) use ($category) {
            return [
                'category_id' => $category->id,
            ];
        });
    }

    /**
     * Indicate that the task has specific tags.
     */
    public function withTags(array $tags): Factory
    {
        return $this->state(function (array $attributes) use ($tags) {
            return [
                'tags' => json_encode($tags),
            ];
        });
    }
}
