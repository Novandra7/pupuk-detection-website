import { defineConfig } from 'vite';
import path from 'path';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers';

export default defineConfig({
    resolve: {
        alias: {
            '~/': `${path.resolve(__dirname, 'resources')}/`,
            '@granule': `${path.resolve(__dirname, 'vendor/granule/starter-kit/src/Application/resources/js')}/`,
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@use "~/css/element-plus.scss" as *;`,
                api: 'modern-compiler',
                silenceDeprecations: ["legacy-js-api", "color-functions"]
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js'
            ],
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
        AutoImport({
            resolvers: [ElementPlusResolver()],
        }),
        Components({
            resolvers: [ElementPlusResolver({
                importStyle: "sass",
                directives: true,
                version: "2.1.5",
            })],
        }),
    ],
});