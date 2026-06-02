<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Master\Order;
use App\Models\Master\Outlet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = Outlet::all();
        if ($outlets->isEmpty()) {
            $this->command->warn('No outlets found. Please seed outlets first.');
            return;
        }

        // 1. Seed some customer users with various tiers
        $customersData = [
            ['name' => 'Maya Anggraini', 'username' => 'maya_ang', 'email' => 'maya@example.com', 'tier' => 'VIP', 'gender' => 'female'],
            ['name' => 'Rizki Pratama', 'username' => 'rizki_prat', 'email' => 'rizki@example.com', 'tier' => 'VIP', 'gender' => 'male'],
            ['name' => 'Budi Santoso', 'username' => 'budi_san', 'email' => 'budi_c@example.com', 'tier' => 'Premium', 'gender' => 'male'],
            ['name' => 'Dewi Lestari', 'username' => 'dewi_les', 'email' => 'dewi@example.com', 'tier' => 'Premium', 'gender' => 'female'],
            ['name' => 'Siti Rahayu', 'username' => 'siti_rah', 'email' => 'siti_c@example.com', 'tier' => 'Premium', 'gender' => 'female'],
            ['name' => 'Ahmad Fauzi', 'username' => 'ahmad_fau', 'email' => 'ahmad@example.com', 'tier' => 'Reguler', 'gender' => 'male'],
            ['name' => 'Hendra Wijaya', 'username' => 'hendra_wij', 'email' => 'hendra@example.com', 'tier' => 'Reguler', 'gender' => 'male'],
            ['name' => 'Nita Kusuma', 'username' => 'nita_kus', 'email' => 'nita@example.com', 'tier' => 'VIP', 'gender' => 'female'],
            ['name' => 'Fajar Nugroho', 'username' => 'fajar_nug', 'email' => 'fajar@example.com', 'tier' => 'Reguler', 'gender' => 'male'],
            ['name' => 'Rini Susanti', 'username' => 'rini_sus', 'email' => 'rini@example.com', 'tier' => 'Reguler', 'gender' => 'female'],
            ['name' => 'Andi Wijaya', 'username' => 'andi_wij', 'email' => 'andi@example.com', 'tier' => 'Baru', 'gender' => 'male'],
            ['name' => 'Diana Putri', 'username' => 'diana_put', 'email' => 'diana@example.com', 'tier' => 'Baru', 'gender' => 'female'],
        ];

        $customers = [];
        foreach ($customersData as $c) {
            $user = User::updateOrCreate(
                ['username' => $c['username']],
                [
                    'name' => $c['name'],
                    'email' => $c['email'],
                    'password' => Hash::make('password'),
                    'gender' => $c['gender'],
                    'phone' => '08' . mt_rand(111111111, 999999999),
                    'is_active' => '1',
                    'dob' => Carbon::now()->subYears(mt_rand(20, 45))->format('Y-m-d'),
                    'address' => 'Jl. Kemenangan No. ' . mt_rand(1, 150) . ', Kota Hijau',
                    'tier' => $c['tier'],
                    'notes' => 'Pelanggan loyal semenjak awal.',
                    'outlet_id' => $outlets->random()->id,
                ]
            );
            $user->assignRole('customer');
            $customers[] = $user;
        }

        // 2. Seed orders spanning the last 60 days
        $services = [
            ['type' => 'Cuci Setrika', 'price' => 10000, 'category' => 'Kiloan'],
            ['type' => 'Cuci Kering', 'price' => 8000, 'category' => 'Kiloan'],
            ['type' => 'Express', 'price' => 15000, 'category' => 'Kiloan'],
            ['type' => 'Satuan', 'price' => 25000, 'category' => 'Satuan'],
            ['type' => 'Setrika Saja', 'price' => 5000, 'category' => 'Kiloan'],
        ];

        $paymentMethods = ['Tunai', 'Transfer Bank', 'QRIS', 'OVO/GoPay', 'Lainnya'];
        $statuses = ['Baru', 'Proses', 'Selesai', 'Diambil'];

        // Seed 150 orders to give plenty of data for charts and tables
        for ($i = 0; $i < 150; $i++) {
            $customer = $customers[array_rand($customers)];
            $outlet = $outlets->random();
            $service = $services[array_rand($services)];
            
            // Distribute order dates across the last 60 days
            $createdAt = Carbon::now()->subDays(mt_rand(0, 60))->subHours(mt_rand(0, 23))->subMinutes(mt_rand(0, 59));
            
            $weight = mt_rand(1, 10) + (mt_rand(0, 9) / 10); // 1.0 to 10.9
            $pricePerUnit = $service['price'];
            $totalPrice = intval($weight * $pricePerUnit);

            $status = $statuses[array_rand($statuses)];
            $paymentStatus = 'Belum';
            $paymentMethod = null;
            $finishedAt = null;

            if ($status === 'Selesai' || $status === 'Diambil') {
                $paymentStatus = 'Lunas';
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $finishedAt = (clone $createdAt)->addDays(mt_rand(1, 3))->addHours(mt_rand(1, 12));
            } else {
                // Proses / Baru could be paid sometimes
                if (mt_rand(1, 10) > 4) {
                    $paymentStatus = 'Lunas';
                    $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                }
            }

            Order::create([
                'order_code' => 'ORD-' . $createdAt->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'outlet_id' => $outlet->id,
                'service_type' => $service['type'],
                'weight' => $weight,
                'price_per_unit' => $pricePerUnit,
                'total_price' => $totalPrice,
                'order_status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'finished_at' => $finishedAt,
                'notes' => mt_rand(1, 10) > 7 ? 'Harap dicuci terpisah.' : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
