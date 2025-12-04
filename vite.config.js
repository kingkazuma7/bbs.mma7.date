import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/scss/app.scss'],
            refresh: true,
        }),
    ],

    // ↓↓　WSL使用時のみ必要なコード　↓↓
    server: {
        hmr: {
            host: 'localhost'
        }
    }
    // ↑↑　WSL使用時のみ必要なコード　↑↑
});
