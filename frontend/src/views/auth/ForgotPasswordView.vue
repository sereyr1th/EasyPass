<template>
  <div class="min-h-screen bg-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="flex justify-center">
          <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mb-4">
            <span class="text-white font-bold text-2xl">E</span>
          </div>
        </div>
        <h2 class="text-3xl font-bold text-white">Reset your password</h2>
        <p class="mt-2 text-gray-400">Enter your email address and we'll send you a reset link.</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="mt-8 space-y-6">
        <!-- Success Message -->
        <div v-if="submitted" class="bg-green-900/50 border border-green-700 rounded-lg p-4">
          <div class="flex">
            <svg class="w-5 h-5 text-green-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-green-300 text-sm">Reset link sent! Check your email.</p>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-900/50 border border-red-700 rounded-lg p-4">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <p class="text-red-300 text-sm">{{ error }}</p>
          </div>
        </div>

        <div>
          <label for="email" class="form-label">Email Address</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
              </svg>
            </div>
            <input
              id="email"
              v-model="email"
              type="email"
              autocomplete="email"
              required
              class="form-input pl-10"
              placeholder="Enter your email"
              :disabled="loading"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="btn btn-primary w-full flex justify-center items-center"
          >
            <div v-if="loading" class="spinner mr-2"></div>
            {{ loading ? 'Sending...' : 'Send Reset Link' }}
          </button>
        </div>

        <div class="text-center">
          <p class="text-gray-400">
            Remember your password?
            <RouterLink
              to="/auth/login"
              class="text-green-400 hover:text-green-300 font-medium"
            >
              Sign in here
            </RouterLink>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const email = ref('')
const loading = ref(false)
const submitted = ref(false)
const error = ref('')

const handleSubmit = async () => {
  if (!email.value) return

  loading.value = true
  error.value = ''
  
  const result = await authStore.forgotPassword(email.value)
  
  if (result.success) {
    submitted.value = true
  } else {
    error.value = result.message || 'Failed to send reset link'
  }
  
  loading.value = false
}
</script>
