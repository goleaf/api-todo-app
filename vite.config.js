import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
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
                    vendor: ['axios'],
                    fonts: ['@fontsource/roboto'],
                },
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    
                    // Handle font files
                    if (/ttf|otf|eot|woff|woff2/i.test(ext)) {
                        return `assets/fonts/[name]-[hash][extname]`;
                    }
                    
                    // Default for other assets
                    return `assets/[name]-[hash][extname]`;
                },
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
            publicDirectory: 'public',
        }),
    ],
    resolve: {
        alias: {
            '~': path.resolve(__dirname, 'resources'),
        },
    },
    server: {
        hmr: {
            host: 'localhost',
        },
        host: '0.0.0.0',
    },
});
