import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/filament/app/theme.css', 'resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                ...refreshPaths,
               'app/Http/Livewire/**',
               'app/Http/Filament/**',
            ],
        }),
    ],
});
