<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container" style="max-width: 1000px;">
      <!-- Header -->
      <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-4 glow-animation" 
             style="width: 120px; height: 120px; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
          <span class="text-white fw-bold" style="font-size: 3rem;">
            {{ authStore.userName.charAt(0).toUpperCase() }}
          </span>
        </div>
        <h1 class="display-4 fw-bold text-light mb-3 professional-title">{{ authStore.userName }}</h1>
        <p class="lead text-muted">{{ authStore.userEmail }}</p>
      </div>

      <!-- Profile Information -->
      <div class="card mb-4">
        <div class="card-body">
          <h2 class="h4 fw-bold text-light mb-4 professional-title">Profile Information</h2>
          
          <form @submit.prevent="updateProfile">
            <!-- Success Message -->
            <div v-if="updateSuccess" class="alert alert-success d-flex align-items-center mb-4">
              <i class="bi bi-check-circle-fill me-2"></i>
              <div>Profile updated successfully!</div>
            </div>

            <!-- Error Message -->
            <div v-if="updateError" class="alert alert-danger d-flex align-items-center mb-4">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <div>{{ updateError }}</div>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Full Name</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  required
                  :disabled="updating"
                />
              </div>

              <div class="col-md-6">
                <label for="email" class="form-label">Email Address</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  required
                  :disabled="updating"
                />
              </div>
            </div>

            <div class="mt-4">
              <button
                type="submit"
                :disabled="updating"
                class="btn btn-primary d-flex align-items-center"
              >
                <div v-if="updating" class="spinner-border spinner-border-sm me-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <i v-else class="bi bi-person-check me-2"></i>
                {{ updating ? 'Updating...' : 'Update Profile' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Account Statistics -->
      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="card h-100 text-center">
            <div class="card-body">
              <div class="d-inline-flex align-items-center justify-content-center rounded-3 mx-auto mb-3 glow-animation" 
                   style="width: 64px; height: 64px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                <i class="bi bi-ticket-perforated fs-2 text-white"></i>
              </div>
              <h3 class="h5 fw-bold text-light mb-2 professional-title">Total Tickets</h3>
              <p class="display-6 fw-bold text-success">{{ stats.totalTickets }}</p>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card h-100 text-center">
            <div class="card-body">
              <div class="d-inline-flex align-items-center justify-content-center rounded-3 mx-auto mb-3 glow-animation" 
                   style="width: 64px; height: 64px; background: linear-gradient(135deg, #15803d 0%, #166534 100%);">
                <i class="bi bi-calendar-event fs-2 text-white"></i>
              </div>
              <h3 class="h5 fw-bold text-light mb-2 professional-title">Events Created</h3>
              <p class="display-6 fw-bold text-success">{{ stats.eventsCreated }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="row g-4">
        <div class="col-md-6">
          <RouterLink
            to="/tickets"
            class="card h-100 text-decoration-none"
            style="transition: transform 0.2s;"
          >
            <div class="card-body d-flex align-items-center">
              <div class="d-flex align-items-center justify-content-center rounded-3 me-3 glow-animation" 
                   style="width: 48px; height: 48px; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <i class="bi bi-ticket-perforated fs-5 text-white"></i>
              </div>
              <div>
                <h3 class="h6 fw-bold text-light mb-1 professional-title">View My Tickets</h3>
                <p class="text-muted small mb-0">Manage your purchased tickets</p>
              </div>
            </div>
          </RouterLink>
        </div>

        <div class="col-md-6">
          <RouterLink
            to="/dashboard"
            class="card h-100 text-decoration-none"
            style="transition: transform 0.2s;"
          >
            <div class="card-body d-flex align-items-center">
              <div class="d-flex align-items-center justify-content-center rounded-3 me-3 glow-animation" 
                   style="width: 48px; height: 48px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                <i class="bi bi-speedometer2 fs-5 text-white"></i>
              </div>
              <div>
                <h3 class="h6 fw-bold text-light mb-1 professional-title">Dashboard</h3>
                <p class="text-muted small mb-0">Manage your events</p>
              </div>
            </div>
          </RouterLink>
        </div>
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
