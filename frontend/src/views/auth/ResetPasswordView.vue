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
        <h2 class="text-3xl font-bold text-white">Set new password</h2>
        <p class="mt-2 text-gray-400">Enter your new password below.</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="mt-8 space-y-6">
        <!-- Success Message -->
        <div v-if="submitted" class="bg-green-900/50 border border-green-700 rounded-lg p-4">
          <div class="flex">
            <svg class="w-5 h-5 text-green-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-green-300 text-sm">Password reset successfully! You can now sign in.</p>
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

        <div class="space-y-4">
          <div>
            <label for="password" class="form-label">New Password</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="form-input"
              placeholder="Enter new password"
              :disabled="loading"
            />
          </div>

          <div>
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input
              id="password_confirmation"
              v-model="form.passwordConfirmation"
              type="password"
              required
              class="form-input"
              placeholder="Confirm new password"
              :disabled="loading"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading || !isFormValid"
            class="btn btn-primary w-full flex justify-center items-center disabled:opacity-50"
          >
            <div v-if="loading" class="spinner mr-2"></div>
            {{ loading ? 'Resetting...' : 'Reset Password' }}
          </button>
        </div>

        <div class="text-center">
          <RouterLink
            to="/auth/login"
            class="text-green-400 hover:text-green-300 font-medium"
          >
            Back to Sign In
          </RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const route = useRoute()
const router = useRouter()

const loading = ref(false)
const submitted = ref(false)
const error = ref('')

const form = reactive({
  password: '',
  passwordConfirmation: ''
})

const isFormValid = computed(() => {
  return form.password.length >= 8 && form.password === form.passwordConfirmation
})

const handleSubmit = async () => {
  if (!isFormValid.value) return

  loading.value = true
  error.value = ''
  
  const token = route.query.token as string
  const email = route.query.email as string
  
  if (!token || !email) {
    error.value = 'Invalid reset link'
    loading.value = false
    return
  }
  
  const result = await authStore.resetPassword(token, email, form.password, form.passwordConfirmation)
  
  if (result.success) {
    submitted.value = true
    setTimeout(() => {
      router.push('/auth/login')
    }, 2000)
  } else {
    error.value = result.message || 'Failed to reset password'
  }
  
  loading.value = false
}

onMounted(() => {
  // Check if we have the required query parameters
  if (!route.query.token || !route.query.email) {
    error.value = 'Invalid reset link. Please request a new password reset.'
  }
})
</script>
