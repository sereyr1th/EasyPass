<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container">
      <!-- Header -->
      <div class="mb-5">
        <h1 class="display-3 fw-bold text-light mb-3 professional-title">Dashboard</h1>
        <p class="lead text-muted">Welcome back, {{ authStore.userName }}! Manage your events and tickets here.</p>
      </div>

      <!-- Quick Actions -->
      <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
          <RouterLink
            to="/dashboard/events/create"
            class="card h-100 text-decoration-none"
            style="transition: transform 0.2s;"
          >
            <div class="card-body d-flex align-items-center">
              <div class="d-flex align-items-center justify-content-center rounded-3 me-3 glow-animation" 
                   style="width: 48px; height: 48px; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <i class="bi bi-plus-circle fs-4 text-white"></i>
              </div>
              <div>
                <h5 class="card-title fw-bold text-light mb-1 professional-title">Create Event</h5>
                <p class="card-text text-muted small mb-0">Start organizing your event</p>
              </div>
            </div>
          </RouterLink>
        </div>

        <div class="col-lg-4 col-md-6">
          <RouterLink
            to="/tickets"
            class="card h-100 text-decoration-none"
            style="transition: transform 0.2s;"
          >
            <div class="card-body d-flex align-items-center">
              <div class="d-flex align-items-center justify-content-center rounded-3 me-3 glow-animation" 
                   style="width: 48px; height: 48px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                <i class="bi bi-ticket-perforated fs-4 text-white"></i>
              </div>
              <div>
                <h5 class="card-title fw-bold text-light mb-1 professional-title">My Tickets</h5>
                <p class="card-text text-muted small mb-0">View purchased tickets</p>
              </div>
            </div>
          </RouterLink>
        </div>

        <div class="col-lg-4 col-md-6">
          <RouterLink
            to="/validate"
            class="card h-100 text-decoration-none"
            style="transition: transform 0.2s;"
          >
            <div class="card-body d-flex align-items-center">
              <div class="d-flex align-items-center justify-content-center rounded-3 me-3 glow-animation" 
                   style="width: 48px; height: 48px; background: linear-gradient(135deg, #15803d 0%, #166534 100%);">
                <i class="bi bi-qr-code fs-4 text-white"></i>
              </div>
              <div>
                <h5 class="card-title fw-bold text-light mb-1 professional-title">Validate Tickets</h5>
                <p class="card-text text-muted small mb-0">Check ticket validity</p>
              </div>
            </div>
          </RouterLink>
        </div>
      </div>

      <!-- My Events Section -->
      <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="h3 fw-bold text-light professional-title">My Events</h2>
          <RouterLink
            to="/dashboard/events/create"
            class="btn btn-primary d-inline-flex align-items-center"
          >
            <i class="bi bi-plus-circle me-2"></i>
            Create Event
          </RouterLink>
        </div>

        <div v-if="eventsStore.loading" class="d-flex justify-content-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <div v-else-if="eventsStore.myEvents.length > 0" class="row g-4">
          <div
            v-for="event in eventsStore.myEvents"
            :key="event.id"
            class="col-lg-4 col-md-6"
          >
            <div class="card h-100 overflow-hidden">
              <!-- Event Image -->
              <div class="position-relative overflow-hidden" style="height: 180px;">
                <img
                  v-if="event.image_url"
                  :src="event.image_url"
                  :alt="event.title"
                  class="card-img-top w-100 h-100 object-fit-cover"
                />
                <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary">
                  <i class="bi bi-calendar-event display-4 text-muted"></i>
                </div>
                
                <!-- Status Badge -->
                <div class="position-absolute top-0 start-0 m-3">
                  <span
                    :class="{
                      'bg-success': event.status === 'active',
                      'bg-danger': event.status === 'cancelled',
                      'bg-secondary': event.status === 'completed'
                    }"
                    class="badge"
                  >
                    {{ event.status }}
                  </span>
                </div>
              </div>

              <!-- Event Content -->
              <div class="card-body">
                <h5 class="card-title fw-bold text-light mb-2 professional-title">{{ event.title }}</h5>
                <p class="text-muted small mb-3">{{ event.category }}</p>
                
                <div class="mb-3">
                  <div class="d-flex align-items-center text-muted small mb-2">
                    <i class="bi bi-calendar-event me-2 text-primary"></i>
                    <span>{{ formatDate(event.event_date) }}</span>
                  </div>
                  <div class="d-flex align-items-center text-muted small">
                    <i class="bi bi-people me-2 text-info"></i>
                    <span>{{ event.current_attendees }}/{{ event.max_attendees }} attendees</span>
                  </div>
                </div>

                <div class="d-grid gap-2">
                  <div class="d-flex gap-2">
                    <RouterLink
                      :to="{ name: 'edit-event', params: { id: event.id } }"
                      class="btn btn-outline-primary btn-sm flex-fill"
                    >
                      <i class="bi bi-pencil me-1"></i>Edit
                    </RouterLink>
                    <RouterLink
                      :to="{ name: 'event-detail', params: { id: event.id } }"
                      class="btn btn-outline-secondary btn-sm flex-fill"
                    >
                      <i class="bi bi-eye me-1"></i>View
                    </RouterLink>
                  </div>
                  <button
                    v-if="event.current_attendees === 0"
                    @click="deleteEvent(event)"
                    class="btn btn-outline-danger btn-sm w-100"
                    :disabled="eventsStore.loading"
                  >
                    <i class="bi bi-trash me-2"></i>Delete Event
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-5">
          <i class="bi bi-calendar-x display-1 text-muted mb-4"></i>
          <h3 class="h4 fw-bold text-light mb-3 professional-title">No Events Yet</h3>
          <p class="text-muted mb-4">Create your first event to get started!</p>
          <RouterLink
            to="/dashboard/events/create"
            class="btn btn-primary d-inline-flex align-items-center"
          >
            <i class="bi bi-plus-circle me-2"></i>
            Create Your First Event
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useEventsStore } from '@/stores/events'
import { useNotifications } from '@/composables/useNotifications'
import type { Event } from '@/stores/events'

const authStore = useAuthStore()
const eventsStore = useEventsStore()
const { success: showSuccess, error: showError } = useNotifications()

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const deleteEvent = async (event: Event) => {
  const confirmMessage = `Are you sure you want to delete "${event.title}"?\n\nThis action cannot be undone.`
  
  if (!confirm(confirmMessage)) {
    return
  }
  
  try {
    await eventsStore.deleteEvent(event.id)
    // The store handles notifications and list updates
  } catch (error) {
    console.error('Delete event error:', error)
  }
}

onMounted(async () => {
  await eventsStore.fetchMyEvents()
})
</script>
