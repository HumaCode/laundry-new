<?php

namespace Database\Seeders;

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
        $users = [
            [
                'name' => 'Developer',
                'username' => 'dev',
                'email' => 'dev@example.com',
                'password' => Hash::make('123'),
                'gender' => 'male',
                'is_active' => '1',
                'role' => 'dev',
            ],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('123'),
                'gender' => 'male',
                'is_active' => '1',
                'role' => 'admin',
            ],
            [
                'name' => 'Kasir Laundry',
                'username' => 'kasir',
                'email' => 'kasir@example.com',
                'password' => Hash::make('123'),
                'gender' => 'female',
                'is_active' => '1',
                'role' => 'kasir',
            ],
            [
                'name' => 'Customer Laundry',
                'username' => 'customer',
                'email' => 'customer@example.com',
                'password' => Hash::make('123'),
                'gender' => 'female',
                'is_active' => '1',
                'role' => 'customer',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );

            // Assign role
            $user->assignRole($role);
        }
    }
}
