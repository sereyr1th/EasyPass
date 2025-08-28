<template>
  <div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="flex justify-center py-12">
        <div class="spinner"></div>
      </div>
      
      <div v-else-if="event" class="space-y-8">
        <!-- Event Header -->
        <div class="card overflow-hidden">
          <div class="aspect-video bg-gray-700 relative">
            <img
              v-if="event.image_url"
              :src="event.image_url"
              :alt="event.title"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center">
              <svg class="w-24 h-24 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
          
          <div class="p-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
              <div class="flex-1">
                <h1 class="text-4xl font-bold text-white mb-4">{{ event.title }}</h1>
                <p class="text-gray-300 text-lg mb-6">{{ event.description }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div class="space-y-4">
                    <div class="flex items-center text-gray-300">
                      <svg class="w-5 h-5 mr-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                      </svg>
                      <span>{{ formatDate(event.event_date) }}</span>
                    </div>
                    
                    <div class="flex items-center text-gray-300">
                      <svg class="w-5 h-5 mr-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                      </svg>
                      <span>{{ event.location }}</span>
                    </div>
                    
                    <div class="flex items-center text-gray-300">
                      <svg class="w-5 h-5 mr-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ event.category }}</span>
                    </div>
                  </div>
                  
                  <div class="space-y-4">
                    <div class="flex items-center text-gray-300">
                      <svg class="w-5 h-5 mr-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                      </svg>
                      <span class="text-2xl font-bold text-green-400">
                        {{ event.price > 0 ? `$${event.price}` : 'Free' }}
                      </span>
                    </div>
                    
                    <div class="flex items-center text-gray-300">
                      <svg class="w-5 h-5 mr-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                      </svg>
                      <span>{{ event.current_attendees }}/{{ event.max_attendees }} attendees</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="lg:ml-8 mt-6 lg:mt-0">
                <div class="card p-6 lg:w-80">
                  <h3 class="text-lg font-semibold text-white mb-4">Get Your Ticket</h3>
                  
                  <div v-if="!authStore.isAuthenticated" class="space-y-4">
                    <p class="text-gray-400 text-sm">Please sign in to purchase tickets</p>
                    <RouterLink
                      to="/auth/login"
                      class="btn btn-primary w-full text-center"
                    >
                      Sign In to Purchase
                    </RouterLink>
                  </div>
                  
                  <div v-else-if="event.status !== 'active'" class="text-center">
                    <p class="text-red-400">This event is no longer available</p>
                  </div>
                  
                  <div v-else-if="event.current_attendees >= event.max_attendees" class="text-center">
                    <p class="text-yellow-400">This event is sold out</p>
                  </div>
                  
                  <div v-else class="space-y-4">
                    <button
                      @click="purchaseTicket"
                      :disabled="purchasing"
                      class="btn btn-primary w-full flex items-center justify-center"
                    >
                      <div v-if="purchasing" class="spinner mr-2"></div>
                      <svg v-else class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                      </svg>
                      {{ purchasing ? 'Processing...' : 'Purchase Ticket' }}
                    </button>
                    
                    <p class="text-xs text-gray-400 text-center">
                      Secure payment processing
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-12">
        <h2 class="text-2xl font-bold text-gray-400 mb-4">Event Not Found</h2>
        <p class="text-gray-500 mb-6">The event you're looking for doesn't exist or has been removed.</p>
        <RouterLink to="/events" class="btn btn-primary">
          Browse Events
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useEventsStore } from '@/stores/events'
import { useTicketsStore } from '@/stores/tickets'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const eventsStore = useEventsStore()
const ticketsStore = useTicketsStore()

const loading = ref(true)
const purchasing = ref(false)
const event = ref(null)

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

const purchaseTicket = async () => {
  purchasing.value = true
  
  const result = await ticketsStore.purchaseTicket(event.value.id)
  
  if (result.success) {
    router.push('/tickets')
  }
  
  purchasing.value = false
}

onMounted(async () => {
  const eventId = route.params.id as string
  const result = await eventsStore.fetchEvent(parseInt(eventId))
  
  if (result.success) {
    event.value = result.data
  }
  
  loading.value = false
})
</script>
