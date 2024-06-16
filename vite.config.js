import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite';
import path from 'path';
import dotenv from 'dotenv';

dotenv.config();

export default defineConfig({
    server: {
        host: 'localhost',
    },
    plugins: [
        laravel({
            input: 'resources/js/app.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        i18n(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    define: {
        'process.env': {
            VITE_PUSHER_APP_KEY: process.env.VITE_PUSHER_APP_KEY,
            VITE_PUSHER_APP_CLUSTER: process.env.VITE_PUSHER_APP_CLUSTER,
        },
        'process.env': process.env
    },
});
