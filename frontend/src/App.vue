<script setup lang="ts">
import { RouterView } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { onMounted } from 'vue'
import Navbar from '@/components/layout/Navbar.vue'

const authStore = useAuthStore()

onMounted(() => {
  // Check for existing token on app load
  authStore.checkAuth()
})
</script>

<template>
  <div id="app" class="min-h-screen bg-dark-900 text-white">
    <Navbar />
    <main class="pt-16">
      <RouterView />
    </main>
  </div>
</template>

<style>
/* Enhanced dark green, black, and white theme */
:root {
  /* Primary green palette */
  --primary-green: #16a34a;
  --primary-green-dark: #15803d;
  --primary-green-light: #22c55e;
  --secondary-green: #14532d;
  --accent-green: #4ade80;
  
  /* Dark theme colors */
  --dark-950: #020617;
  --dark-900: #0f172a;
  --dark-800: #1e293b;
  --dark-700: #334155;
  --dark-600: #475569;
  --dark-500: #64748b;
  --dark-400: #94a3b8;
  --dark-300: #cbd5e1;
  --dark-200: #e2e8f0;
  --dark-100: #f1f5f9;
  --dark-50: #f8fafc;
  
  /* Semantic colors */
  --success: #22c55e;
  --danger: #ef4444;
  --warning: #f59e0b;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  background: linear-gradient(135deg, var(--dark-900) 0%, var(--dark-800) 100%);
  color: var(--dark-50);
  line-height: 1.6;
}

#app {
  min-height: 100vh;
}

/* Enhanced custom scrollbar */
::-webkit-scrollbar {
  width: 10px;
}

::-webkit-scrollbar-track {
  background: var(--dark-800);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, var(--primary-green) 0%, var(--accent-green) 100%);
  border-radius: 10px;
  border: 2px solid var(--dark-800);
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 100%);
}

/* Enhanced button styles */
.btn {
  @apply px-6 py-3 rounded-xl font-semibold transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-offset-dark-900 transform hover:scale-105 active:scale-95;
}

.btn-primary {
  @apply bg-gradient-to-r from-primary-600 to-primary-500 text-white hover:from-primary-700 hover:to-primary-600 focus:ring-primary-500 shadow-lg hover:shadow-glow;
}

.btn-secondary {
  @apply bg-dark-700 text-dark-100 hover:bg-dark-600 focus:ring-dark-500 shadow-lg hover:shadow-dark;
}

.btn-danger {
  @apply bg-gradient-to-r from-danger-600 to-danger-500 text-white hover:from-danger-700 hover:to-danger-600 focus:ring-danger-500 shadow-lg;
}

.btn-outline {
  @apply border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white focus:ring-primary-500 bg-transparent backdrop-blur-sm;
}

/* Enhanced input styles */
.form-input {
  @apply w-full px-4 py-3 bg-dark-800/80 backdrop-blur-sm border border-dark-600 rounded-xl text-white placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-300;
}

.form-input:focus {
  @apply shadow-glow;
}

.form-label {
  @apply block text-sm font-semibold text-dark-200 mb-3;
}

/* Enhanced card styles */
.card {
  @apply bg-dark-800/90 backdrop-blur-md rounded-2xl shadow-dark-lg border border-dark-700/50 transition-all duration-300 hover:shadow-dark-lg hover:border-dark-600/50;
}

.card-header {
  @apply px-8 py-6 border-b border-dark-700/50;
}

.card-body {
  @apply px-8 py-6;
}

/* Enhanced loading spinner */
.spinner {
  @apply animate-spin h-6 w-6 border-3 border-primary-500 border-t-transparent rounded-full;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from {
  transform: translateX(-100%);
}

.slide-leave-to {
  transform: translateX(100%);
}
</style>
