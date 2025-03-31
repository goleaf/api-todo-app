<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'color' => '#' . substr(md5(fake()->word()), 0, 6),
            'user_id' => \App\Models\User::factory(),
            'usage_count' => fake()->numberBetween(0, 20),
        ];
    }
}
