<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductRecipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductRecipe>
 */
class ProductRecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'ingredient_id' => Ingredient::factory(),
            'quantity' => 18.00,
            'unit' => 'gram',
            'cost' => 3600.00,
            'notes' => 'Standard portion',
        ];
    }
}
