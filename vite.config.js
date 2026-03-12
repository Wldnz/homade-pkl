import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'sass/metronic/style.scss', 
                'sass/app.scss', 
                'resources/js/app.js',
            ],
            refresh: [
                'sass/**',
                'resources/views/**',
                'resources/js/**',
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
