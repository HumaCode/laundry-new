<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Operasional\Inventory;
use App\Models\Master\Outlet;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Deterjen & Kimia',
            'Pewangi & Softener',
            'Plastik & Kemasan',
            'Peralatan Cuci',
            'Peralatan Setrika',
            'Kebersihan Outlet',
            'ATK & Administrasi'
        ];

        $catIcons = [
            'Deterjen & Kimia' => '🧴',
            'Pewangi & Softener' => '🌸',
            'Plastik & Kemasan' => '📦',
            'Peralatan Cuci' => '🪣',
            'Peralatan Setrika' => '🔌',
            'Kebersihan Outlet' => '🧹',
            'ATK & Administrasi' => '📋'
        ];

        $catColors = [
            'Deterjen & Kimia' => '#6366F1',
            'Pewangi & Softener' => '#EC4899',
            'Plastik & Kemasan' => '#3B82F6',
            'Peralatan Cuci' => '#10B981',
            'Peralatan Setrika' => '#F59E0B',
            'Kebersihan Outlet' => '#14B8A6',
            'ATK & Administrasi' => '#F97316'
        ];

        $rawItems = [
            /* Deterjen */
            ['name' => 'Deterjen Attack Cair', 'brand' => 'PT Kao Indonesia', 'price' => 45000, 'minS' => 20, 'maxS' => 100, 'unit' => 'liter', 'category' => 'Deterjen & Kimia'],
            ['name' => 'Deterjen Rinso Ultra', 'brand' => 'PT Unilever', 'price' => 38000, 'minS' => 15, 'maxS' => 80, 'unit' => 'kg', 'category' => 'Deterjen & Kimia'],
            ['name' => 'Deterjen Bubuk Wings', 'brand' => 'PT Wings Surya', 'price' => 28000, 'minS' => 10, 'maxS' => 60, 'unit' => 'kg', 'category' => 'Deterjen & Kimia'],
            ['name' => 'Pemutih Bayclin', 'brand' => 'PT Clorox', 'price' => 15000, 'minS' => 12, 'maxS' => 50, 'unit' => 'botol', 'category' => 'Deterjen & Kimia'],
            ['name' => 'Cairan Antinoda Pro', 'brand' => 'CV Kimia Prima', 'price' => 55000, 'minS' => 8, 'maxS' => 40, 'unit' => 'liter', 'category' => 'Deterjen & Kimia'],
            /* Pewangi */
            ['name' => 'Pewangi Molto Ultra', 'brand' => 'PT Unilever', 'price' => 32000, 'minS' => 15, 'maxS' => 60, 'unit' => 'liter', 'category' => 'Pewangi & Softener'],
            ['name' => 'Softener Downy', 'brand' => 'P&G Indonesia', 'price' => 42000, 'minS' => 10, 'maxS' => 50, 'unit' => 'liter', 'category' => 'Pewangi & Softener'],
            ['name' => 'Pengharum Baju Fresh', 'brand' => 'CV Aroma Nusantara', 'price' => 18000, 'minS' => 20, 'maxS' => 80, 'unit' => 'botol', 'category' => 'Pewangi & Softener'],
            /* Kemasan */
            ['name' => 'Plastik PP 40x60 cm', 'brand' => 'CV Plastik Jaya', 'price' => 85000, 'minS' => 10, 'maxS' => 50, 'unit' => 'pack', 'category' => 'Plastik & Kemasan'],
            ['name' => 'Plastik Gantung Jas', 'brand' => 'CV Plastik Jaya', 'price' => 65000, 'minS' => 5, 'maxS' => 30, 'unit' => 'pack', 'category' => 'Plastik & Kemasan'],
            ['name' => 'Kantong Laundry Medium', 'brand' => 'UD Kemasan Prima', 'price' => 95000, 'minS' => 8, 'maxS' => 40, 'unit' => 'pack', 'category' => 'Plastik & Kemasan'],
            ['name' => 'Label Stiker Pelanggan', 'brand' => 'CV Print Express', 'price' => 25000, 'minS' => 20, 'maxS' => 100, 'unit' => 'roll', 'category' => 'Plastik & Kemasan'],
            /* Peralatan Cuci */
            ['name' => 'Sikat Noda Pakaian', 'brand' => 'Merk Lokal', 'price' => 12000, 'minS' => 10, 'maxS' => 40, 'unit' => 'pcs', 'category' => 'Peralatan Cuci'],
            ['name' => 'Ember Cuci 25L', 'brand' => 'Shinpo', 'price' => 35000, 'minS' => 5, 'maxS' => 20, 'unit' => 'pcs', 'category' => 'Peralatan Cuci'],
            ['name' => 'Keranjang Cucian Besar', 'brand' => 'Olax', 'price' => 55000, 'minS' => 5, 'maxS' => 20, 'unit' => 'pcs', 'category' => 'Peralatan Cuci'],
            ['name' => 'Selang Air 10m', 'brand' => 'Wavin', 'price' => 75000, 'minS' => 3, 'maxS' => 15, 'unit' => 'pcs', 'category' => 'Peralatan Cuci'],
            /* Peralalan Setrika */
            ['name' => 'Setrika Uap Philips GC', 'brand' => 'Philips', 'price' => 450000, 'minS' => 2, 'maxS' => 10, 'unit' => 'pcs', 'category' => 'Peralatan Setrika'],
            ['name' => 'Papan Setrika Besar', 'brand' => 'Toyama', 'price' => 180000, 'minS' => 3, 'maxS' => 12, 'unit' => 'pcs', 'category' => 'Peralatan Setrika'],
            ['name' => 'Spray Pelicin Pakaian', 'brand' => 'Robin', 'price' => 22000, 'minS' => 10, 'maxS' => 40, 'unit' => 'botol', 'category' => 'Peralatan Setrika'],
            ['name' => 'Kain Lap Setrika', 'brand' => 'Merk Lokal', 'price' => 8000, 'minS' => 20, 'maxS' => 80, 'unit' => 'lembar', 'category' => 'Peralatan Setrika'],
            /* Kebersihan */
            ['name' => 'Sabun Cuci Tangan Dettol', 'brand' => 'RB Health', 'price' => 28000, 'minS' => 10, 'maxS' => 50, 'unit' => 'botol', 'category' => 'Kebersihan Outlet'],
            ['name' => 'Wipol Pembersih Lantai', 'brand' => 'Unilever', 'price' => 18000, 'minS' => 8, 'maxS' => 40, 'unit' => 'botol', 'category' => 'Kebersihan Outlet'],
            ['name' => 'Tisu Lap Serbaguna', 'brand' => 'Paseo', 'price' => 35000, 'minS' => 10, 'maxS' => 50, 'unit' => 'pack', 'category' => 'Kebersihan Outlet'],
            ['name' => 'Masker Kain Kasir', 'brand' => 'CV Sehat Jaya', 'price' => 5000, 'minS' => 30, 'maxS' => 100, 'unit' => 'pcs', 'category' => 'Kebersihan Outlet'],
            /* ATK */
            ['name' => 'Nota Bon Rangkap', 'brand' => 'Sinar Dunia', 'price' => 8500, 'minS' => 20, 'maxS' => 80, 'unit' => 'dus', 'category' => 'ATK & Administrasi'],
            ['name' => 'Bolpoin Pilot Hitam', 'brand' => 'Pilot', 'price' => 4500, 'minS' => 30, 'maxS' => 100, 'unit' => 'pcs', 'category' => 'ATK & Administrasi'],
            ['name' => 'Buku Kas Laundry A4', 'brand' => 'Kiky', 'price' => 12000, 'minS' => 10, 'maxS' => 40, 'unit' => 'pcs', 'category' => 'ATK & Administrasi'],
            ['name' => 'Stapler + Isi Staples', 'brand' => 'MAX', 'price' => 35000, 'minS' => 5, 'maxS' => 20, 'unit' => 'pcs', 'category' => 'ATK & Administrasi'],
            ['name' => 'Selotip Besar', 'brand' => 'Nachi', 'price' => 9000, 'minS' => 10, 'maxS' => 40, 'unit' => 'pcs', 'category' => 'ATK & Administrasi'],
            ['name' => 'Spidol Permanen', 'brand' => 'Snowman', 'price' => 6500, 'minS' => 15, 'maxS' => 50, 'unit' => 'pcs', 'category' => 'ATK & Administrasi'],
            ['name' => 'Map Plastik Pelanggan', 'brand' => 'Bantex', 'price' => 3500, 'minS' => 50, 'maxS' => 200, 'unit' => 'pcs', 'category' => 'ATK & Administrasi'],
            ['name' => 'Tinta Printer Epson', 'brand' => 'Epson', 'price' => 95000, 'minS' => 3, 'maxS' => 15, 'unit' => 'botol', 'category' => 'ATK & Administrasi'],
        ];

        $outlets = Outlet::all();
        if ($outlets->isEmpty()) {
            return;
        }

        $historyDates = ['2024-12-01', '2024-11-15', '2024-11-01', '2024-10-15', '2024-10-01'];

        foreach ($rawItems as $i => $item) {
            $cat = $item['category'];
            $outlet = $outlets[$i % $outlets->count()];

            // vary stock levels
            $stockLevels = [0, 3, 7, 14, 25, 40, 65, 90];
            $rawStock = $stockLevels[$i % count($stockLevels)];
            $stock = min($rawStock, $item['maxS']);

            $history = [];
            for ($j = 0; $j < 3; $j++) {
                $history[] = [
                    'date' => $historyDates[$j],
                    'qty' => $item['minS'] + $j * 5,
                    'supplier' => $item['brand'],
                    'invoice' => 'INV-2024-' . str_pad((100 + $i * 3 + $j), 4, '0', STR_PAD_LEFT),
                ];
            }

            Inventory::create([
                'name' => $item['name'],
                'code' => strtoupper(substr($cat, 0, 3)) . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'brand' => $item['brand'],
                'category' => $cat,
                'emoji' => $catIcons[$cat] ?? '📦',
                'color' => $catColors[$cat] ?? '#6366F1',
                'stock' => $stock,
                'min_stock' => $item['minS'],
                'max_stock' => $item['maxS'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'outlet_id' => $outlet->id,
                'desc' => 'Stok perlengkapan untuk operasional harian.',
                'last_restock' => $historyDates[$i % count($historyDates)],
                'last_restock_qty' => $item['minS'] + 10,
                'history' => $history,
            ]);
        }
    }
}
