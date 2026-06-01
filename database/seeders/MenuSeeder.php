<?php

namespace Database\Seeders;

use App\Models\Konfigurasi\Menu;
use App\Traits\HasMenuPermission;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    use HasMenuPermission;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'MASTER' => [
                [
                    'name' => 'Semua Order',
                    'url' => 'orders',
                    'icon' => 'shopping-cart',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Pelanggan',
                    'url' => 'customers',
                    'icon' => 'users',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Karyawan',
                    'url' => 'employees',
                    'icon' => 'user-check',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Bisnis',
                    'url' => 'businesses',
                    'icon' => 'building',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Outlet',
                    'url' => 'outlets',
                    'icon' => 'home',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
            ],
            'OPERASIONAL' => [
                [
                    'name' => 'Layanan dan Harga',
                    'url' => 'services',
                    'icon' => 'tag',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Antar Jemput',
                    'url' => 'shuttles',
                    'icon' => 'truck',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Inventaris',
                    'url' => 'inventories',
                    'icon' => 'archive',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
            ],
            'KEUANGAN' => [
                [
                    'name' => 'Laporan',
                    'url' => 'reports',
                    'icon' => 'file-text',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Pembayaran',
                    'url' => 'payments',
                    'icon' => 'credit-card',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Promo & Diskon',
                    'url' => 'promos',
                    'icon' => 'gift',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
            ],
            'ROLE PERMISSION' => [
                [
                    'name' => 'Role',
                    'url' => 'roles',
                    'icon' => 'shield',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Permission',
                    'url' => 'permissions',
                    'icon' => 'key',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Menu',
                    'url' => 'menus',
                    'icon' => 'menu',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
            ],
            'SISTEM' => [
                [
                    'name' => 'Profil',
                    'url' => 'profile',
                    'icon' => 'user',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
                [
                    'name' => 'Pengaturan',
                    'url' => 'settings',
                    'icon' => 'settings',
                    'permissions' => ['menu', 'create', 'read', 'show', 'update', 'delete'],
                ],
            ],
        ];

        $order = 1;
        foreach ($categories as $categoryName => $menuList) {
            foreach ($menuList as $menuItem) {
                $menu = Menu::updateOrCreate(
                    [
                        'url' => $menuItem['url'],
                    ],
                    [
                        'name' => $menuItem['name'],
                        'category' => $categoryName,
                        'icon' => $menuItem['icon'],
                        'is_active' => '1',
                        'orders' => $order++,
                    ]
                );

                // Assign permissions to dev and admin
                $this->attachMenupermission($menu, $menuItem['permissions'], ['dev', 'admin']);
            }
        }
    }
}
