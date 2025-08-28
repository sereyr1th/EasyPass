<template>
  <div class="min-h-screen bg-dark-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="flex justify-center">
          <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6 shadow-glow">
            <span class="text-white font-bold text-3xl">E</span>
          </div>
        </div>
        <h2 class="text-4xl font-bold text-white">Sign in to EasyPass</h2>
        <p class="mt-3 text-dark-400 text-lg">Welcome back! Please sign in to your account.</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="mt-8 space-y-6">
        <!-- Error Message -->
        <div v-if="authStore.error" class="bg-danger-900/20 border border-danger-600/30 rounded-xl p-4 backdrop-blur-sm">
          <div class="flex">
            <ExclamationTriangleIcon class="w-5 h-5 text-danger-400 mr-3 flex-shrink-0 mt-0.5" />
            <p class="text-danger-300 font-medium">{{ authStore.error }}</p>
          </div>
        </div>

        <div class="space-y-4">
          <!-- Email Field -->
          <div>
            <label for="email" class="form-label">Email Address</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <EnvelopeIcon class="h-5 w-5 text-gray-400" />
              </div>
              <input
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="email"
                required
                class="form-input pl-10"
                placeholder="Enter your email"
              />
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <label for="password" class="form-label">Password</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <LockClosedIcon class="h-5 w-5 text-gray-400" />
              </div>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                required
                class="form-input pl-10 pr-10"
                placeholder="Enter your password"
              />
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="text-gray-400 hover:text-gray-300 focus:outline-none"
                >
                  <EyeIcon v-if="!showPassword" class="h-5 w-5" />
                  <EyeSlashIcon v-else class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember-me"
              v-model="form.rememberMe"
              type="checkbox"
              class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-600 rounded bg-gray-700"
            />
            <label for="remember-me" class="ml-2 block text-sm text-gray-300">
              Remember me
            </label>
          </div>

          <RouterLink
            to="/auth/forgot-password"
            class="text-sm text-green-400 hover:text-green-300"
          >
            Forgot your password?
          </RouterLink>
        </div>

        <!-- Submit Button -->
        <div>
          <button
            type="submit"
            :disabled="authStore.loading"
            class="btn btn-primary w-full flex justify-center items-center"
          >
            <div v-if="authStore.loading" class="spinner mr-2"></div>
            <ArrowRightOnRectangleIcon v-else class="w-5 h-5 mr-2" />
            {{ authStore.loading ? 'Signing In...' : 'Sign In' }}
          </button>
        </div>

        <!-- Sign Up Link -->
        <div class="text-center">
          <p class="text-gray-400">
            Don't have an account?
            <RouterLink
              to="/auth/register"
              class="text-green-400 hover:text-green-300 font-medium"
            >
              Sign up here
            </RouterLink>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  EnvelopeIcon,
  LockClosedIcon,
  EyeIcon,
  EyeSlashIcon,
  ArrowRightOnRectangleIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const showPassword = ref(false)

const form = reactive({
  email: '',
  password: '',
  rememberMe: false
})

const handleLogin = async () => {
  const result = await authStore.login(form.email, form.password)
  
  if (result.success) {
    // Redirect to intended page or dashboard
    const redirectTo = route.query.redirect as string || '/dashboard'
    router.push(redirectTo)
  }
}
</script>
