<template>
  <div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <QrCodeIcon class="w-16 h-16 text-green-500 mx-auto mb-4" />
        <h1 class="text-4xl font-bold text-white mb-4">Validate Ticket</h1>
        <p class="text-gray-300 text-lg">
          Scan QR code or enter ticket number to validate entry
        </p>
      </div>

      <!-- Validation Form -->
      <div class="card p-8 mb-8">
        <form @submit.prevent="validateTicket" class="space-y-6">
          <!-- Error Message -->
          <div v-if="ticketsStore.error" class="bg-red-900/50 border border-red-700 rounded-lg p-4">
            <div class="flex">
              <ExclamationTriangleIcon class="w-5 h-5 text-red-400 mr-2 flex-shrink-0 mt-0.5" />
              <p class="text-red-300 text-sm">{{ ticketsStore.error }}</p>
            </div>
          </div>

          <!-- Ticket Number Input -->
          <div>
            <label for="ticketNumber" class="form-label">Ticket Number</label>
            <div class="relative">
              <TicketIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <input
                id="ticketNumber"
                v-model="ticketNumber"
                type="text"
                placeholder="Enter ticket number (e.g., EP-ABC123)"
                class="form-input pl-10 text-center font-mono"
                required
                :disabled="ticketsStore.loading"
              />
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="ticketsStore.loading || !ticketNumber.trim()"
            class="btn btn-primary w-full flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <div v-if="ticketsStore.loading" class="spinner mr-2"></div>
            <CheckCircleIcon v-else class="w-5 h-5 mr-2" />
            {{ ticketsStore.loading ? 'Validating...' : 'Validate Ticket' }}
          </button>
        </form>

        <!-- QR Code Scanner Button -->
        <div class="mt-6 pt-6 border-t border-gray-700">
          <button
            @click="openQRScanner"
            class="btn btn-outline w-full flex justify-center items-center"
          >
            <CameraIcon class="w-5 h-5 mr-2" />
            Scan QR Code
          </button>
        </div>
      </div>

      <!-- Validation Result -->
      <div v-if="ticketsStore.validationResult" class="space-y-6">
        <!-- Success Result -->
        <div v-if="validationSuccess" class="card border-green-700 bg-green-900/20">
          <div class="card-header">
            <div class="flex items-center">
              <CheckCircleIcon class="w-8 h-8 text-green-400 mr-3" />
              <div>
                <h3 class="text-xl font-semibold text-white">Ticket Valid!</h3>
                <p class="text-green-300">Entry approved</p>
              </div>
            </div>
          </div>
          
          <div class="card-body space-y-4">
            <!-- Event Info -->
            <div>
              <h4 class="font-medium text-white mb-2">Event Details</h4>
              <div class="bg-gray-800 rounded-lg p-4">
                <h5 class="text-lg font-semibold text-white">{{ ticketsStore.validationResult.event.title }}</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 text-sm text-gray-300">
                  <div>
                    <strong>Date:</strong> {{ formatDate(ticketsStore.validationResult.event.event_date) }}
                  </div>
                  <div>
                    <strong>Location:</strong> {{ ticketsStore.validationResult.event.location }}
                  </div>
                  <div>
                    <strong>Category:</strong> {{ ticketsStore.validationResult.event.category }}
                  </div>
                  <div>
                    <strong>Price:</strong> ${{ ticketsStore.validationResult.event.price }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Attendee Info -->
            <div>
              <h4 class="font-medium text-white mb-2">Attendee Information</h4>
              <div class="bg-gray-800 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-300">
                  <div>
                    <strong>Name:</strong> {{ ticketsStore.validationResult.user.name }}
                  </div>
                  <div>
                    <strong>Email:</strong> {{ ticketsStore.validationResult.user.email }}
                  </div>
                  <div>
                    <strong>Ticket Number:</strong> {{ ticketsStore.validationResult.ticket.ticket_number }}
                  </div>
                  <div>
                    <strong>Validated At:</strong> {{ formatDate(ticketsStore.validationResult.validated_at) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Error Result -->
        <div v-else class="card border-red-700 bg-red-900/20">
          <div class="card-header">
            <div class="flex items-center">
              <XCircleIcon class="w-8 h-8 text-red-400 mr-3" />
              <div>
                <h3 class="text-xl font-semibold text-white">Validation Failed</h3>
                <p class="text-red-300">Entry denied</p>
              </div>
            </div>
          </div>
          
          <div class="card-body">
            <p class="text-gray-300">
              Please check the ticket number and try again, or contact support if you believe this is an error.
            </p>
          </div>
        </div>

        <!-- Clear Button -->
        <div class="text-center">
          <button
            @click="clearValidation"
            class="btn btn-secondary"
          >
            Validate Another Ticket
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useTicketsStore } from '@/stores/tickets'
import {
  QrCodeIcon,
  TicketIcon,
  CheckCircleIcon,
  XCircleIcon,
  CameraIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const ticketsStore = useTicketsStore()
const ticketNumber = ref('')

const validationSuccess = computed(() => {
  return ticketsStore.validationResult && !ticketsStore.error
})

const validateTicket = async () => {
  if (!ticketNumber.value.trim()) return
  
  await ticketsStore.validateTicket(ticketNumber.value.trim())
}

const openQRScanner = () => {
  // In a real implementation, this would open a QR code scanner
  // For now, we'll show an alert
  alert('QR Code scanner would be implemented here using a library like vue-qr-reader')
}

const clearValidation = () => {
  ticketsStore.clearValidationResult()
  ticketNumber.value = ''
}

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
</script>
