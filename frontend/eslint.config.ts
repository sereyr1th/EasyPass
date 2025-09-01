import pluginVue from 'eslint-plugin-vue'
import { defineConfigWithVueTs } from '@vue/eslint-config-typescript'

export default defineConfigWithVueTs(
  {
    files: ['**/*.{ts,mts,tsx,vue}'],
  },
  {
    ignores: ['**/dist/**', '**/dist-ssr/**', '**/coverage/**'],
  },
  ...pluginVue.configs['flat/essential'],
  {
    rules: {
      // Add any custom rules here
      '@typescript-eslint/no-unused-vars': 'warn',
      'vue/multi-word-component-names': 'off',
    },
  }
)
