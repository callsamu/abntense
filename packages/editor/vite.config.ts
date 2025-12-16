import { defineConfig } from 'vite';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import dts from 'vite-plugin-dts';


const __dirname = dirname (fileURLToPath(import.meta.url));

export default defineConfig({
  build: {
      copyPublicDir: false,
      lib: {
          entry: resolve(__dirname, 'lib/main.js'),
          name: 'editor',
          fileName: 'editor',
          formats: ['es', 'cjs', 'umd'],
      },
      rollupOptions: {
        external: ['vue', 'tiptap'],
        output: {
            globals: {
                vue: 'Vue',
            },
        },
      },
  },

  plugins: [
      vue(),
      dts({
          include: ['lib'],
          rollupTypes: true,
          tsconfigPath: './tsconfig.app.json',
      }),
      tailwindcss()
  ],
})
