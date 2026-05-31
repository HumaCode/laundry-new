<?php

namespace Database\Seeders;

use App\Models\Master\Outlet;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = [
            [
                "name" => "Outlet Pusat",
                "code" => "OUT-0001",
                "phone" => "021-5551234",
                "email" => "pusat@laundrypro.com",
                "city" => "Jakarta Pusat",
                "address" => "Jl. Sudirman No. 12, Jakarta Pusat",
                "manager" => "Hendra Wijaya",
                "is_active" => true,
                "payment_type" => "pay_later",
                "dp_percentage" => 50,
            ],
            [
                "name" => "Outlet Bandung",
                "code" => "OUT-0002",
                "phone" => "022-4445678",
                "email" => "bandung@laundrypro.com",
                "city" => "Bandung Kota",
                "address" => "Jl. Merdeka No. 45, Bandung",
                "manager" => "Dewi Lestari",
                "is_active" => true,
                "payment_type" => "pay_first",
                "dp_percentage" => 50,
            ],
            [
                "name" => "Outlet Surabaya",
                "code" => "OUT-0003",
                "phone" => "031-7778899",
                "email" => "surabaya@laundrypro.com",
                "city" => "Surabaya Barat",
                "address" => "Jl. Diponegoro No. 8, Surabaya",
                "manager" => "Nita Kusuma",
                "is_active" => true,
                "payment_type" => "dp_first",
                "dp_percentage" => 30,
            ],
            [
                "name" => "Outlet Yogyakarta",
                "code" => "OUT-0004",
                "phone" => "0274-123456",
                "email" => "jogja@laundrypro.com",
                "city" => "Yogyakarta",
                "address" => "Jl. Malioboro No. 100, Yogyakarta",
                "manager" => "Fajar Nugroho",
                "is_active" => true,
                "payment_type" => "pay_later",
                "dp_percentage" => 50,
            ],
            [
                "name" => "Outlet Semarang",
                "code" => "OUT-0005",
                "phone" => "024-987654",
                "email" => "semarang@laundrypro.com",
                "city" => "Semarang Tengah",
                "address" => "Jl. Pemuda No. 33, Semarang",
                "manager" => "Rini Susanti",
                "is_active" => false,
                "payment_type" => "pay_later",
                "dp_percentage" => 50,
            ],
        ];

        foreach ($outlets as $outlet) {
            Outlet::updateOrCreate(
                ["code" => $outlet["code"]],
                $outlet
            );
        }
    }
}
