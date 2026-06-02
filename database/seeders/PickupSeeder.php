<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Master\Outlet;
use App\Models\Master\Employee;
use App\Models\Master\Order;
use App\Models\Operasional\Pickup;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PickupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = Outlet::all();
        $drivers = Employee::where('role', 'Kurir')->get();
        $orders = Order::all();
        $customers = User::role('customer')->get();

        if ($outlets->isEmpty() || $drivers->isEmpty()) {
            $this->command->warn('Outlets or drivers (Kurir) not found. Skip seeding pickups.');
            return;
        }

        $statuses = ['menunggu', 'jemput', 'proses', 'antar', 'selesai', 'batal'];
        $colors = ['#6366F1', '#10B981', '#F59E0B', '#EF4444', '#EC4899', '#3B82F6'];

        // Seed 40 pickups
        for ($i = 0; $i < 40; $i++) {
            $outlet = $outlets->random();
            $driver = $drivers->random();
            $customer = $customers->isNotEmpty() ? $customers->random() : null;
            $order = $orders->isNotEmpty() ? $orders->random() : null;

            $cName = $customer ? $customer->name : 'Pelanggan Walk-In ' . ($i + 1);
            $cPhone = ($customer && $customer->phone) ? $customer->phone : '08' . mt_rand(111111111, 999999999);
            $cId = $customer ? $customer->id : null;

            $status = $statuses[array_rand($statuses)];
            $scheduledAt = Carbon::now()->subDays(mt_rand(0, 30))->subHours(mt_rand(0, 12));
            $distance = mt_rand(1, 15) + (mt_rand(0, 9) / 10);
            $fee = intval(ceil($distance) * 2000 + 5000);

            Pickup::create([
                'trip_code' => 'TRP-' . $scheduledAt->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'customer_name' => $cName,
                'customer_phone' => $cPhone,
                'customer_id' => $cId,
                'outlet_id' => $outlet->id,
                'order_code' => $order ? $order->order_code : null,
                'address_from' => 'Jl. Asal No. ' . mt_rand(1, 100) . ', Kecamatan A',
                'address_to' => 'Jl. Tujuan No. ' . mt_rand(1, 100) . ', Kecamatan B',
                'service_type' => mt_rand(0, 1) ? 'Antar Jemput Standar' : 'Antar Jemput Express',
                'employee_id' => $driver->id,
                'distance' => $distance,
                'eta' => mt_rand(15, 60) . ' menit',
                'fee' => $fee,
                'scheduled_at' => $scheduledAt,
                'weight' => $order ? $order->weight . ' kg' : mt_rand(2, 8) . ' kg',
                'notes' => mt_rand(1, 10) > 8 ? 'Tolong jemput di lobi apartemen.' : null,
                'status' => $status,
                'avatar_color' => $colors[array_rand($colors)],
            ]);
        }
    }
}
