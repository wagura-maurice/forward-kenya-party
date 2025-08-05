import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
                compilerOptions: {
                    whitespace: 'condense',
                },
            },
        }),
    ],
    build: {
        minify: 'esbuild',
        sourcemap: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': [
                        'vue',
                        '@inertiajs/vue3',
                        'sweetalert2',
                        'axios'
                    ]
                }
            }
        },
        commonjsOptions: {
            transformMixedEsModules: true,
        },
    },
    optimizeDeps: {
        include: ['vue', '@inertiajs/vue3', 'sweetalert2', 'axios'],
        esbuildOptions: {
            define: {
                global: 'globalThis',
            },
        },
    },
});
