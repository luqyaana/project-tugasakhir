import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/admin.js',
                'resources/js/public.js',
                'resources/js/pages/keloladata.js',
                'resources/js/pages/kelolasaran.js',
                'resources/js/pages/model.js',
                'resources/js/pages/monitoring.js',
            ],
            refresh: true,
        }),
    ],
});