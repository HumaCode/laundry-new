<?php

namespace Database\Seeders;

use App\Models\Operasional\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // KILOAN
            [
                'service_code' => 'SVC-001',
                'name' => 'Cuci Kering',
                'emoji' => '🧺',
                'category' => 'kiloan',
                'description' => 'Proses cuci dan pengeringan tanpa setrika. Cocok untuk pakaian sehari-hari.',
                'price' => 8000,
                'unit' => '/kg',
                'eta' => '1-2 hari',
                'color' => 'sc-purple',
                'status' => true,
                'express' => true,
                'pickup' => true,
                'orders' => 412,
                'revenue' => 3296000,
                'target' => 400,
                'min_qty' => '1 kg',
                'features' => ['Dicuci dengan deterjen premium', 'Dikeringkan hingga sempurna', 'Dilipat rapi', 'Dikemas plastik bersih'],
                'tiers' => [
                    ['label' => '1-5 kg', 'price' => 8000],
                    ['label' => '6-10 kg', 'price' => 7500],
                    ['label' => '>10 kg', 'price' => 7000]
                ]
            ],
            [
                'service_code' => 'SVC-002',
                'name' => 'Cuci Setrika',
                'emoji' => '👕',
                'category' => 'kiloan',
                'description' => 'Layanan lengkap cuci, keringkan, dan setrika. Pakaian siap pakai.',
                'price' => 10000,
                'unit' => '/kg',
                'eta' => '1-2 hari',
                'color' => 'sc-green',
                'status' => true,
                'express' => true,
                'pickup' => true,
                'orders' => 387,
                'revenue' => 3870000,
                'target' => 350,
                'min_qty' => '1 kg',
                'features' => ['Dicuci bersih menyeluruh', 'Dikeringkan sempurna', 'Disetrika rapi & wangi', 'Dikemas plastik premium'],
                'tiers' => [
                    ['label' => '1-5 kg', 'price' => 10000],
                    ['label' => '6-10 kg', 'price' => 9500],
                    ['label' => '>10 kg', 'price' => 9000]
                ]
            ],
            [
                'service_code' => 'SVC-003',
                'name' => 'Setrika Saja',
                'emoji' => '🔥',
                'category' => 'kiloan',
                'description' => 'Layanan setrika pakaian bersih yang sudah dicuci sendiri.',
                'price' => 5000,
                'unit' => '/kg',
                'eta' => '6-8 jam',
                'color' => 'sc-orange',
                'status' => true,
                'express' => false,
                'pickup' => false,
                'orders' => 156,
                'revenue' => 780000,
                'target' => 150,
                'min_qty' => '1 kg',
                'features' => ['Setrika rapi & presisi', 'Bebas lipatan', 'Wangi segar', 'Pengerjaan cepat'],
                'tiers' => []
            ],
            [
                'service_code' => 'SVC-004',
                'name' => 'Express',
                'emoji' => '⚡',
                'category' => 'kiloan',
                'description' => 'Layanan cuci setrika kilat, selesai dalam 6 jam untuk kebutuhan mendesak.',
                'price' => 15000,
                'unit' => '/kg',
                'eta' => '4-6 jam',
                'color' => 'sc-pink',
                'status' => true,
                'express' => false,
                'pickup' => true,
                'orders' => 98,
                'revenue' => 1470000,
                'target' => 100,
                'min_qty' => '1 kg',
                'features' => ['Prioritas antrian tertinggi', 'Selesai 4-6 jam', 'Cuci + setrika lengkap', 'Notifikasi real-time'],
                'tiers' => []
            ],
            // SATUAN
            [
                'service_code' => 'SVC-005',
                'name' => 'Jas & Blazer',
                'emoji' => '🤵',
                'category' => 'satuan',
                'description' => 'Perawatan khusus jas dan blazer dengan teknik dry cleaning profesional.',
                'price' => 35000,
                'unit' => '/pcs',
                'eta' => '2-3 hari',
                'color' => 'sc-indigo',
                'status' => true,
                'express' => true,
                'pickup' => true,
                'orders' => 89,
                'revenue' => 3115000,
                'target' => 80,
                'min_qty' => '1 pcs',
                'features' => ['Dry cleaning profesional', 'Anti kusut & kilap', 'Penanganan bahan mewah', 'Dikemas gantung'],
                'tiers' => []
            ],
            [
                'service_code' => 'SVC-006',
                'name' => 'Bed Cover',
                'emoji' => '🛏️',
                'category' => 'satuan',
                'description' => 'Pencucian bed cover dan sprei ukuran besar dengan mesin kapasitas khusus.',
                'price' => 25000,
                'unit' => '/pcs',
                'eta' => '1-2 hari',
                'color' => 'sc-blue',
                'status' => true,
                'express' => false,
                'pickup' => true,
                'orders' => 134,
                'revenue' => 3350000,
                'target' => 120,
                'min_qty' => '1 pcs',
                'features' => ['Mesin kapasitas besar', 'Deterjen anti bakteri', 'Keringkan sempurna', 'Dilipat rapi'],
                'tiers' => [
                    ['label' => 'Single', 'price' => 20000],
                    ['label' => 'Double/Queen', 'price' => 25000],
                    ['label' => 'King', 'price' => 30000]
                ]
            ],
            [
                'service_code' => 'SVC-007',
                'name' => 'Gorden',
                'emoji' => '🪟',
                'category' => 'satuan',
                'description' => 'Pencucian gorden berbagai ukuran dengan teknik khusus menjaga warna dan bentuk.',
                'price' => 15000,
                'unit' => '/m²',
                'eta' => '2-3 hari',
                'color' => 'sc-teal',
                'status' => true,
                'express' => false,
                'pickup' => true,
                'orders' => 67,
                'revenue' => 1005000,
                'target' => 70,
                'min_qty' => '1 m²',
                'features' => ['Teknik cuci lembut', 'Menjaga warna & bentuk', 'Anti kusut', 'Bisa antar pasang'],
                'tiers' => []
            ],
            [
                'service_code' => 'SVC-008',
                'name' => 'Sepatu & Tas',
                'emoji' => '👟',
                'category' => 'satuan',
                'description' => 'Perawatan sepatu dan tas dengan bahan khusus sesuai material.',
                'price' => 40000,
                'unit' => '/pair',
                'eta' => '2-3 hari',
                'color' => 'sc-orange',
                'status' => true,
                'express' => true,
                'pickup' => true,
                'orders' => 103,
                'revenue' => 4120000,
                'target' => 90,
                'min_qty' => '1 pair',
                'features' => ['Dibersihkan per material', 'Obat khusus kulit/kanvas', 'Dikeringkan sempurna', 'Dikemas dus'],
                'tiers' => [
                    ['label' => 'Sneakers', 'price' => 35000],
                    ['label' => 'Boots/Heels', 'price' => 45000],
                    ['label' => 'Tas kulit', 'price' => 55000]
                ]
            ],
            [
                'service_code' => 'SVC-009',
                'name' => 'Boneka',
                'emoji' => '🧸',
                'category' => 'satuan',
                'description' => 'Pencucian boneka lembut berbagai ukuran dengan deterjen ramah anak.',
                'price' => 20000,
                'unit' => '/pcs',
                'eta' => '2-3 hari',
                'color' => 'sc-pink',
                'status' => false,
                'express' => false,
                'pickup' => false,
                'orders' => 31,
                'revenue' => 620000,
                'target' => 50,
                'min_qty' => '1 pcs',
                'features' => ['Deterjen ramah anak', 'Bahan lembut terjaga', 'Anti jamur & bakteri', 'Dikemas plastik'],
                'tiers' => [
                    ['label' => 'Kecil (<30cm)', 'price' => 15000],
                    ['label' => 'Sedang (30-60cm)', 'price' => 20000],
                    ['label' => 'Besar (>60cm)', 'price' => 35000]
                ]
            ],
            // PAKET
            [
                'service_code' => 'SVC-010',
                'name' => 'Paket Bulanan Basic',
                'emoji' => '📦',
                'category' => 'paket',
                'description' => 'Paket langganan bulanan untuk 30 kg cucian. Hemat hingga 20% dari harga normal.',
                'price' => 240000,
                'unit' => '/bulan',
                'eta' => '1-2 hari',
                'color' => 'sc-blue',
                'status' => true,
                'express' => false,
                'pickup' => true,
                'orders' => 48,
                'revenue' => 11520000,
                'target' => 50,
                'min_qty' => '1 paket',
                'features' => ['30 kg cuci setrika/bulan', 'Prioritas antrian', 'Antar jemput gratis', 'Laporan bulanan'],
                'tiers' => []
            ],
            [
                'service_code' => 'SVC-011',
                'name' => 'Paket Bulanan Premium',
                'emoji' => '💎',
                'category' => 'paket',
                'description' => 'Paket VIP bulanan dengan 60 kg cucian, express tersedia, dan konsultasi perawatan baju.',
                'price' => 420000,
                'unit' => '/bulan',
                'eta' => 'Same day',
                'color' => 'sc-purple',
                'status' => true,
                'express' => true,
                'pickup' => true,
                'orders' => 23,
                'revenue' => 9660000,
                'target' => 30,
                'min_qty' => '1 paket',
                'features' => ['60 kg cuci setrika/bulan', 'Layanan express included', 'Antar jemput 2x/minggu', 'Konsultasi perawatan', 'Laporan detail'],
                'tiers' => []
            ],
            // ANTAR JEMPUT
            [
                'service_code' => 'SVC-012',
                'name' => 'Antar Jemput Standar',
                'emoji' => '🛵',
                'category' => 'antar',
                'description' => 'Layanan ambil cucian di rumah pelanggan dan antar kembali setelah selesai.',
                'price' => 10000,
                'unit' => '/trip',
                'eta' => 'Sesuai jadwal',
                'color' => 'sc-teal',
                'status' => true,
                'express' => false,
                'pickup' => false,
                'orders' => 215,
                'revenue' => 2150000,
                'target' => 200,
                'min_qty' => 'Min. 2 kg',
                'features' => ['Radius 5 km dari outlet', 'Jadwal fleksibel', 'Tracking real-time', 'Dikemas aman'],
                'tiers' => [
                    ['label' => 'Radius 0-3 km', 'price' => 8000],
                    ['label' => 'Radius 3-5 km', 'price' => 12000],
                    ['label' => 'Radius 5-10 km', 'price' => 18000]
                ]
            ],
            [
                'service_code' => 'SVC-013',
                'name' => 'Antar Jemput Express',
                'emoji' => '🏍️',
                'category' => 'antar',
                'description' => 'Layanan antar jemput kilat dengan prioritas dan jaminan sampai 2 jam.',
                'price' => 20000,
                'unit' => '/trip',
                'eta' => '< 2 jam',
                'color' => 'sc-red',
                'status' => true,
                'express' => false,
                'pickup' => false,
                'orders' => 87,
                'revenue' => 1740000,
                'target' => 80,
                'min_qty' => 'Min. 1 kg',
                'features' => ['Jaminan datang < 2 jam', 'Prioritas penjemputan', 'Tracking live', 'Dikemas express'],
                'tiers' => []
            ],
        ];

        foreach ($services as $srv) {
            Service::create($srv);
        }
    }
}
