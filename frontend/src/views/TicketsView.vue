<template>
  <div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">My Tickets</h1>
        <p class="text-gray-300">View and manage your purchased event tickets</p>
      </div>

      <!-- Loading State -->
      <div v-if="ticketsStore.loading" class="flex justify-center py-12">
        <div class="spinner"></div>
      </div>

      <!-- Tickets Grid -->
      <div v-else-if="ticketsStore.tickets.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="ticket in ticketsStore.tickets"
          :key="ticket.id"
          class="card overflow-hidden"
        >
          <!-- Ticket Header -->
          <div class="bg-gradient-to-r from-green-600 to-green-700 p-4">
            <div class="flex items-center justify-between">
              <h3 class="text-white font-semibold">{{ ticket.event?.title }}</h3>
              <span
                :class="{
                  'bg-green-100 text-green-800': ticket.status === 'valid',
                  'bg-gray-100 text-gray-800': ticket.status === 'used',
                  'bg-red-100 text-red-800': ticket.status === 'cancelled'
                }"
                class="px-2 py-1 rounded-full text-xs font-medium"
              >
                {{ ticket.status }}
              </span>
            </div>
            <p class="text-green-100 text-sm">{{ ticket.event?.category }}</p>
          </div>

          <!-- Ticket Content -->
          <div class="p-6">
            <!-- Event Details -->
            <div class="space-y-3 mb-6">
              <div class="flex items-center text-gray-300 text-sm">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                <span>{{ formatDate(ticket.event?.event_date) }}</span>
              </div>
              
              <div class="flex items-center text-gray-300 text-sm">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                <span>{{ ticket.event?.location }}</span>
              </div>
              
              <div class="flex items-center text-gray-300 text-sm">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                  <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                </svg>
                <span class="font-mono">{{ ticket.ticket_number }}</span>
              </div>
            </div>

            <!-- QR Code -->
            <div v-if="ticket.status === 'valid'" class="bg-white p-4 rounded-lg mb-4 text-center">
              <img
                :src="`data:image/png;base64,${ticket.qr_code}`"
                :alt="`QR Code for ${ticket.ticket_number}`"
                class="w-24 h-24 mx-auto"
              />
              <p class="text-gray-600 text-xs mt-2">Scan at event entrance</p>
            </div>

            <!-- Actions -->
            <div class="flex space-x-2">
              <RouterLink
                :to="{ name: 'ticket-detail', params: { id: ticket.id } }"
                class="btn btn-secondary text-sm flex-1 text-center"
              >
                View Details
              </RouterLink>
              
              <button
                v-if="ticket.status === 'valid' && canCancel(ticket)"
                @click="cancelTicket(ticket.id)"
                class="btn btn-danger text-sm flex-1"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
          <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
          <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
        </svg>
        <h3 class="text-xl font-semibold text-gray-400 mb-2">No Tickets Yet</h3>
        <p class="text-gray-500 mb-6">You haven't purchased any tickets yet. Discover amazing events!</p>
        <RouterLink
          to="/events"
          class="btn btn-primary inline-flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
          </svg>
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
  const eventDate = new Date(ticket.event?.event_date)
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
