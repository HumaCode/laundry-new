<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'dev',
                'slug' => 'dev',
                'type_role' => 'developer',
                'color' => '#6366f1',
                'priority' => 1,
                'is_active' => '1',
                'description' => 'System Developer role with unrestricted access.',
                'guard_name' => 'web',
            ],
            [
                'name' => 'admin',
                'slug' => 'admin',
                'type_role' => 'administrator',
                'color' => '#ef4444',
                'priority' => 2,
                'is_active' => '1',
                'description' => 'Administrator role for outlet and system management.',
                'guard_name' => 'web',
            ],
            [
                'name' => 'kasir',
                'slug' => 'kasir',
                'type_role' => 'cashier',
                'color' => '#3b82f6',
                'priority' => 3,
                'is_active' => '1',
                'description' => 'Cashier role for transactions and order management.',
                'guard_name' => 'web',
            ],
            [
                'name' => 'customer',
                'slug' => 'customer',
                'type_role' => 'customer',
                'color' => '#10b981',
                'priority' => 4,
                'is_active' => '1',
                'description' => 'Customer role for placing orders and checking statuses.',
                'guard_name' => 'web',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
