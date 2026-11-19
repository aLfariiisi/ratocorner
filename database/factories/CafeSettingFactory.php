<?php

namespace Database\Factories;

use App\Models\CafeSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CafeSetting>
 */
class CafeSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Ratocorner',
            'tagline' => 'Specialty Coffee & Eatery',
            'phone' => '081234567890',
            'email' => 'hello@ratocorner.com',
            'address' => 'Jl. Kopi Ratocorner No. 1, Jakarta Selatan',
            'tax_percentage' => 11.00,
            'service_charge_percentage' => 5.00,
            'receipt_header' => 'Selamat Menikmati Kopi di Ratocorner!',
            'receipt_footer' => 'Terima kasih atas kunjungan Anda.',
            'currency' => 'IDR',
        ];
    }
}
