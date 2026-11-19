<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_name' => 'Kopi Susu Ratocorner',
            'quantity' => 1,
            'unit_price' => 25000.00,
            'hpp' => 8500.00,
            'subtotal' => 25000.00,
            'notes' => 'Less sugar',
        ];
    }
}
