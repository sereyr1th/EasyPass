<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="text-light fw-bold mb-1">All Tickets</h2>
            <p class="text-muted mb-0">Manage all user tickets across the platform</p>
          </div>
          <div class="d-flex gap-2">
            <button 
              @click="refreshTickets" 
              :disabled="adminStore.isLoading"
              class="btn btn-outline-primary"
            >
              <i class="bi bi-arrow-clockwise me-2"></i>
              {{ adminStore.isLoading ? 'Loading...' : 'Refresh' }}
            </button>
          </div>
        </div>

        <!-- Error Alert -->
        <div v-if="adminStore.hasError" class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>
          {{ adminStore.error }}
          <button type="button" class="btn-close" @click="adminStore.clearError()"></button>
        </div>

        <!-- Tickets Table -->
        <div class="card bg-dark border-secondary">
          <div class="card-header bg-transparent border-secondary">
            <h5 class="card-title text-light mb-0">
              <i class="bi bi-ticket me-2"></i>
              Tickets ({{ adminStore.ticketsPagination.total }})
            </h5>
          </div>
          <div class="card-body p-0">
            <div v-if="adminStore.isLoading && adminStore.tickets.length === 0" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="text-muted mt-3">Loading tickets...</p>
            </div>
            
            <div v-else-if="adminStore.tickets.length === 0" class="text-center py-5">
              <i class="bi bi-ticket display-1 text-muted"></i>
              <h4 class="text-muted mt-3">No Tickets Found</h4>
              <p class="text-muted">No tickets have been purchased yet.</p>
            </div>
            
            <div v-else class="table-responsive">
              <table class="table table-dark table-hover mb-0">
                <thead class="table-secondary">
                  <tr>
                    <th>Ticket ID</th>
                    <th>Event</th>
                    <th>User</th>
                    <th>Amount Paid</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Purchase Date</th>
                    <th>Event Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ticket in adminStore.tickets" :key="ticket.id">
                    <td>
                      <code class="text-primary">#{{ ticket.id }}</code>
                      <br>
                      <small class="text-muted">{{ ticket.verification_code }}</small>
                    </td>
                    <td>
                      <div class="fw-semibold text-light">{{ ticket.event?.title || 'N/A' }}</div>
                      <small class="text-muted">
                        <i class="bi bi-tag me-1"></i>{{ ticket.event?.category || 'N/A' }}
                      </small>
                    </td>
                    <td>
                      <div class="fw-semibold text-light">{{ ticket.user?.name || 'N/A' }}</div>
                      <small class="text-muted">{{ ticket.user?.email || 'N/A' }}</small>
                    </td>
                    <td>
                      <div class="fw-semibold text-success">
                        ${{ formatCurrency(ticket.purchase?.amount_paid || 0) }}
                      </div>
                      <small class="text-muted">
                        {{ ticket.purchase?.payment_status || 'N/A' }}
                      </small>
                    </td>
                    <td>
                      <span class="badge bg-info text-dark">
                        <i class="bi bi-credit-card me-1"></i>
                        {{ ticket.purchase?.payment_method || 'N/A' }}
                      </span>
                    </td>
                    <td>
                      <span 
                        :class="{
                          'badge bg-success': ticket.status === 'valid',
                          'badge bg-danger': ticket.status === 'cancelled',
                          'badge bg-warning text-dark': ticket.status === 'used'
                        }"
                      >
                        <i 
                          :class="{
                            'bi bi-check-circle': ticket.status === 'valid',
                            'bi bi-x-circle': ticket.status === 'cancelled',
                            'bi bi-clock': ticket.status === 'used'
                          }"
                          class="me-1"
                        ></i>
                        {{ ticket.status }}
                      </span>
                    </td>
                    <td>
                      <div class="text-light">{{ formatDate(ticket.purchase?.created_at || ticket.created_at) }}</div>
                    </td>
                    <td>
                      <div class="text-light">{{ formatDate(ticket.event?.event_date) }}</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <!-- Pagination -->
          <div v-if="adminStore.ticketsPagination.last_page > 1" class="card-footer bg-transparent border-secondary">
            <nav aria-label="Tickets pagination">
              <ul class="pagination pagination-sm justify-content-center mb-0">
                <li class="page-item" :class="{ disabled: adminStore.ticketsPagination.current_page === 1 }">
                  <button 
                    class="page-link bg-dark border-secondary text-light"
                    @click="changePage(adminStore.ticketsPagination.current_page - 1)"
                    :disabled="adminStore.ticketsPagination.current_page === 1"
                  >
                    Previous
                  </button>
                </li>
                
                <li 
                  v-for="page in getPageNumbers()" 
                  :key="page"
                  class="page-item"
                  :class="{ active: page === adminStore.ticketsPagination.current_page }"
                >
                  <button 
                    class="page-link bg-dark border-secondary text-light"
                    @click="changePage(page)"
                  >
                    {{ page }}
                  </button>
                </li>
                
                <li class="page-item" :class="{ disabled: adminStore.ticketsPagination.current_page === adminStore.ticketsPagination.last_page }">
                  <button 
                    class="page-link bg-dark border-secondary text-light"
                    @click="changePage(adminStore.ticketsPagination.current_page + 1)"
                    :disabled="adminStore.ticketsPagination.current_page === adminStore.ticketsPagination.last_page"
                  >
                    Next
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useAdminStore } from '@/stores/admin'

const adminStore = useAdminStore()

const formatCurrency = (amount: number): string => {
  return (amount || 0).toFixed(2)
}

const formatDate = (dateString: string | undefined): string => {
  if (!dateString) return 'N/A'
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(dateString))
}

const refreshTickets = async () => {
  await adminStore.fetchTickets(adminStore.ticketsPagination.current_page)
}

const changePage = async (page: number) => {
  if (page >= 1 && page <= adminStore.ticketsPagination.last_page) {
    await adminStore.fetchTickets(page)
  }
}

const getPageNumbers = () => {
  const current = adminStore.ticketsPagination.current_page
  const last = adminStore.ticketsPagination.last_page
  const pages: number[] = []
  
  // Show max 5 page numbers
  let start = Math.max(1, current - 2)
  let end = Math.min(last, start + 4)
  
  // Adjust start if we're near the end
  if (end - start < 4) {
    start = Math.max(1, end - 4)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
}

onMounted(async () => {
  await adminStore.fetchTickets()
})
</script>

<style scoped>
.table-dark {
  --bs-table-bg: transparent;
}

.page-link.bg-dark:hover {
  background-color: #495057 !important;
}

.page-item.active .page-link {
  background-color: #0d6efd !important;
  border-color: #0d6efd !important;
}

code {
  font-size: 0.875rem;
}
</style>
