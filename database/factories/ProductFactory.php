<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
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
        $name = fake()->words(2, true).' Coffee';

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->randomNumber(4),
            'sku' => 'PRD-'.fake()->unique()->numerify('####'),
            'description' => fake()->sentence(),
            'price' => fake()->randomElement([22000, 25000, 28000, 32000, 35000]),
            'hpp' => 8500.00,
            'is_available' => true,
        ];
    }
}
