<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container" style="max-width: 1200px;">
      <div v-if="loading" class="d-flex justify-content-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      
      <div v-else-if="event">
        <!-- Event Header -->
        <div class="card overflow-hidden mb-4">
          <div class="position-relative overflow-hidden" style="height: 400px;">
            <img
              v-if="event.image_url"
              :src="event.image_url"
              :alt="event.title"
              class="w-100 h-100 object-fit-cover"
            />
            <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary">
              <i class="bi bi-calendar-event display-1 text-muted"></i>
            </div>
          </div>
          
          <div class="card-body">
            <div class="row">
              <div class="col-lg-8">
                <h1 class="display-4 fw-bold text-light mb-4 professional-title">{{ event.title }}</h1>
                <p class="lead text-muted mb-4">{{ event.description }}</p>
                
                <div class="row g-4 mb-4">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <div class="d-flex align-items-center text-muted mb-2">
                        <i class="bi bi-calendar-event me-3 text-primary fs-5"></i>
                        <span>{{ formatDate(event.event_date) }}</span>
                      </div>
                      
                      <div class="d-flex align-items-center text-muted mb-2">
                        <i class="bi bi-geo-alt me-3 text-success fs-5"></i>
                        <span>{{ event.location }}</span>
                      </div>
                      
                      <div class="d-flex align-items-center text-muted">
                        <i class="bi bi-bookmark me-3 text-info fs-5"></i>
                        <span>{{ event.category }}</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="mb-3">
                      <div class="d-flex align-items-center text-muted mb-2">
                        <i class="bi bi-cash-coin me-3 text-success fs-5"></i>
                        <span class="h4 fw-bold text-success mb-0">
                          {{ event.price > 0 ? `$${event.price}` : 'Free' }}
                        </span>
                      </div>
                      
                      <div class="d-flex align-items-center text-muted">
                        <i class="bi bi-people me-3 text-warning fs-5"></i>
                        <span>{{ event.current_attendees }}/{{ event.max_attendees }} attendees</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <h3 class="h5 fw-bold text-light mb-4 professional-title">Get Your Ticket</h3>
                    
                    <div v-if="!authStore.isAuthenticated">
                      <p class="text-muted small mb-3">Please sign in to purchase tickets</p>
                      <RouterLink
                        to="/auth/login"
                        class="btn btn-primary w-100 d-flex align-items-center justify-content-center"
                      >
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Sign In to Purchase
                      </RouterLink>
                    </div>
                    
                    <div v-else-if="event.status !== 'active'" class="text-center">
                      <p class="text-danger">This event is no longer available</p>
                    </div>
                    
                    <div v-else-if="event.current_attendees >= event.max_attendees" class="text-center">
                      <p class="text-warning">This event is sold out</p>
                    </div>
                    
                    <div v-else>
                      <button
                        @click="showPaymentModal = true"
                        :disabled="purchasing"
                        class="btn btn-primary w-100 d-flex align-items-center justify-content-center mb-3"
                      >
                        <div v-if="purchasing" class="spinner-border spinner-border-sm me-2" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                        <i v-else class="bi bi-cart-plus me-2"></i>
                        {{ purchasing ? 'Processing...' : 'Purchase Ticket' }}
                      </button>
                      
                      <p class="text-muted small text-center mb-0">
                        <i class="bi bi-shield-check me-1"></i>
                        Secure Bakong payment
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-5">
        <i class="bi bi-calendar-x display-1 text-muted mb-4"></i>
        <h2 class="h4 fw-bold text-muted mb-3 professional-title">Event Not Found</h2>
        <p class="text-muted mb-4">The event you're looking for doesn't exist or has been removed.</p>
        <RouterLink to="/events" class="btn btn-primary d-inline-flex align-items-center">
          <i class="bi bi-calendar-event me-2"></i>
          Browse Events
        </RouterLink>
      </div>
    </div>

    <!-- Payment Modal -->
    <div
      v-if="showPaymentModal"
      class="modal fade show d-block"
      style="background-color: rgba(0, 0, 0, 0.5);"
      tabindex="-1"
    >
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Purchase Ticket - {{ event?.title }}</h5>
            <button
              type="button"
              class="btn-close"
              @click="closePaymentModal"
            ></button>
          </div>
          <div class="modal-body">
            <BakongPayment
              v-if="event"
              :event="event"
              @payment-success="handlePaymentSuccess"
              @payment-cancelled="closePaymentModal"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useEventsStore, type Event } from '@/stores/events'
import { useTicketsStore } from '@/stores/tickets'
import BakongPayment from '@/components/payment/BakongPayment.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const eventsStore = useEventsStore()
const ticketsStore = useTicketsStore()

const loading = ref(true)
const purchasing = ref(false)
const event = ref<Event | null>(null)
const showPaymentModal = ref(false)

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

const handlePaymentSuccess = (ticket: any) => {
  showPaymentModal.value = false
  router.push('/tickets')
}

const closePaymentModal = () => {
  showPaymentModal.value = false
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
