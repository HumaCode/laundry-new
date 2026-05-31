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
                'resources/js/auth/reset-password.js'
            ],
            refresh: true,
        }),
    ],
});
