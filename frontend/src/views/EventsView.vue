<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container">
      <!-- Header -->
      <div class="text-center mb-5">
        <h1 class="display-2 fw-bold text-light mb-4 professional-title">Discover Events</h1>
        <p class="lead text-muted mx-auto" style="max-width: 600px;">
          Find amazing events happening around you. Book your tickets now and create unforgettable memories!
        </p>
      </div>

      <!-- Search and Filters -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="row g-3">
            <!-- Search -->
            <div class="col-md-6">
              <label class="form-label">Search Events</label>
              <div class="position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input
                  v-model="filters.search"
                  type="text"
                  placeholder="Search events, categories, locations..."
                  class="form-control ps-5"
                  @input="debouncedSearch"
                />
              </div>
            </div>

            <!-- Category Filter -->
            <div class="col-md-3">
              <label class="form-label">Category</label>
              <select v-model="filters.category" class="form-select" @change="handleFilterChange">
                <option value="">All Categories</option>
                <option v-for="category in eventsStore.categories" :key="category" :value="category">
                  {{ category }}
                </option>
              </select>
            </div>

            <!-- Sort -->
            <div class="col-md-3">
              <label class="form-label">Sort By</label>
              <select v-model="filters.sort_by" class="form-select" @change="handleFilterChange">
                <option value="event_date">Event Date</option>
                <option value="price">Price</option>
                <option value="created_at">Recently Added</option>
              </select>
            </div>
          </div>

          <!-- Date Range -->
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">From Date</label>
              <input
                v-model="filters.date_from"
                type="date"
                class="form-control"
                @change="handleFilterChange"
              />
            </div>
            <div class="col-md-6">
              <label class="form-label">To Date</label>
              <input
                v-model="filters.date_to"
                type="date"
                class="form-control"
                @change="handleFilterChange"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="eventsStore.loading" class="d-flex justify-content-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <!-- Events Grid -->
      <div v-else-if="eventsStore.events.length > 0">
        <div class="row g-4 mb-5">
          <div v-for="event in eventsStore.events" :key="event.id" class="col-lg-4 col-md-6">
            <EventCard :event="event" />
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="eventsStore.pagination.last_page > 1" class="d-flex justify-content-center">
          <nav aria-label="Page navigation">
            <ul class="pagination">
              <li class="page-item" :class="{ disabled: eventsStore.pagination.current_page === 1 }">
                <button
                  class="page-link"
                  @click="changePage(eventsStore.pagination.current_page - 1)"
                  :disabled="eventsStore.pagination.current_page === 1"
                >
                  <i class="bi bi-chevron-left me-1"></i>
                  Previous
                </button>
              </li>

              <li class="page-item disabled">
                <span class="page-link">
                  Page {{ eventsStore.pagination.current_page }} of {{ eventsStore.pagination.last_page }}
                </span>
              </li>

              <li class="page-item" :class="{ disabled: eventsStore.pagination.current_page === eventsStore.pagination.last_page }">
                <button
                  class="page-link"
                  @click="changePage(eventsStore.pagination.current_page + 1)"
                  :disabled="eventsStore.pagination.current_page === eventsStore.pagination.last_page"
                >
                  Next
                  <i class="bi bi-chevron-right ms-1"></i>
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-5">
        <i class="bi bi-calendar-x display-1 text-muted mb-4"></i>
        <h3 class="h4 fw-bold text-light mb-3 professional-title">No Events Found</h3>
        <p class="text-muted mb-4">
          {{ filters.search || filters.category ? 'Try adjusting your search criteria.' : 'No events are currently available.' }}
        </p>
        <button
          v-if="filters.search || filters.category || filters.date_from || filters.date_to"
          @click="clearFilters"
          class="btn btn-outline-primary"
        >
          Clear Filters
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, onMounted } from 'vue'
import { useEventsStore } from '@/stores/events'
import EventCard from '@/components/events/EventCard.vue'

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

let searchTimeout: ReturnType<typeof setTimeout> | null = null

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
