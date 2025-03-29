<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $colors = [
            '#FF5733', // Red-Orange
            '#33FF57', // Green
            '#3357FF', // Blue
            '#F3FF33', // Yellow
            '#FF33F3', // Magenta
            '#33FFF3', // Cyan
            '#9B59B6', // Purple
            '#2ECC71', // Emerald
            '#E74C3C', // Red
            '#3498DB', // Blue
        ];

        return [
            'name' => $this->faker->unique()->word(),
            'color' => $this->faker->randomElement($colors),
            'user_id' => User::factory(),
        ];
    }
} 