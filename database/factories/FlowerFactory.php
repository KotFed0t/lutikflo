<?php

namespace Database\Factories;

use App\Models\FlowerType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flower>
 */
class FlowerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'price' => rand(100, 300),
            'flower_type_id' => rand(1, 5)
        ];
    }
}
