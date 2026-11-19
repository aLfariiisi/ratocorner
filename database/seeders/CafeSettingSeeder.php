<?php

namespace Database\Seeders;

use App\Models\CafeSetting;
use Illuminate\Database\Seeder;

class CafeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CafeSetting::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Ratocorner',
                'tagline' => 'Specialty Coffee & Eatery',
                'phone' => '0812-3456-7890',
                'email' => 'info@ratocorner.com',
                'address' => 'Jl. Senopati No. 45, Kebayoran Baru, Jakarta Selatan',
                'logo_path' => 'images/logo.png',
                'tax_percentage' => 11.00,
                'service_charge_percentage' => 5.00,
                'receipt_header' => "*** RATOCORNER SPECIALTY COFFEE ***\nJl. Senopati No. 45, Kebayoran Baru, Jakarta Selatan\nTelp: 0812-3456-7890",
                'receipt_footer' => "Terima kasih atas kunjungan Anda!\nFollow Instagram kami: @ratocorner.coffee\nWiFi: Ratocorner-Guest / Pass: kopienak",
                'currency' => 'IDR',
            ]
        );
    }
}
