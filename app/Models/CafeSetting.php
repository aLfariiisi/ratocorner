<?php

namespace App\Models;

use Database\Factories\CafeSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'tagline',
    'phone',
    'email',
    'address',
    'wifi_name',
    'wifi_password',
    'logo_path',
    'tax_percentage',
    'service_charge_percentage',
    'receipt_header',
    'receipt_footer',
    'currency',
])]
class CafeSetting extends Model
{
    /** @use HasFactory<CafeSettingFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tax_percentage' => 'decimal:2',
            'service_charge_percentage' => 'decimal:2',
        ];
    }

    /**
     * Retrieve the active cafe settings singleton or default.
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Ratocorner',
                'tagline' => 'Specialty Coffee & Eatery',
                'currency' => 'IDR',
                'wifi_name' => 'Ratocorner-Guest',
                'wifi_password' => 'kopienak',
                'tax_percentage' => 11.00,
                'service_charge_percentage' => 5.00,
            ]
        );
    }
}
