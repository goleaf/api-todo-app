<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $colors = [
            'red', 'orange', 'yellow', 'green', 'blue', 'indigo', 'purple',
            'pink', 'teal', 'cyan', 'amber', 'lime', 'emerald', 'sky', 'violet',
        ];

        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->optional()->sentence(),
            'color' => $this->faker->hexColor(),
            'user_id' => User::factory(),
            'icon' => $this->faker->randomElement([
                'tag', 'folder', 'book', 'briefcase', 'star', 'heart', 'flag',
                'home', 'user', 'cog', 'file', 'calendar', 'shopping-cart',
                'gift', 'money-bill', 'plane', 'car', 'graduation-cap', 'utensils',
            ]),
        ];
    }

    /**
     * Indicate that the category belongs to a specific user.
     */
    public function forUser(User $user): Factory
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }

    /**
     * Indicate that the category is for work.
     *
     * @return $this
     */
    public function work(): self
    {
        return $this->state(function () {
            return [
                'name' => 'Work',
                'description' => 'Work-related tasks and projects',
                'color' => 'blue',
                'icon' => 'briefcase',
            ];
        });
    }

    /**
     * Indicate that the category is for personal use.
     *
     * @return $this
     */
    public function personal(): self
    {
        return $this->state(function () {
            return [
                'name' => 'Personal',
                'description' => 'Personal tasks and errands',
                'color' => 'green',
                'icon' => 'user',
            ];
        });
    }

    /**
     * Indicate that the category is for home.
     *
     * @return $this
     */
    public function home(): self
    {
        return $this->state(function () {
            return [
                'name' => 'Home',
                'description' => 'Home tasks and maintenance',
                'color' => 'orange',
                'icon' => 'home',
            ];
        });
    }
}
