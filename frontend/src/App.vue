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
  <div id="app" class="min-vh-100 bg-dark-custom text-light position-relative overflow-hidden">
    <!-- Animated background effects -->
    <div class="position-fixed top-0 start-0 w-100 h-100" style="pointer-events: none; z-index: -1;">
      <!-- Gradient orbs -->
      <div class="position-absolute rounded-circle glow-animation" 
           style="top: 0; left: 25%; width: 400px; height: 400px; 
                  background: radial-gradient(circle, rgba(5, 150, 105, 0.3) 0%, rgba(16, 185, 129, 0.15) 100%); 
                  filter: blur(60px);"></div>
      <div class="position-absolute rounded-circle glow-animation" 
           style="bottom: 0; right: 25%; width: 350px; height: 350px; 
                  background: radial-gradient(circle, rgba(34, 197, 94, 0.25) 0%, rgba(22, 163, 74, 0.1) 100%); 
                  filter: blur(60px); animation-delay: 1s;"></div>
      <div class="position-absolute rounded-circle glow-animation" 
           style="top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px; height: 300px; 
                  background: radial-gradient(circle, rgba(6, 78, 59, 0.2) 0%, rgba(5, 150, 105, 0.1) 100%); 
                  filter: blur(60px); animation-delay: 2s;"></div>
    </div>
    
    <Navbar />
    <main class="position-relative">
      <RouterView />
    </main>
  </div>
</template>


