<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        // Owner Account
        User::updateOrCreate(
            ['email' => 'owner@ratocorner.com'],
            [
                'name' => 'Owner Ratocorner',
                'password' => $defaultPassword,
                'role' => UserRole::Owner,
                'email_verified_at' => now(),
            ]
        );

        // Manager Account
        User::updateOrCreate(
            ['email' => 'manager@ratocorner.com'],
            [
                'name' => 'Manager Ratocorner',
                'password' => $defaultPassword,
                'role' => UserRole::Manager,
                'email_verified_at' => now(),
            ]
        );

        // Kasir (Cashier) Account
        User::updateOrCreate(
            ['email' => 'kasir@ratocorner.com'],
            [
                'name' => 'Kasir Ratocorner',
                'password' => $defaultPassword,
                'role' => UserRole::Kasir,
                'email_verified_at' => now(),
            ]
        );
    }
}
