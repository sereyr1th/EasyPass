<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container" style="max-width: 1400px;">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
          <h1 class="display-5 fw-bold text-light mb-3 professional-title">
            <i class="bi bi-calendar-event me-3 text-success"></i>Event Management
          </h1>
          <p class="lead text-muted">Oversee and manage all events in the system</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Search</label>
              <input 
                v-model="searchQuery" 
                @input="debouncedSearch"
                type="text" 
                class="form-control" 
                placeholder="Search by title or category..."
              >
            </div>
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select v-model="statusFilter" @change="fetchEvents" class="form-select">
                <option value="">All Events</option>
                <option value="active">Active Events</option>
                <option value="past">Past Events</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">&nbsp;</label>
              <button @click="clearFilters" class="btn btn-outline-secondary w-100">
                <i class="bi bi-x-circle me-2"></i>Clear
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="adminStore.isLoading && events.length === 0" class="d-flex justify-content-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="adminStore.hasError" class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ adminStore.error }}
        <button @click="adminStore.clearError" class="btn-close float-end"></button>
      </div>

      <!-- Events Table -->
      <div v-else class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Events ({{ pagination.total }})</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Category</th>
                  <th>Date</th>
                  <th>Creator</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="event in events" :key="event.id">
                  <td>{{ event.id }}</td>
                  <td>
                    <div class="fw-semibold text-light">{{ event.title }}</div>
                  </td>
                  <td>
                    <span class="badge bg-info">{{ event.category }}</span>
                  </td>
                  <td>
                    <div class="text-light">{{ formatEventDate(event.event_date) }}</div>
                    <small class="text-muted">{{ formatEventTime(event.event_date) }}</small>
                  </td>
                  <td>
                    <span class="text-light">{{ event.creator?.name || 'N/A' }}</span>
                  </td>
                  <td>
                    <span :class="getStatusBadgeClass(event.event_date)">
                      {{ getEventStatus(event.event_date) }}
                    </span>
                  </td>
                  <td>
                    <small class="text-muted">{{ formatDate(event.created_at) }}</small>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <RouterLink 
                        :to="`/events/${event.id}`" 
                        class="btn btn-outline-info"
                        target="_blank"
                      >
                        <i class="bi bi-eye"></i>
                      </RouterLink>
                      <button 
                        @click="confirmDelete(event)" 
                        class="btn btn-outline-danger"
                        :disabled="adminStore.isLoading"
                      >
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="events.length === 0">
                  <td colspan="8" class="text-center py-4 text-muted">
                    No events found
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                <button @click="changePage(pagination.current_page - 1)" class="page-link">Previous</button>
              </li>
              
              <li v-for="page in visiblePages" :key="page" 
                  class="page-item" :class="{ active: page === pagination.current_page }">
                <button @click="changePage(page)" class="page-link">{{ page }}</button>
              </li>
              
              <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                <button @click="changePage(pagination.current_page + 1)" class="page-link">Next</button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" :class="{ show: showDeleteModal }" 
         :style="{ display: showDeleteModal ? 'block' : 'none' }"
         tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content bg-dark">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button @click="showDeleteModal = false" class="btn-close btn-close-white"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the event <strong>{{ eventToDelete?.title }}</strong>?</p>
            <p class="text-warning">
              <i class="bi bi-exclamation-triangle me-2"></i>
              This will also delete any associated tickets and cannot be undone.
            </p>
          </div>
          <div class="modal-footer">
            <button @click="showDeleteModal = false" class="btn btn-secondary">Cancel</button>
            <button @click="deleteEvent" class="btn btn-danger" :disabled="adminStore.isLoading">
              <span v-if="adminStore.isLoading">
                <div class="spinner-border spinner-border-sm me-2"></div>
                Deleting...
              </span>
              <span v-else>Delete</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div v-if="showDeleteModal" 
         class="modal-backdrop fade show" @click="showDeleteModal = false"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAdminStore } from '@/stores/admin'
import { useNotifications } from '@/composables/useNotifications'
import type { Event } from '@/stores/admin'

const adminStore = useAdminStore()
const { error: showError, success: showSuccess } = useNotifications()

// State
const searchQuery = ref('')
const statusFilter = ref('')
const showDeleteModal = ref(false)
const eventToDelete = ref<Event | null>(null)

// Computed
const events = computed(() => adminStore.events)
const pagination = computed(() => adminStore.eventsPagination)

const visiblePages = computed(() => {
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []
  
  const start = Math.max(1, current - 2)
  const end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const formatEventDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(date)
}

const formatEventTime = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const getEventStatus = (dateString: string) => {
  const eventDate = new Date(dateString)
  const now = new Date()
  return eventDate >= now ? 'Active' : 'Past'
}

const getStatusBadgeClass = (dateString: string) => {
  const eventDate = new Date(dateString)
  const now = new Date()
  return eventDate >= now ? 'badge bg-success' : 'badge bg-secondary'
}

const fetchEvents = () => {
  adminStore.fetchEvents(pagination.value.current_page, searchQuery.value, statusFilter.value)
}

const debouncedSearch = (() => {
  let timeout: NodeJS.Timeout
  return () => {
    clearTimeout(timeout)
    timeout = setTimeout(fetchEvents, 500)
  }
})()

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = ''
  fetchEvents()
}

const changePage = (page: number) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    adminStore.fetchEvents(page, searchQuery.value, statusFilter.value)
  }
}

const confirmDelete = (event: Event) => {
  eventToDelete.value = event
  showDeleteModal.value = true
}

const deleteEvent = async () => {
  if (eventToDelete.value) {
    const eventTitle = eventToDelete.value.title
    try {
      await adminStore.deleteEvent(eventToDelete.value.id)
      showDeleteModal.value = false
      eventToDelete.value = null
      showSuccess('Event Deleted', `"${eventTitle}" has been successfully deleted.`)
    } catch (error: any) {
      console.error('Failed to delete event:', error)
      showDeleteModal.value = false
      eventToDelete.value = null
      
      // Show user-friendly error notification
      const errorMessage = error.response?.data?.message || adminStore.error || 'Failed to delete event'
      showError('Cannot Delete Event', errorMessage)
    }
  }
}

onMounted(() => {
  fetchEvents()
})
</script>

<style scoped>
.card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.modal-content {
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.table th {
  border-color: rgba(255, 255, 255, 0.1);
}

.table td {
  border-color: rgba(255, 255, 255, 0.05);
}

.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
}
</style>
