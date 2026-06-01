<?php

namespace Database\Seeders;

use App\Models\Master\Business;
use App\Models\Master\Outlet;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businesses = [
            [
                'name'        => 'LaundryPro Pusat',
                'code'        => 'BIS-0001',
                'owner'       => 'Budi Hartono',
                'phone'       => '021-55501234',
                'email'       => 'pusat@laundrypro.com',
                'city'        => 'Jakarta',
                'address'     => 'Jl. Sudirman No. 1, Jakarta Pusat 10220',
                'description' => 'Bisnis laundry utama dengan jaringan outlet terbesar.',
                'is_active'   => true,
            ],
            [
                'name'        => 'LaundryPro Timur',
                'code'        => 'BIS-0002',
                'owner'       => 'Siti Rahayu',
                'phone'       => '031-77802345',
                'email'       => 'timur@laundrypro.com',
                'city'        => 'Surabaya',
                'address'     => 'Jl. Raya Darmo No. 45, Surabaya 60264',
                'description' => 'Ekspansi bisnis laundry di kawasan Jawa Timur.',
                'is_active'   => true,
            ],
            [
                'name'        => 'LaundryPro Selatan',
                'code'        => 'BIS-0003',
                'owner'       => 'Ahmad Fauzi',
                'phone'       => '0274-884567',
                'email'       => 'selatan@laundrypro.com',
                'city'        => 'Yogyakarta',
                'address'     => 'Jl. Malioboro No. 12, Yogyakarta 55213',
                'description' => 'Bisnis laundry di wilayah DIY dan sekitarnya.',
                'is_active'   => true,
            ],
        ];

        foreach ($businesses as $data) {
            Business::updateOrCreate(['code' => $data['code']], $data);
        }

        // Opsional: hubungkan outlet yang sudah ada ke bisnis pertama
        $firstBusiness = Business::where('code', 'BIS-0001')->first();
        if ($firstBusiness) {
            Outlet::whereNull('business_id')->take(3)->get()->each(function ($outlet) use ($firstBusiness) {
                $outlet->update(['business_id' => $firstBusiness->id]);
            });
        }
    }
}
