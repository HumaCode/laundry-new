<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
            OutletSeeder::class,
            BusinessSeeder::class,
            EmployeeSeeder::class,
            ServiceSeeder::class,
            InventorySeeder::class,
            OrderSeeder::class,
            PickupSeeder::class,
        ]);

        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
        ]);

        $user->assignRole('admin');
    }
}
