<template>
  <div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="flex justify-center py-12">
        <div class="spinner"></div>
      </div>
      
      <div v-else-if="ticket" class="space-y-6">
        <!-- Ticket Card -->
        <div class="card overflow-hidden">
          <!-- Header -->
          <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
            <div class="text-center">
              <h1 class="text-2xl font-bold text-white mb-2">{{ ticket.event?.title }}</h1>
              <p class="text-green-100">{{ ticket.event?.category }}</p>
              <div class="mt-4">
                <span
                  :class="{
                    'bg-green-100 text-green-800': ticket.status === 'valid',
                    'bg-gray-100 text-gray-800': ticket.status === 'used',
                    'bg-red-100 text-red-800': ticket.status === 'cancelled'
                  }"
                  class="px-3 py-1 rounded-full text-sm font-medium"
                >
                  {{ ticket.status.toUpperCase() }}
                </span>
              </div>
            </div>
          </div>

          <!-- Ticket Content -->
          <div class="p-6">
            <!-- QR Code -->
            <div v-if="ticket.status === 'valid'" class="text-center mb-6">
              <div class="bg-white p-6 rounded-lg inline-block">
                <img
                  :src="`data:image/png;base64,${ticket.qr_code}`"
                  :alt="`QR Code for ${ticket.ticket_number}`"
                  class="w-48 h-48"
                />
              </div>
              <p class="text-gray-400 text-sm mt-2">Present this QR code at the event entrance</p>
            </div>

            <!-- Ticket Details -->
            <div class="space-y-4">
              <div>
                <h3 class="text-lg font-semibold text-white mb-4">Ticket Information</h3>
                <div class="grid grid-cols-1 gap-4">
                  <div class="flex justify-between">
                    <span class="text-gray-400">Ticket Number:</span>
                    <span class="text-white font-mono">{{ ticket.ticket_number }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-400">Purchase Date:</span>
                    <span class="text-white">{{ formatDate(ticket.purchase_date) }}</span>
                  </div>
                  <div v-if="ticket.used_at" class="flex justify-between">
                    <span class="text-gray-400">Used At:</span>
                    <span class="text-white">{{ formatDate(ticket.used_at) }}</span>
                  </div>
                </div>
              </div>

              <div>
                <h3 class="text-lg font-semibold text-white mb-4">Event Details</h3>
                <div class="grid grid-cols-1 gap-4">
                  <div class="flex justify-between">
                    <span class="text-gray-400">Date & Time:</span>
                    <span class="text-white">{{ ticket.event?.event_date ? formatDate(ticket.event.event_date) : 'TBD' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-400">Location:</span>
                    <span class="text-white">{{ ticket.event?.location }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-400">Price:</span>
                    <span class="text-white">{{ (ticket.event?.price ?? 0) > 0 ? `$${ticket.event?.price}` : 'Free' }}</span>
                  </div>
                </div>
              </div>

              <div v-if="ticket.purchase">
                <h3 class="text-lg font-semibold text-white mb-4">Payment Information</h3>
                <div class="grid grid-cols-1 gap-4">
                  <div class="flex justify-between">
                    <span class="text-gray-400">Amount Paid:</span>
                    <span class="text-white">${{ ticket.purchase.amount_paid }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-400">Payment Method:</span>
                    <span class="text-white capitalize">{{ ticket.purchase.payment_method }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-400">Transaction ID:</span>
                    <span class="text-white font-mono text-sm">{{ ticket.purchase.transaction_id }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex space-x-4">
              <RouterLink
                to="/tickets"
                class="btn btn-secondary flex-1 text-center"
              >
                Back to Tickets
              </RouterLink>
              
              <button
                v-if="ticket.status === 'valid' && canCancel"
                @click="cancelTicket"
                class="btn btn-danger flex-1"
              >
                Cancel Ticket
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-12">
        <h2 class="text-2xl font-bold text-gray-400 mb-4">Ticket Not Found</h2>
        <p class="text-gray-500 mb-6">The ticket you're looking for doesn't exist or you don't have access to it.</p>
        <RouterLink to="/tickets" class="btn btn-primary">
          Back to My Tickets
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useTicketsStore, type Ticket } from '@/stores/tickets'

const route = useRoute()
const router = useRouter()
const ticketsStore = useTicketsStore()

const loading = ref(true)
const ticket = ref<Ticket | null>(null)

const canCancel = computed(() => {
  if (!ticket.value || ticket.value.status !== 'valid') return false
  
  if (!ticket.value.event?.event_date) return false
  
  const eventDate = new Date(ticket.value.event.event_date)
  const hoursUntilEvent = (eventDate.getTime() - Date.now()) / (1000 * 60 * 60)
  return hoursUntilEvent > 24
})

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const cancelTicket = async () => {
  if (!ticket.value) return
  
  if (confirm('Are you sure you want to cancel this ticket? This action cannot be undone.')) {
    const result = await ticketsStore.cancelTicket(ticket.value.id)
    if (result.success) {
      router.push('/tickets')
    }
  }
}

onMounted(async () => {
  const ticketId = route.params.id as string
  const result = await ticketsStore.fetchTicket(parseInt(ticketId))
  
  if (result.success) {
    ticket.value = result.data
  }
  
  loading.value = false
})
</script>
