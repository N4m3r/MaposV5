import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

// Vite config para MaposV5
// - Build: gera bundle minificado em assets/frontend/dist/
// - Dev server: porta 5173, com HMR
// - Code-splitting: separa React, CoreUI, Charts em chunks
export default defineConfig({
    plugins: [react()],

    // Servidor dev (rodar `npm run dev` no PC local)
    server: {
        port: 5173,
        host: 'localhost',
        proxy: {
            // Proxy /api e /index.php pra CodeIgniter local
            '/index.php': {
                target: 'http://localhost:8080',
                changeOrigin: true,
            },
            '/assets/img': {
                target: 'http://localhost:8080',
                changeOrigin: true,
            },
        },
    },

    // Build de produção
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        sourcemap: false,
        minify: 'esbuild',
        target: 'es2018',
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                // Hash no filename = cache imutável 1 ano
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
                manualChunks: {
                    'react-vendor': ['react', 'react-dom', 'react-router-dom'],
                    'coreui-vendor': ['@coreui/react', '@coreui/coreui', '@coreui/icons-react'],
                    'chart-vendor': ['chart.js'],
                },
            },
        },
    },

    // Resolve paths
    resolve: {
        alias: {
            '@': resolve(__dirname, 'src'),
            '@components': resolve(__dirname, 'src/components'),
            '@api': resolve(__dirname, 'src/api'),
            '@hooks': resolve(__dirname, 'src/hooks'),
        },
    },
});
