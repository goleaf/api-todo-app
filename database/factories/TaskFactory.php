<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'due_date' => $this->faker->dateTimeBetween('-1 week', '+2 weeks'),
            'priority' => $this->faker->randomElement([1, 2, 3, 4]),
            'completed' => $this->faker->boolean(20), // 20% chance of being completed
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'tags' => $this->faker->randomElements(['work', 'personal', 'urgent', 'shopping', 'health', 'finance', 'family', 'learning'], $this->faker->numberBetween(0, 3)),
            'notes' => $this->faker->optional(0.7)->paragraph(),
            'attachments' => null,
        ];
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'completed' => true,
        ]);
    }

    /**
     * Indicate that the task is not completed.
     */
    public function incomplete(): self
    {
        return $this->state(fn (array $attributes) => [
            'completed' => false,
        ]);
    }

    /**
     * Set the task's priority.
     */
    public function priority(int $priority): self
    {
        return $this->state(fn (array $attributes) => [
            'priority' => $priority,
        ]);
    }

    /**
     * Indicate that the task is due today.
     */
    public function dueToday(): self
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => Carbon::today(),
        ]);
    }

    /**
     * Indicate that the task is overdue.
     */
    public function overdue(): self
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('-2 weeks', '-1 day'),
            'completed' => false,
        ]);
    }

    /**
     * Indicate that the task is upcoming (due in the future).
     */
    public function upcoming(): self
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('+1 day', '+2 weeks'),
            'completed' => false,
        ]);
    }

    /**
     * Indicate that the task has specific tags.
     */
    public function withTags(array $tags): self
    {
        return $this->state(fn (array $attributes) => [
            'tags' => $tags,
        ]);
    }

    /**
     * Indicate that the task belongs to a specific user.
     */
    public function forUser(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the task belongs to a specific category.
     */
    public function inCategory(Category $category): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $category->user_id,
            'category_id' => $category->id,
        ]);
    }
}
