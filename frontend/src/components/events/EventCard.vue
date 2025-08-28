<template>
  <div class="card overflow-hidden hover:shadow-glow-lg transition-all duration-500 transform hover:scale-105 group">
    <!-- Event Image -->
    <div class="aspect-video bg-dark-700 relative overflow-hidden">
      <img
        v-if="event.image_url"
        :src="event.image_url"
        :alt="event.title"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
      />
      <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-dark-700 to-dark-600">
        <CalendarIcon class="w-20 h-20 text-dark-500 group-hover:text-primary-400 transition-colors duration-300" />
      </div>
      
      <!-- Event Status Badge -->
      <div class="absolute top-6 left-6">
        <span
          v-if="event.status === 'active' && isAvailable"
          class="px-3 py-2 bg-success-600 text-white text-sm font-semibold rounded-xl backdrop-blur-sm shadow-lg"
        >
          Available
        </span>
        <span
          v-else-if="event.status === 'active' && !isAvailable"
          class="px-3 py-2 bg-warning-600 text-white text-sm font-semibold rounded-xl backdrop-blur-sm shadow-lg"
        >
          Full
        </span>
        <span
          v-else-if="event.status === 'cancelled'"
          class="px-3 py-2 bg-danger-600 text-white text-sm font-semibold rounded-xl backdrop-blur-sm shadow-lg"
        >
          Cancelled
        </span>
        <span
          v-else
          class="px-3 py-2 bg-dark-600 text-white text-sm font-semibold rounded-xl backdrop-blur-sm shadow-lg"
        >
          Completed
        </span>
      </div>

      <!-- Price Badge -->
      <div class="absolute top-6 right-6">
        <span class="px-4 py-2 bg-dark-900/80 backdrop-blur-sm text-white text-sm font-bold rounded-xl shadow-lg">
          {{ event.price > 0 ? `$${event.price}` : 'Free' }}
        </span>
      </div>
    </div>

    <!-- Event Content -->
    <div class="p-8">
      <!-- Category -->
      <div class="mb-3">
        <span class="text-primary-400 text-sm font-semibold uppercase tracking-wider">{{ event.category }}</span>
      </div>

      <!-- Title -->
      <h3 class="text-2xl font-bold text-white mb-4 line-clamp-2 group-hover:text-primary-300 transition-colors duration-300">
        {{ event.title }}
      </h3>

      <!-- Description -->
      <p class="text-dark-300 text-base mb-6 line-clamp-2 leading-relaxed">
        {{ event.description }}
      </p>

      <!-- Event Details -->
      <div class="space-y-3 mb-8">
        <!-- Date -->
        <div class="flex items-center text-dark-400 text-sm">
          <CalendarIcon class="w-5 h-5 mr-3 text-primary-400" />
          <span class="font-medium">{{ formatDate(event.event_date) }}</span>
        </div>

        <!-- Location -->
        <div class="flex items-center text-dark-400 text-sm">
          <MapPinIcon class="w-5 h-5 mr-3 text-primary-400" />
          <span class="truncate font-medium">{{ event.location }}</span>
        </div>

        <!-- Attendees -->
        <div class="flex items-center text-dark-400 text-sm">
          <UsersIcon class="w-5 h-5 mr-3 text-primary-400" />
          <span class="font-medium">{{ event.current_attendees }}/{{ event.max_attendees }} attendees</span>
        </div>
      </div>

      <!-- Action Button -->
      <RouterLink
        :to="{ name: 'event-detail', params: { id: event.id } }"
        class="btn btn-primary w-full text-center inline-flex items-center justify-center text-lg py-4 group-hover:shadow-glow-lg transform group-hover:scale-105"
      >
        <EyeIcon class="w-5 h-5 mr-3" />
        View Details
      </RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import type { Event } from '@/stores/events'
import {
  CalendarIcon,
  MapPinIcon,
  UsersIcon,
  EyeIcon
} from '@heroicons/vue/24/outline'

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

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
