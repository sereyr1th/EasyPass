<template>
  <div class="card h-100 overflow-hidden glow-animation">
    <!-- Event Image -->
    <div class="position-relative overflow-hidden" style="height: 200px;">
      <img
        v-if="event.image_url"
        :src="event.image_url"
        :alt="event.title"
        class="card-img-top w-100 h-100 object-fit-cover"
        style="transition: transform 0.3s ease;"
      />
      <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary">
        <i class="bi bi-calendar-event display-3 text-muted"></i>
      </div>
      
      <!-- Event Status Badge -->
      <div class="position-absolute top-0 start-0 m-3">
        <span
          v-if="event.status === 'active' && isAvailable"
          class="badge bg-success"
        >
          Available
        </span>
        <span
          v-else-if="event.status === 'active' && !isAvailable"
          class="badge bg-warning text-dark"
        >
          Full
        </span>
        <span
          v-else-if="event.status === 'cancelled'"
          class="badge bg-danger"
        >
          Cancelled
        </span>
        <span
          v-else
          class="badge bg-secondary"
        >
          Completed
        </span>
      </div>

      <!-- Price Badge -->
      <div class="position-absolute top-0 end-0 m-3">
        <span class="badge bg-dark">
          {{ event.price > 0 ? `$${event.price}` : 'Free' }}
        </span>
      </div>
    </div>

    <!-- Event Content -->
    <div class="card-body">
      <!-- Category -->
      <div class="mb-3">
        <span class="text-success small fw-bold text-uppercase professional-title">{{ event.category }}</span>
      </div>

      <!-- Title -->
      <h5 class="card-title fw-bold text-light mb-3 professional-title" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
        {{ event.title }}
      </h5>

      <!-- Description -->
      <p class="card-text text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
        {{ event.description }}
      </p>

      <!-- Event Details -->
      <div class="mb-3">
        <!-- Date -->
        <div class="d-flex align-items-center text-muted small mb-2">
          <i class="bi bi-calendar-event me-2 text-primary"></i>
          <span>{{ formatDate(event.event_date) }}</span>
        </div>

        <!-- Location -->
        <div class="d-flex align-items-center text-muted small mb-2">
          <i class="bi bi-geo-alt me-2 text-success"></i>
          <span class="text-truncate">{{ event.location }}</span>
        </div>

        <!-- Attendees -->
        <div class="d-flex align-items-center text-muted small">
          <i class="bi bi-people me-2 text-info"></i>
          <span>{{ event.current_attendees }}/{{ event.max_attendees }} attendees</span>
        </div>
      </div>

      <!-- Action Button -->
      <RouterLink
        :to="{ name: 'event-detail', params: { id: event.id } }"
        class="btn btn-primary w-100 d-flex align-items-center justify-content-center"
      >
        <i class="bi bi-eye me-2"></i>
        View Details
      </RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import type { Event } from '@/stores/events'

interface Props {
  event: Event
}

const props = defineProps<Props>()

const isAvailable = computed(() => {
  const now = new Date()
  const eventDate = new Date(props.event.event_date)
  const registrationDeadline = new Date(props.event.registration_deadline)
  
  return (
    props.event.status === 'active' &&
    props.event.current_attendees < props.event.max_attendees &&
    registrationDeadline > now &&
    eventDate > now
  )
})

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}
</script>


