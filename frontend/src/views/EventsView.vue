<template>
  <div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-white mb-4">Discover Events</h1>
        <p class="text-gray-300 text-lg max-w-2xl mx-auto">
          Find amazing events happening around you. Book your tickets now!
        </p>
      </div>

      <!-- Search and Filters -->
      <div class="card p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div class="md:col-span-2">
            <label class="form-label">Search Events</label>
            <div class="relative">
              <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search events, categories, locations..."
                class="form-input pl-10"
                @input="debouncedSearch"
              />
            </div>
          </div>

          <!-- Category Filter -->
          <div>
            <label class="form-label">Category</label>
            <select v-model="filters.category" class="form-input" @change="handleFilterChange">
              <option value="">All Categories</option>
              <option v-for="category in eventsStore.categories" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
          </div>

          <!-- Sort -->
          <div>
            <label class="form-label">Sort By</label>
            <select v-model="filters.sort_by" class="form-input" @change="handleFilterChange">
              <option value="event_date">Event Date</option>
              <option value="price">Price</option>
              <option value="created_at">Recently Added</option>
            </select>
          </div>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <div>
            <label class="form-label">From Date</label>
            <input
              v-model="filters.date_from"
              type="date"
              class="form-input"
              @change="handleFilterChange"
            />
          </div>
          <div>
            <label class="form-label">To Date</label>
            <input
              v-model="filters.date_to"
              type="date"
              class="form-input"
              @change="handleFilterChange"
            />
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="eventsStore.loading" class="flex justify-center py-12">
        <div class="spinner"></div>
      </div>

      <!-- Events Grid -->
      <div v-else-if="eventsStore.events.length > 0">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <EventCard
            v-for="event in eventsStore.events"
            :key="event.id"
            :event="event"
          />
        </div>

        <!-- Pagination -->
        <div v-if="eventsStore.pagination.last_page > 1" class="flex justify-center">
          <nav class="flex items-center space-x-2">
            <button
              :disabled="eventsStore.pagination.current_page === 1"
              @click="changePage(eventsStore.pagination.current_page - 1)"
              class="btn btn-secondary disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronLeftIcon class="w-4 h-4" />
              Previous
            </button>

            <span class="text-gray-300 px-4">
              Page {{ eventsStore.pagination.current_page }} of {{ eventsStore.pagination.last_page }}
            </span>

            <button
              :disabled="eventsStore.pagination.current_page === eventsStore.pagination.last_page"
              @click="changePage(eventsStore.pagination.current_page + 1)"
              class="btn btn-secondary disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
              <ChevronRightIcon class="w-4 h-4" />
            </button>
          </nav>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <CalendarIcon class="w-16 h-16 text-gray-600 mx-auto mb-4" />
        <h3 class="text-xl font-semibold text-gray-400 mb-2">No Events Found</h3>
        <p class="text-gray-500 mb-4">
          {{ filters.search || filters.category ? 'Try adjusting your search criteria.' : 'No events are currently available.' }}
        </p>
        <button
          v-if="filters.search || filters.category || filters.date_from || filters.date_to"
          @click="clearFilters"
          class="btn btn-outline"
        >
          Clear Filters
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useEventsStore } from '@/stores/events'
import EventCard from '@/components/events/EventCard.vue'
import {
  MagnifyingGlassIcon,
  CalendarIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline'

const eventsStore = useEventsStore()

const filters = reactive({
  search: '',
  category: '',
  date_from: '',
  date_to: '',
  sort_by: 'event_date',
  sort_order: 'asc' as 'asc' | 'desc',
  per_page: 12
})

let searchTimeout: number | null = null

const debouncedSearch = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  searchTimeout = setTimeout(() => {
    handleFilterChange()
  }, 500)
}

const handleFilterChange = async () => {
  await eventsStore.fetchEvents({ ...filters })
}

const changePage = async (page: number) => {
  await eventsStore.fetchEvents({ ...filters, page })
}

const clearFilters = async () => {
  Object.assign(filters, {
    search: '',
    category: '',
    date_from: '',
    date_to: '',
    sort_by: 'event_date',
    sort_order: 'asc',
    per_page: 12
  })
  await handleFilterChange()
}

onMounted(async () => {
  await Promise.all([
    eventsStore.fetchEvents(filters),
    eventsStore.fetchCategories()
  ])
})
</script>
