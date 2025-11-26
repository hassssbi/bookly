<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user_seed = [
            [
                'email' => 'admin@example.com',
                'name' => 'Admin',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'email' => 'seller1@example.com',
                'name' => 'Seller 1',
                'password' => 'password',
                'role' => 'seller',
            ],
            [
                'email' => 'seller2@example.com',
                'name' => 'Seller 2',
                'password' => 'password',
                'role' => 'seller',
            ],
            [
                'email' => 'customer1@example.com',
                'name' => 'Customer 1',
                'password' => 'password',
                'role' => 'customer',
            ],
            [
                'email' => 'customer2@example.com',
                'name' => 'Customer 2',
                'password' => 'password',
                'role' => 'customer',
            ],
        ];

        foreach ($user_seed as $user) {
            User::firstOrCreate([
                'email' => $user['email'],
                'name' => $user['name'],
                'password' => Hash::make($user['password']),
                'role' => $user['role'],
            ]);
        }

        /* User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // change later
                'role' => 'admin',
            ]
        ); */
    }
}
