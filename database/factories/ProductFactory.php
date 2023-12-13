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
            'category_id' => rand(1, 7),
            'price' => rand(500, 5000),
            'is_active' => fake()->boolean(),
            'description' => 'lorem Ipsum daksd nasjnd kasnd kajksnd nasdnkmd d memd mekm d.',
            'main_img' => 'img/path',
            'order' => rand(1, 20)
        ];
    }
}
