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
                      <!-- Price Display -->
                      <div class="price-display text-center mb-4">
                        <div class="price-label text-muted small mb-1">Ticket Price</div>
                        <div class="price-amount h3 fw-bold text-success mb-0">
                          ${{ event.price || '0.00' }}
                        </div>
                      </div>
                      
                      <!-- Modern Purchase Button -->
                      <button
                        @click="showPaymentModal = true"
                        :disabled="purchasing"
                        class="btn-purchase-modern w-100 d-flex align-items-center justify-content-center mb-3 position-relative overflow-hidden"
                      >
                        <div class="btn-purchase-bg position-absolute top-0 start-0 w-100 h-100"></div>
                        <div class="btn-purchase-content position-relative d-flex align-items-center">
                          <div v-if="purchasing" class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                          <i v-else class="bi bi-credit-card me-2"></i>
                          {{ purchasing ? 'Processing...' : 'Purchase Ticket' }}
                        </div>
                        <div class="btn-purchase-glow position-absolute top-0 start-0 w-100 h-100"></div>
                      </button>
                      
                      <!-- Security Features -->
                      <div class="security-features">
                        <div class="d-flex align-items-center justify-content-center text-muted small mb-3">
                          <div class="d-flex align-items-center me-3">
                            <i class="bi bi-shield-check text-success me-1"></i>
                            <span>Secure</span>
                          </div>
                          <div class="d-flex align-items-center me-3">
                            <i class="bi bi-lightning-charge text-warning me-1"></i>
                            <span>Instant</span>
                          </div>
                          <div class="d-flex align-items-center">
                            <i class="bi bi-phone text-info me-1"></i>
                            <span>Bakong</span>
                          </div>
                        </div>
                        
                        <div class="trust-badges d-flex align-items-center justify-content-center">
                          <div class="trust-badge me-2">
                            <i class="bi bi-bank2 text-primary"></i>
                          </div>
                          <div class="trust-badge me-2">
                            <i class="bi bi-qr-code text-success"></i>
                          </div>
                          <div class="trust-badge">
                            <i class="bi bi-shield-fill-check text-info"></i>
                          </div>
                        </div>
                      </div>
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

    <!-- Modern Payment Experience -->
    <div
      v-if="showPaymentModal"
      class="payment-overlay position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
      style="z-index: 1060; background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px);"
      @click="closePaymentModal"
    >
      <div 
        class="payment-container position-relative w-100 h-100 overflow-auto"
        @click.stop
        style="max-width: 1200px; padding: 20px;"
      >
        <!-- Close Button -->
        <button
          @click="closePaymentModal"
          class="btn-close-modern position-absolute d-flex align-items-center justify-content-center"
          style="top: 30px; right: 30px; z-index: 10; width: 50px; height: 50px; background: rgba(255, 255, 255, 0.1); border: none; border-radius: 50%; backdrop-filter: blur(10px); transition: all 0.3s ease;"
        >
          <i class="bi bi-x-lg text-white fs-4"></i>
        </button>
        
        <!-- Payment Content -->
        <div class="payment-content">
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

<style scoped>
/* Modern Payment Button */
.btn-purchase-modern {
  padding: 16px 24px;
  border: none;
  border-radius: 16px;
  font-weight: 600;
  font-size: 1.1rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
  cursor: pointer;
}

.btn-purchase-bg {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 16px;
  transition: all 0.3s ease;
}

.btn-purchase-content {
  color: white;
  z-index: 2;
}

.btn-purchase-glow {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
  opacity: 0;
  border-radius: 16px;
  transition: opacity 0.3s ease;
}

.btn-purchase-modern:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 8px 30px rgba(16, 185, 129, 0.4);
}

.btn-purchase-modern:hover .btn-purchase-glow {
  opacity: 1;
}

.btn-purchase-modern:active {
  transform: translateY(0) scale(0.98);
}

.btn-purchase-modern:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* Price Display */
.price-display {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
  border-radius: 12px;
  padding: 16px;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.price-amount {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Security Features */
.security-features {
  background: rgba(255, 255, 255, 0.02);
  border-radius: 12px;
  padding: 12px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.trust-badge {
  width: 32px;
  height: 32px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.trust-badge:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.1);
}

/* Payment Overlay */
.payment-overlay {
  animation: fadeIn 0.3s ease;
}

.payment-container {
  animation: slideInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-close-modern:hover {
  background: rgba(255, 255, 255, 0.2) !important;
  transform: scale(1.1);
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(40px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .payment-container {
    padding: 10px !important;
  }
  
  .btn-close-modern {
    top: 15px !important;
    right: 15px !important;
    width: 40px !important;
    height: 40px !important;
  }
}
</style>
