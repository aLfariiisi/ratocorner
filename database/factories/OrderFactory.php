<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-'.now()->format('Ymd').'-'.fake()->unique()->numerify('####'),
            'table_id' => Table::factory(),
            'customer_id' => Customer::factory(),
            'cashier_id' => User::factory()->kasir(),
            'order_type' => 'dine_in',
            'customer_name' => fake()->name(),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => null,
            'subtotal' => 50000.00,
            'tax_amount' => 5500.00,
            'service_charge' => 2500.00,
            'discount_amount' => 0.00,
            'total_amount' => 58000.00,
            'paid_amount' => 0.00,
            'change_amount' => 0.00,
            'notes' => fake()->sentence(),
            'paid_at' => null,
        ];
    }
}
