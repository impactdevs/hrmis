import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/custom-css.css',
                'resources/js/app.js',
                'resources/js/apexcharts/apexcharts.min.js',
                'resources/js/chart.js/chart.umd.js',
                'resources/js/custom-js.js',
                'resources/js/echarts/echarts.min.js',
                'resources/js/simple-datatables/simple-datatables.js',
                'resources/js/simple-datatables/style.css',
                'resources/js/custom-dashboard'
            ],
            refresh: true,
        }),
    ],
});
