import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/auth/login.css',
                'resources/js/auth/login.js',
                'resources/js/auth/register.js',
                'resources/js/auth/forgot-password.js',
                'resources/js/auth/reset-password.js',
                'resources/css/admin/dashboard.css',
                'resources/js/admin/dashboard.js',
                'resources/css/admin/global.css',
                'resources/js/admin/global.js',
                'resources/css/admin/pelanggan.css',
                'resources/js/admin/pelanggan.js',
                'resources/css/admin/outlet.css',
                'resources/js/admin/outlet.js',
                'resources/css/admin/karyawan.css',
                'resources/js/admin/karyawan.js',
                'resources/css/admin/bisnis.css',
                'resources/js/admin/bisnis.js',
                'resources/css/admin/order.css',
                'resources/js/admin/order.js',
                'resources/css/admin/services.css',
                'resources/js/admin/services.js'
            ],
            refresh: true,
        }),
    ],
});
