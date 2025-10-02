import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/fullcalendarcss.css',
                'resources/js/app.js',
                'resources/js/custom-dashboard.js',
                'resources/js/custom-js.js',
                'resources/js/dashboard-logic.js',
            ],
            refresh: true,
        }),
    ],
});
