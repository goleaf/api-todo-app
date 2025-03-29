import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  test: {
    globals: true,
    environment: 'happy-dom',
    include: ['resources/js/**/*.{test,spec}.js'],
    coverage: {
      reporter: ['text', 'json', 'html'],
    },
  },
}) 