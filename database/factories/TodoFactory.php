<?php

namespace Database\Factories;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Todo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'completed' => $this->faker->boolean(20), // 20% chance of being completed
            'priority' => $this->faker->randomElement([0, 1, 2]), // Low, Medium, High
            'due_date' => $this->faker->optional(70)->dateTimeBetween('-1 week', '+2 weeks'), // 70% chance of having a due date
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the todo is completed.
     */
    public function completed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'completed' => true,
            ];
        });
    }

    /**
     * Indicate that the todo is high priority.
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
     * Indicate that the todo is overdue.
     */
    public function overdue(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'due_date' => $this->faker->dateTimeBetween('-2 weeks', '-1 day'),
                'completed' => false,
            ];
        });
    }
}
