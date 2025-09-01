module.exports = {
  root: true,
  env: {
    node: true,
    browser: true,
    es2022: true,
  },
  extends: [
    'eslint:recommended',
    '@vue/eslint-config-typescript/recommended',
    'plugin:vue/vue3-essential',
  ],
  parser: 'vue-eslint-parser',
  parserOptions: {
    parser: '@typescript-eslint/parser',
    ecmaVersion: 2022,
    sourceType: 'module',
    extraFileExtensions: ['.vue'],
  },
  plugins: ['@typescript-eslint', 'vue'],
  rules: {
    // Add custom rules here
    '@typescript-eslint/no-unused-vars': 'warn',
    'vue/multi-word-component-names': 'off',
  },
  ignorePatterns: ['dist/', 'dist-ssr/', 'coverage/', 'node_modules/'],
}
