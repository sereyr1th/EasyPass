<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container">
      <!-- Header -->
      <div class="mb-5">
        <h1 class="display-3 fw-bold text-light mb-3 professional-title">My Tickets</h1>
        <p class="lead text-muted">View and manage your purchased event tickets</p>
      </div>

      <!-- Loading State -->
      <div v-if="ticketsStore.loading" class="d-flex justify-content-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <!-- Tickets Grid -->
      <div v-else-if="ticketsStore.tickets.length > 0" class="row g-4">
        <div
          v-for="ticket in ticketsStore.tickets"
          :key="ticket.id"
          class="col-lg-4 col-md-6"
        >
          <div class="card h-100 overflow-hidden">
            <!-- Ticket Header -->
            <div class="card-header text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">{{ ticket.event?.title }}</h5>
                <span
                  :class="{
                    'bg-success': ticket.status === 'valid',
                    'bg-secondary': ticket.status === 'used',
                    'bg-danger': ticket.status === 'cancelled'
                  }"
                  class="badge"
                >
                  {{ ticket.status }}
                </span>
              </div>
              <small class="text-light opacity-75">{{ ticket.event?.category }}</small>
            </div>

            <!-- Ticket Content -->
            <div class="card-body">
              <!-- Event Details -->
              <div class="mb-4">
                <div class="d-flex align-items-center text-muted small mb-2">
                  <i class="bi bi-calendar-event me-2 text-primary"></i>
                  <span>{{ formatDate(ticket.event?.event_date || '') }}</span>
                </div>
                
                <div class="d-flex align-items-center text-muted small mb-2">
                  <i class="bi bi-geo-alt me-2 text-success"></i>
                  <span>{{ ticket.event?.location }}</span>
                </div>
                
                <div class="d-flex align-items-center text-muted small mb-3">
                  <i class="bi bi-ticket-perforated me-2 text-info"></i>
                  <code class="bg-secondary px-2 py-1 rounded">{{ ticket.ticket_number }}</code>
                </div>
              </div>

              <!-- QR Code -->
              <div v-if="ticket.status === 'valid'" class="bg-light p-3 rounded text-center mb-4">
                <img
                  :src="`data:image/png;base64,${ticket.qr_code}`"
                  :alt="`QR Code for ${ticket.ticket_number}`"
                  class="mx-auto"
                  style="width: 96px; height: 96px;"
                />
                <small class="text-muted d-block mt-2">Scan at event entrance</small>
              </div>

              <!-- Actions -->
              <div class="d-grid gap-2">
                <RouterLink
                  :to="{ name: 'ticket-detail', params: { id: ticket.id } }"
                  class="btn btn-outline-primary btn-sm"
                >
                  <i class="bi bi-eye me-2"></i>View Details
                </RouterLink>
                
                <button
                  v-if="ticket.status === 'valid' && canCancel(ticket)"
                  @click="cancelTicket(ticket.id)"
                  class="btn btn-outline-danger btn-sm"
                >
                  <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-5">
        <i class="bi bi-ticket-perforated display-1 text-muted mb-4"></i>
        <h3 class="h4 fw-bold text-light mb-3 professional-title">No Tickets Yet</h3>
        <p class="text-muted mb-4">You haven't purchased any tickets yet. Discover amazing events!</p>
        <RouterLink
          to="/events"
          class="btn btn-primary d-inline-flex align-items-center"
        >
          <i class="bi bi-calendar-event me-2"></i>
          Browse Events
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useTicketsStore } from '@/stores/tickets'

const ticketsStore = useTicketsStore()

const formatDate = (dateString: string) => {
  if (!dateString) return 'TBD'
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const canCancel = (ticket: any) => {
  if (!ticket.event?.event_date) return false
  const eventDate = new Date(ticket.event.event_date)
  const hoursUntilEvent = (eventDate.getTime() - Date.now()) / (1000 * 60 * 60)
  return hoursUntilEvent > 24 // Can cancel if more than 24 hours before event
}

const cancelTicket = async (ticketId: number) => {
  if (confirm('Are you sure you want to cancel this ticket? This action cannot be undone.')) {
    await ticketsStore.cancelTicket(ticketId)
  }
}

onMounted(async () => {
  await ticketsStore.fetchTickets()
})
</script>
