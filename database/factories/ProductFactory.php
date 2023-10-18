<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => fake()->word().'-'.rand(1, 50),
            'category_id' => rand(1, 8),
            'price' => rand(500, 5000),
            'is_active' => fake()->boolean(),
            'description' => fake()->text(),
            'main_img' => fake()->image(),
            'order' => fake()->randomDigit()
        ];
    }
}
