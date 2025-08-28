<template>
  <div class="min-h-screen bg-gray-900 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="text-center">
      <!-- 404 Illustration -->
      <div class="mb-8">
        <div class="text-9xl font-bold text-green-600 mb-4">404</div>
        <ExclamationTriangleIcon class="w-24 h-24 text-gray-600 mx-auto" />
      </div>

      <!-- Error Message -->
      <h1 class="text-4xl font-bold text-white mb-4">Page Not Found</h1>
      <p class="text-gray-300 text-lg mb-8 max-w-md mx-auto">
        Sorry, we couldn't find the page you're looking for. It might have been moved, deleted, or you entered the wrong URL.
      </p>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <RouterLink
          to="/"
          class="btn btn-primary inline-flex items-center justify-center"
        >
          <HomeIcon class="w-5 h-5 mr-2" />
          Go Home
        </RouterLink>
        
        <button
          @click="goBack"
          class="btn btn-secondary inline-flex items-center justify-center"
        >
          <ArrowLeftIcon class="w-5 h-5 mr-2" />
          Go Back
        </button>
        
        <RouterLink
          to="/events"
          class="btn btn-outline inline-flex items-center justify-center"
        >
          <CalendarIcon class="w-5 h-5 mr-2" />
          Browse Events
        </RouterLink>
      </div>

      <!-- Help Text -->
      <div class="mt-12 pt-8 border-t border-gray-700">
        <p class="text-gray-400 text-sm mb-4">
          If you believe this is an error, please contact our support team.
        </p>
        <RouterLink
          to="/contact"
          class="text-green-400 hover:text-green-300 text-sm font-medium"
        >
          Contact Support →
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
import {
  ExclamationTriangleIcon,
  HomeIcon,
  ArrowLeftIcon,
  CalendarIcon
} from '@heroicons/vue/24/outline'

const goBack = () => {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    window.location.href = '/'
  }
}
</script>
