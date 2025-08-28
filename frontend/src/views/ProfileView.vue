<template>
  <div class="min-h-screen bg-dark-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <div class="w-32 h-32 bg-gradient-to-br from-primary-600 to-primary-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-glow">
          <span class="text-white font-bold text-4xl">
            {{ authStore.userName.charAt(0).toUpperCase() }}
          </span>
        </div>
        <h1 class="text-4xl font-bold text-white mb-3">{{ authStore.userName }}</h1>
        <p class="text-dark-400 text-lg">{{ authStore.userEmail }}</p>
      </div>

      <!-- Profile Information -->
      <div class="card p-8 mb-8">
        <h2 class="text-2xl font-bold text-white mb-6">Profile Information</h2>
        
        <form @submit.prevent="updateProfile" class="space-y-6">
          <!-- Success Message -->
          <div v-if="updateSuccess" class="bg-success-900/20 border border-success-600/30 rounded-xl p-4 backdrop-blur-sm">
            <div class="flex">
              <svg class="w-5 h-5 text-success-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <p class="text-success-300 font-medium">Profile updated successfully!</p>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="updateError" class="bg-danger-900/20 border border-danger-600/30 rounded-xl p-4 backdrop-blur-sm">
            <div class="flex">
              <svg class="w-5 h-5 text-danger-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <p class="text-danger-300 font-medium">{{ updateError }}</p>
            </div>
          </div>

          <div>
            <label for="name" class="form-label">Full Name</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="form-input"
              required
              :disabled="updating"
            />
          </div>

          <div>
            <label for="email" class="form-label">Email Address</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="form-input"
              required
              :disabled="updating"
            />
          </div>

          <div>
            <button
              type="submit"
              :disabled="updating"
              class="btn btn-primary w-full flex items-center justify-center"
            >
              <div v-if="updating" class="spinner mr-2"></div>
              {{ updating ? 'Updating...' : 'Update Profile' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Account Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="card p-8 text-center hover:transform hover:scale-105 transition-all duration-300 group">
          <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-500 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:shadow-blue-500/30">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
              <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Total Tickets</h3>
          <p class="text-3xl font-bold text-blue-400">{{ stats.totalTickets }}</p>
        </div>

        <div class="card p-8 text-center hover:transform hover:scale-105 transition-all duration-300 group">
          <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-purple-500 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:shadow-purple-500/30">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Events Created</h3>
          <p class="text-3xl font-bold text-purple-400">{{ stats.eventsCreated }}</p>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <RouterLink
          to="/tickets"
          class="card p-8 hover:border-blue-500/50 transition-all duration-300 group transform hover:scale-105"
        >
          <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-500 rounded-xl flex items-center justify-center group-hover:shadow-blue-500/30 transition-all duration-300 shadow-lg">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-5">
              <h3 class="font-bold text-white text-lg group-hover:text-blue-400 transition-colors duration-300">View My Tickets</h3>
              <p class="text-dark-400 text-sm">Manage your purchased tickets</p>
            </div>
          </div>
        </RouterLink>

        <RouterLink
          to="/dashboard"
          class="card p-8 hover:border-primary-500/50 transition-all duration-300 group transform hover:scale-105"
        >
          <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-500 rounded-xl flex items-center justify-center group-hover:shadow-glow transition-all duration-300 shadow-lg">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15 13.586V12a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-5">
              <h3 class="font-bold text-white text-lg group-hover:text-primary-400 transition-colors duration-300">Dashboard</h3>
              <p class="text-dark-400 text-sm">Manage your events</p>
            </div>
          </div>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const updating = ref(false)
const updateSuccess = ref(false)
const updateError = ref('')

const form = reactive({
  name: authStore.userName,
  email: authStore.userEmail
})

const stats = reactive({
  totalTickets: 0,
  eventsCreated: 0
})

const updateProfile = async () => {
  updating.value = true
  updateError.value = ''
  updateSuccess.value = false
  
  // Simulate profile update (in real app, this would call an API)
  setTimeout(() => {
    updateSuccess.value = true
    updating.value = false
    
    // Clear success message after 3 seconds
    setTimeout(() => {
      updateSuccess.value = false
    }, 3000)
  }, 1000)
}

onMounted(() => {
  // In a real app, these would be fetched from the API
  stats.totalTickets = 5
  stats.eventsCreated = 2
})
</script>
