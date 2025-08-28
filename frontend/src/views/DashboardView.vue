<template>
  <div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Dashboard</h1>
        <p class="text-gray-300">Welcome back, {{ authStore.userName }}! Manage your events and tickets here.</p>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <RouterLink
          to="/dashboard/events/create"
          class="card p-6 hover:bg-gray-700 transition-colors duration-200 group"
        >
          <div class="flex items-center">
            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center group-hover:bg-green-500 transition-colors duration-200">
              <PlusIcon class="w-6 h-6 text-white" />
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-semibold text-white">Create Event</h3>
              <p class="text-gray-400">Start organizing your event</p>
            </div>
          </div>
        </RouterLink>

        <RouterLink
          to="/tickets"
          class="card p-6 hover:bg-gray-700 transition-colors duration-200 group"
        >
          <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center group-hover:bg-blue-500 transition-colors duration-200">
              <TicketIcon class="w-6 h-6 text-white" />
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-semibold text-white">My Tickets</h3>
              <p class="text-gray-400">View purchased tickets</p>
            </div>
          </div>
        </RouterLink>

        <RouterLink
          to="/validate"
          class="card p-6 hover:bg-gray-700 transition-colors duration-200 group"
        >
          <div class="flex items-center">
            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center group-hover:bg-purple-500 transition-colors duration-200">
              <QrCodeIcon class="w-6 h-6 text-white" />
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-semibold text-white">Validate Tickets</h3>
              <p class="text-gray-400">Check ticket validity</p>
            </div>
          </div>
        </RouterLink>
      </div>

      <!-- My Events Section -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-white">My Events</h2>
          <RouterLink
            to="/dashboard/events/create"
            class="btn btn-primary inline-flex items-center"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
            Create Event
          </RouterLink>
        </div>

        <div v-if="eventsStore.loading" class="flex justify-center py-12">
          <div class="spinner"></div>
        </div>

        <div v-else-if="eventsStore.myEvents.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="event in eventsStore.myEvents"
            :key="event.id"
            class="card overflow-hidden"
          >
            <!-- Event Image -->
            <div class="aspect-video bg-gray-700 relative">
              <img
                v-if="event.image_url"
                :src="event.image_url"
                :alt="event.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <CalendarIcon class="w-12 h-12 text-gray-500" />
              </div>
              
              <!-- Status Badge -->
              <div class="absolute top-4 left-4">
                <span
                  :class="{
                    'bg-green-600': event.status === 'active',
                    'bg-red-600': event.status === 'cancelled',
                    'bg-gray-600': event.status === 'completed'
                  }"
                  class="px-2 py-1 text-white text-xs font-medium rounded-full"
                >
                  {{ event.status }}
                </span>
              </div>
            </div>

            <!-- Event Content -->
            <div class="p-6">
              <h3 class="text-lg font-semibold text-white mb-2">{{ event.title }}</h3>
              <p class="text-gray-400 text-sm mb-4">{{ event.category }}</p>
              
              <div class="space-y-2 mb-4">
                <div class="flex items-center text-gray-400 text-sm">
                  <CalendarIcon class="w-4 h-4 mr-2" />
                  <span>{{ formatDate(event.event_date) }}</span>
                </div>
                <div class="flex items-center text-gray-400 text-sm">
                  <UsersIcon class="w-4 h-4 mr-2" />
                  <span>{{ event.current_attendees }}/{{ event.max_attendees }} attendees</span>
                </div>
              </div>

              <div class="flex space-x-2">
                <RouterLink
                  :to="{ name: 'edit-event', params: { id: event.id } }"
                  class="btn btn-secondary text-sm flex-1 text-center"
                >
                  Edit
                </RouterLink>
                <RouterLink
                  :to="{ name: 'event-detail', params: { id: event.id } }"
                  class="btn btn-outline text-sm flex-1 text-center"
                >
                  View
                </RouterLink>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-12">
          <CalendarIcon class="w-16 h-16 text-gray-600 mx-auto mb-4" />
          <h3 class="text-xl font-semibold text-gray-400 mb-2">No Events Yet</h3>
          <p class="text-gray-500 mb-4">Create your first event to get started!</p>
          <RouterLink
            to="/dashboard/events/create"
            class="btn btn-primary inline-flex items-center"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
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
import {
  PlusIcon,
  TicketIcon,
  QrCodeIcon,
  CalendarIcon,
  UsersIcon
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const eventsStore = useEventsStore()

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

onMounted(async () => {
  await eventsStore.fetchMyEvents()
})
</script>
