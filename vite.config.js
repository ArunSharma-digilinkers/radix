import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Fonts are downloaded at build time and served from our own origin.
            // Never load these from the Google Fonts CDN: the extra connection and
            // render-blocking request works against the <2.5s target in the brief.
            //
            // Preloading is deliberately narrow. Only the weights that paint above
            // the fold are preloaded; preloading all eleven variants would compete
            // with the hero video for bandwidth on mobile.
            //
            // Devanagari is not requested here. When Hindi is switched on (Phase 8),
            // Archivo has no Devanagari coverage, so a companion family must be
            // chosen at that point rather than a subset simply added.
            fonts: [
                // Display / headings
                bunny('Archivo', {
                    weights: [600, 700, 800, 900],
                    preload: [{ weight: 800 }],
                    fallbacks: ['ui-sans-serif', 'system-ui', 'sans-serif'],
                }),
                // Body copy
                bunny('IBM Plex Sans', {
                    weights: [400, 500, 600],
                    preload: [{ weight: 400 }],
                    fallbacks: ['ui-sans-serif', 'system-ui', 'sans-serif'],
                }),
                // Eyebrows, spec labels, meta
                bunny('IBM Plex Mono', {
                    weights: [500, 600],
                    preload: false,
                    fallbacks: ['ui-monospace', 'monospace'],
                }),
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
