<?php

namespace Database\Factories;

use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Table>
 */
class TableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'table_number' => 'T-'.fake()->unique()->numberBetween(1, 99),
            'name' => 'Meja '.fake()->numberBetween(1, 99),
            'capacity' => fake()->randomElement([2, 4, 6, 8]),
            'location' => fake()->randomElement(['Indoor', 'Outdoor', 'Bar Area', 'Lantai 2']),
            'status' => 'available',
            'qr_code_token' => (string) Str::uuid(),
            'is_active' => true,
        ];
    }
}
