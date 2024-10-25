<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'name' => [
                'en' => $this->faker->sentence,
                'ar' => $this->faker->sentence,
            ],
            'description' => [
                'en' => $this->faker->paragraph,
                'ar' => $this->faker->paragraph,
            ],
            'price' => rand(0, 10),
            'image' => $this->faker->imageUrl(100, 100, 'food'),
            'discount' => $this->faker->randomElement([rand(0, 10), null]),
            'order' => $this->faker->randomNumber()
        ];
    }
}
