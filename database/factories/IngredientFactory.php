<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'name' => fake()->words(2, true),
            'code' => 'ING-'.fake()->unique()->numerify('###'),
            'unit' => fake()->randomElement(['gram', 'ml', 'pcs', 'shot']),
            'current_stock' => fake()->randomFloat(2, 500, 5000),
            'minimum_stock' => 100.00,
            'cost_per_unit' => fake()->randomFloat(2, 50, 1000),
            'description' => fake()->sentence(),
        ];
    }
}
