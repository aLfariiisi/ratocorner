<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
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
            'phone' => fake()->unique()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'loyalty_points' => fake()->numberBetween(0, 500),
            'total_orders' => fake()->numberBetween(0, 50),
            'total_spent' => fake()->randomFloat(2, 50000, 2000000),
            'notes' => fake()->sentence(),
        ];
    }
}
