<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (!$email || !$password) {
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'CADY Admin'),
                'phone' => env('ADMIN_PHONE'),
                'role' => Role::ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make($password),
            ]
        );
    }
}