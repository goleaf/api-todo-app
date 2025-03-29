import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    base: '/',
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        chunkSizeWarningLimit: 1024, // 1MB
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'vue-router', 'pinia', 'axios'],
                },
                assetFileNames: (assetInfo) => {
                    // Put font files in a dedicated directory
                    if (assetInfo.name.endsWith('.woff2') || 
                        assetInfo.name.endsWith('.woff') || 
                        assetInfo.name.endsWith('.ttf') || 
                        assetInfo.name.endsWith('.eot')) {
                        return 'fonts/[name][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/main.js',
            ],
            refresh: true,
            publicDirectory: 'public',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '~': path.resolve(__dirname, 'resources'),
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    server: {
        hmr: {
            host: 'localhost',
        },
        host: '0.0.0.0',
    },
});
