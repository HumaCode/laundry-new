<?php

namespace Database\Seeders;

use App\Models\Master\Employee;
use App\Models\Master\Outlet;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = Outlet::all();
        if ($outlets->isEmpty()) {
            return;
        }

        $employees = [
            [
                'name' => 'Budi Santoso',
                'code' => 'EMP-0001',
                'phone' => '081234567890',
                'email' => 'budi@laundrypro.com',
                'role' => 'Kepala Outlet',
                'is_active' => true,
                'address' => 'Jl. Cempaka No. 5, Jakarta',
                'joined_at' => '2025-01-10',
                'outlet_id' => $outlets->where('code', 'OUT-0001')->first()?->id ?? $outlets->first()->id,
            ],
            [
                'name' => 'Siti Aminah',
                'code' => 'EMP-0002',
                'phone' => '082345678901',
                'email' => 'siti@laundrypro.com',
                'role' => 'Kasir',
                'is_active' => true,
                'address' => 'Jl. Mawar No. 12, Bandung',
                'joined_at' => '2025-02-15',
                'outlet_id' => $outlets->where('code', 'OUT-0002')->first()?->id ?? $outlets->first()->id,
            ],
            [
                'name' => 'Agus Wijaya',
                'code' => 'EMP-0003',
                'phone' => '083456789012',
                'email' => 'agus@laundrypro.com',
                'role' => 'Kurir',
                'is_active' => true,
                'address' => 'Jl. Melati No. 8, Surabaya',
                'joined_at' => '2025-03-20',
                'outlet_id' => $outlets->where('code', 'OUT-0003')->first()?->id ?? $outlets->first()->id,
            ],
            [
                'name' => 'Lani Suryani',
                'code' => 'EMP-0004',
                'phone' => '084567890123',
                'email' => 'lani@laundrypro.com',
                'role' => 'Pencuci',
                'is_active' => true,
                'address' => 'Jl. Anggrek No. 3, Yogyakarta',
                'joined_at' => '2025-04-01',
                'outlet_id' => $outlets->where('code', 'OUT-0004')->first()?->id ?? $outlets->first()->id,
            ],
            [
                'name' => 'Joko Widodo',
                'code' => 'EMP-0005',
                'phone' => '085678901234',
                'email' => 'joko@laundrypro.com',
                'role' => 'Penyetrika',
                'is_active' => false,
                'address' => 'Jl. Kamboja No. 17, Semarang',
                'joined_at' => '2025-04-10',
                'outlet_id' => $outlets->where('code', 'OUT-0005')->first()?->id ?? $outlets->first()->id,
            ],
        ];

        foreach ($employees as $emp) {
            Employee::updateOrCreate(
                ['code' => $emp['code']],
                $emp
            );
        }
    }
}
