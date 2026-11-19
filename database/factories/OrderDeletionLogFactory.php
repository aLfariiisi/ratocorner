<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderDeletionLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderDeletionLog>
 */
class OrderDeletionLogFactory extends Factory
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
            'order_number' => 'ORD-'.now()->format('Ymd').'-0001',
            'deleted_by' => User::factory()->manager(),
            'reason' => 'Customer changed their mind before payment',
            'order_data' => [
                'order_number' => 'ORD-'.now()->format('Ymd').'-0001',
                'subtotal' => 50000,
                'items' => [],
            ],
        ];
    }
}
