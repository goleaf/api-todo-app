<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-30 days', 'now');
        $duration = fake()->numberBetween(15, 480); // 15 minutes to 8 hours
        $endedAt = (clone $startedAt)->modify("+{$duration} minutes");

        return [
            'user_id' => User::factory(),
            'task_id' => Task::factory(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'description' => fake()->optional()->sentence(),
            'deleted_at' => null,
        ];
    }

    /**
     * Indicate that the time entry is ongoing.
     */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => fake()->dateTimeBetween('-2 hours', 'now'),
            'ended_at' => null,
        ]);
    }
} 