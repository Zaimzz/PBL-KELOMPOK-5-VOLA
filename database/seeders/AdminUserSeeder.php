<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@vola.test'],
            [
                'name' => 'Admin Vola',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'role' => UserRole::Admin,
                'status' => UserStatus::Active,
            ]
        );
    }
}
