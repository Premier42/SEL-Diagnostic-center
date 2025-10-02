import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  // Build output to public/dist
  build: {
    outDir: 'public/dist',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'resources/js/main.js'),
        style: resolve(__dirname, 'resources/css/style.css'),
      }
    }
  },

  // Dev server config
  server: {
    port: 5173,
    strictPort: true,
    cors: true,
    // HMR settings
    hmr: {
      host: 'localhost',
    },
  },

  // Public base path
  base: '/dist/',
});
