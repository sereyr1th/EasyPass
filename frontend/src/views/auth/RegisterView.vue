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
        <h2 class="text-3xl font-bold text-white">Create your account</h2>
        <p class="mt-2 text-gray-400">Join EasyPass and start managing events today!</p>
      </div>

      <!-- Registration Form -->
      <form @submit.prevent="handleRegister" class="mt-8 space-y-6">
        <!-- Error Message -->
        <div v-if="authStore.error" class="bg-red-900/50 border border-red-700 rounded-lg p-4">
          <div class="flex">
            <ExclamationTriangleIcon class="w-5 h-5 text-red-400 mr-2 flex-shrink-0 mt-0.5" />
            <p class="text-red-300 text-sm">{{ authStore.error }}</p>
          </div>
        </div>

        <div class="space-y-4">
          <!-- Name Field -->
          <div>
            <label for="name" class="form-label">Full Name</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <UserIcon class="h-5 w-5 text-gray-400" />
              </div>
              <input
                id="name"
                v-model="form.name"
                type="text"
                autocomplete="name"
                required
                class="form-input pl-10"
                placeholder="Enter your full name"
              />
            </div>
          </div>

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
                autocomplete="new-password"
                required
                class="form-input pl-10 pr-10"
                placeholder="Create a password"
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
            <p class="mt-1 text-xs text-gray-400">
              Password must be at least 8 characters long
            </p>
          </div>

          <!-- Confirm Password Field -->
          <div>
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <LockClosedIcon class="h-5 w-5 text-gray-400" />
              </div>
              <input
                id="password_confirmation"
                v-model="form.passwordConfirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                autocomplete="new-password"
                required
                class="form-input pl-10 pr-10"
                placeholder="Confirm your password"
              />
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <button
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="text-gray-400 hover:text-gray-300 focus:outline-none"
                >
                  <EyeIcon v-if="!showConfirmPassword" class="h-5 w-5" />
                  <EyeSlashIcon v-else class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-start">
          <input
            id="terms"
            v-model="form.acceptTerms"
            type="checkbox"
            required
            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-600 rounded bg-gray-700 mt-1"
          />
          <label for="terms" class="ml-2 block text-sm text-gray-300">
            I agree to the
            <a href="#" class="text-green-400 hover:text-green-300">Terms of Service</a>
            and
            <a href="#" class="text-green-400 hover:text-green-300">Privacy Policy</a>
          </label>
        </div>

        <!-- Submit Button -->
        <div>
          <button
            type="submit"
            :disabled="authStore.loading || !isFormValid"
            class="btn btn-primary w-full flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <div v-if="authStore.loading" class="spinner mr-2"></div>
            <UserPlusIcon v-else class="w-5 h-5 mr-2" />
            {{ authStore.loading ? 'Creating Account...' : 'Create Account' }}
          </button>
        </div>

        <!-- Sign In Link -->
        <div class="text-center">
          <p class="text-gray-400">
            Already have an account?
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
import { ref, reactive, computed } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  UserIcon,
  EnvelopeIcon,
  LockClosedIcon,
  EyeIcon,
  EyeSlashIcon,
  UserPlusIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const router = useRouter()

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const form = reactive({
  name: '',
  email: '',
  password: '',
  passwordConfirmation: '',
  acceptTerms: false
})

const isFormValid = computed(() => {
  return (
    form.name.trim() !== '' &&
    form.email.trim() !== '' &&
    form.password.length >= 8 &&
    form.password === form.passwordConfirmation &&
    form.acceptTerms
  )
})

const handleRegister = async () => {
  if (!isFormValid.value) return

  const result = await authStore.register(
    form.name,
    form.email,
    form.password,
    form.passwordConfirmation
  )
  
  if (result.success) {
    router.push('/dashboard')
  }
}
</script>
