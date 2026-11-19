<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\StockOpname;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockOpname>
 */
class StockOpnameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ingredient_id' => Ingredient::factory(),
            'user_id' => User::factory()->manager(),
            'opname_date' => now()->toDateString(),
            'system_stock' => 1000.00,
            'physical_stock' => 950.00,
            'difference' => -50.00,
            'cost_difference' => -10000.00,
            'status' => 'adjusted',
            'notes' => 'Spill during morning rush',
        ];
    }
}
