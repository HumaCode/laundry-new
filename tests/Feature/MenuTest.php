<?php

use App\Models\Konfigurasi\Menu;
use Illuminate\Support\Facades\Cache;

test('menus can be created with ulids', function () {
    $menu = Menu::create([
        'name' => 'Dashboard',
        'url' => 'dashboard',
        'category' => 'Main',
        'icon' => 'home',
        'is_active' => '1',
        'orders' => 1,
    ]);

    expect($menu->id)->toBeString()->toHaveLength(26);
});

test('menus helper retrieves and groups active menus', function () {
    // Clear cache first
    Cache::forget('menus_data');
    Cache::forget('menus_url_list');

    $menu1 = Menu::create([
        'name' => 'Dashboard',
        'url' => 'dashboard',
        'category' => 'Main',
        'icon' => 'home',
        'is_active' => '1',
        'orders' => 1,
    ]);

    $menu2 = Menu::create([
        'name' => 'Profile',
        'url' => 'profile',
        'category' => 'Settings',
        'icon' => 'user',
        'is_active' => '1',
        'orders' => 2,
    ]);

    $menu3 = Menu::create([
        'name' => 'Hidden Menu',
        'url' => 'hidden',
        'category' => 'Main',
        'icon' => 'eye-off',
        'is_active' => '0',
        'orders' => 3,
    ]);

    $grouped = menus();

    expect($grouped)->toHaveKey('Main')
        ->and($grouped)->toHaveKey('Settings')
        ->and($grouped['Main'])->toHaveCount(1)
        ->and($grouped['Main']->first()->name)->toBe('Dashboard')
        ->and($grouped['Settings']->first()->name)->toBe('Profile');

    $urls = urlMenu();
    expect($urls)->toContain('dashboard')
        ->toContain('profile')
        ->not->toContain('hidden');
});
