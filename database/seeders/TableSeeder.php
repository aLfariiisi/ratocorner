<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            [
                'table_number' => '01',
                'name' => 'Meja 01 (Indoor Window)',
                'capacity' => 2,
                'location' => 'Indoor',
                'status' => 'available',
                'qr_code_token' => 'qr-table-01-'.Str::random(12),
                'is_active' => true,
            ],
            [
                'table_number' => '02',
                'name' => 'Meja 02 (Indoor Center)',
                'capacity' => 4,
                'location' => 'Indoor',
                'status' => 'available',
                'qr_code_token' => 'qr-table-02-'.Str::random(12),
                'is_active' => true,
            ],
            [
                'table_number' => '03',
                'name' => 'Meja 03 (Indoor Center)',
                'capacity' => 4,
                'location' => 'Indoor',
                'status' => 'available',
                'qr_code_token' => 'qr-table-03-'.Str::random(12),
                'is_active' => true,
            ],
            [
                'table_number' => '04',
                'name' => 'Meja 04 (Indoor Sofa)',
                'capacity' => 6,
                'location' => 'Indoor',
                'status' => 'available',
                'qr_code_token' => 'qr-table-04-'.Str::random(12),
                'is_active' => true,
            ],
            [
                'table_number' => '05',
                'name' => 'Meja 05 (Slow Bar)',
                'capacity' => 2,
                'location' => 'Indoor Bar',
                'status' => 'available',
                'qr_code_token' => 'qr-table-05-'.Str::random(12),
                'is_active' => true,
            ],
            [
                'table_number' => 'VIP',
                'name' => 'Ruang VIP Meeting',
                'capacity' => 8,
                'location' => 'Lantai 2 VIP',
                'status' => 'available',
                'qr_code_token' => 'qr-table-vip-'.Str::random(12),
                'is_active' => true,
            ],
            [
                'table_number' => 'OUT-01',
                'name' => 'Meja Outdoor 01',
                'capacity' => 4,
                'location' => 'Outdoor Garden',
                'status' => 'available',
                'qr_code_token' => 'qr-table-out01-'.Str::random(12),
                'is_active' => true,
            ],
            [
                'table_number' => 'OUT-02',
                'name' => 'Meja Outdoor 02',
                'capacity' => 4,
                'location' => 'Outdoor Garden',
                'status' => 'available',
                'qr_code_token' => 'qr-table-out02-'.Str::random(12),
                'is_active' => true,
            ],
        ];

        foreach ($tables as $table) {
            Table::updateOrCreate(
                ['table_number' => $table['table_number']],
                $table
            );
        }
    }
}
