<template>
  <div class="stripe-payment">
    <!-- Payment Form Step -->
    <div v-if="currentStep === 'form'" class="payment-form">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-credit-card me-2 text-primary"></i>
          Purchase Ticket
        </h5>
        <button @click="$emit('close')" class="btn-close btn-close-white"></button>
      </div>
      
      <div class="modal-body">
        <div class="mb-4">
          <h6 class="fw-bold text-light">{{ event.title }}</h6>
          <p class="text-muted mb-2">{{ formatDate(event.event_date) }}</p>
          <p class="text-muted mb-3">{{ event.location }}</p>
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-light">Ticket Price:</span>
            <span class="h5 mb-0 text-success fw-bold">${{ event.price }}</span>
          </div>
        </div>

        <div v-if="error" class="alert alert-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>{{ error }}
        </div>

        <form @submit.prevent="createPaymentIntent">
          <div class="mb-3">
            <label class="form-label">Amount</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input 
                v-model="amount" 
                type="number" 
                step="0.01" 
                min="0.50"
                :max="event.price"
                class="form-control" 
                required
                readonly
              >
            </div>
            <small class="form-text text-muted">
              Minimum payment: $0.50 (Stripe requirement)
            </small>
          </div>

          <div class="d-grid">
            <button 
              type="submit" 
              class="btn btn-primary"
              :disabled="processing"
            >
              <span v-if="processing">
                <div class="spinner-border spinner-border-sm me-2"></div>
                Creating Payment...
              </span>
              <span v-else>
                <i class="bi bi-credit-card me-2"></i>
                Continue to Payment
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Stripe Payment Step -->
    <div v-else-if="currentStep === 'payment'" class="payment-step">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-shield-check me-2 text-success"></i>
          Secure Payment
        </h5>
        <button @click="$emit('close')" class="btn-close btn-close-white"></button>
      </div>
      
      <div class="modal-body">
        <!-- Ticket Quantity Selector -->
        <div class="mb-4">
          <label class="form-label text-light mb-3">
            <i class="bi bi-ticket-perforated me-2"></i>
            Number of Tickets
          </label>
          <div class="quantity-selector mb-3">
            <button 
              @click="decreaseQuantity" 
              :disabled="quantity <= 1"
              class="btn btn-outline-light btn-quantity"
              type="button"
            >
              <i class="bi bi-dash"></i>
            </button>
            <input 
              v-model.number="quantity" 
              @input="updateAmount"
              type="number" 
              min="1" 
              max="10"
              class="form-control quantity-input text-center"
            >
            <button 
              @click="increaseQuantity" 
              :disabled="quantity >= 10"
              class="btn btn-outline-light btn-quantity"
              type="button"
            >
              <i class="bi bi-plus"></i>
            </button>
          </div>
          <div class="quantity-info text-center mb-3">
            <small class="text-muted">
              Price per ticket: <span class="text-success">${{ ticketPrice }}</span>
            </small>
          </div>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-light">Total Amount:</span>
            <span class="h4 mb-0 text-success fw-bold">${{ totalAmount }}</span>
          </div>
          <div class="progress mb-3" style="height: 4px;">
            <div class="progress-bar bg-success" style="width: 75%"></div>
          </div>
        </div>

        <div v-if="error" class="alert alert-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>{{ error }}
        </div>

        <!-- Stripe Elements will be mounted here -->
        <div id="payment-element" class="mb-4"></div>

        <div class="d-grid gap-2">
          <button 
            @click="confirmPayment"
            class="btn btn-success"
            :disabled="processing || !stripe || !elements"
          >
            <span v-if="processing">
              <div class="spinner-border spinner-border-sm me-2"></div>
              Processing Payment...
            </span>
            <span v-else>
              <i class="bi bi-lock me-2"></i>
              Pay ${{ amount }}
            </span>
          </button>
          
          <button @click="goBack" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
          </button>
        </div>

        <div class="mt-3">
          <small class="text-muted d-flex align-items-center">
            <i class="bi bi-shield-check me-2"></i>
            Secured by Stripe. Your payment information is encrypted and secure.
          </small>
        </div>
      </div>
    </div>

    <!-- Success Step -->
    <div v-else-if="currentStep === 'success'" class="payment-success">
      <div class="success-container">
        <!-- Animated Success Icon -->
        <div class="success-animation mb-4">
          <div class="success-checkmark">
            <div class="check-icon">
              <span class="icon-line line-tip"></span>
              <span class="icon-line line-long"></span>
              <div class="icon-circle"></div>
              <div class="icon-fix"></div>
            </div>
          </div>
        </div>

        <!-- Success Content -->
        <div class="success-content text-center">
          <h2 class="success-title text-success mb-3 animate-fade-in">
            🎉 Payment Successful!
          </h2>
          <p class="success-subtitle text-light mb-4 animate-fade-in-delay">
            Your {{ quantity > 1 ? `${quantity} tickets` : 'ticket' }} for <strong>{{ event.title }}</strong> {{ quantity > 1 ? 'have' : 'has' }} been purchased successfully!
          </p>

          <!-- Event & Transaction Details Card -->
          <div class="success-details-card animate-slide-up">
            <div class="card bg-gradient border-0 shadow-lg mb-4">
              <div class="card-body p-4">
                <div class="row align-items-center mb-3">
                  <div class="col-auto">
                    <div class="success-ticket-icon">
                      <i class="bi bi-ticket-perforated-fill text-primary fs-1"></i>
                    </div>
                  </div>
                  <div class="col">
                    <h5 class="card-title text-dark mb-1">{{ event.title }}</h5>
                    <p class="card-text text-muted mb-0">
                      <i class="bi bi-calendar-event me-1"></i>
                      {{ new Date(event.event_date).toLocaleDateString() }}
                    </p>
                  </div>
                </div>
                
                <hr class="my-3">
                
                <div class="row text-start">
                  <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Transaction ID</small>
                    <code class="text-primary">{{ transactionId }}</code>
                  </div>
                  <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Quantity</small>
                    <span class="h6 text-info mb-0">{{ quantity }} {{ quantity > 1 ? 'tickets' : 'ticket' }}</span>
                  </div>
                  <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Price per Ticket</small>
                    <span class="text-dark">${{ ticketPrice }}</span>
                  </div>
                  <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">Total Amount</small>
                    <span class="h5 text-success mb-0">${{ totalAmount }}</span>
                  </div>
                  <div class="col-md-6">
                    <small class="text-muted d-block">Payment Method</small>
                    <span class="text-dark">
                      <i class="bi bi-credit-card me-1"></i>
                      Stripe
                    </span>
                  </div>
                  <div class="col-md-6">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-success">
                      <i class="bi bi-check-circle me-1"></i>
                      Confirmed
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="success-actions animate-fade-in-delay-2">
            <div class="d-grid gap-3">
              <button @click="viewTicket" class="btn btn-primary btn-lg shadow-sm">
                <i class="bi bi-ticket-perforated me-2"></i>
                View My Ticket
              </button>
              <div class="row g-2">
                <div class="col-6">
                  <button @click="$emit('close')" class="btn btn-outline-light w-100">
                    <i class="bi bi-house me-1"></i>
                    Continue
                  </button>
                </div>
                <div class="col-6">
                  <button @click="downloadReceipt" class="btn btn-outline-success w-100">
                    <i class="bi bi-download me-1"></i>
                    Receipt
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Thank You Message -->
          <div class="success-footer mt-4 animate-fade-in-delay-3">
            <p class="text-muted small mb-0">
              <i class="bi bi-heart-fill text-danger me-1"></i>
              Thank you for choosing EasyPass! We hope you enjoy the event.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Step -->
    <div v-else-if="currentStep === 'error'" class="payment-error">
      <div class="modal-header">
        <h5 class="modal-title text-danger">
          <i class="bi bi-x-circle me-2"></i>
          Payment Failed
        </h5>
        <button @click="$emit('close')" class="btn-close btn-close-white"></button>
      </div>
      
      <div class="modal-body text-center">
        <div class="mb-4">
          <div class="error-icon mb-3">
            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
          </div>
          <h4 class="text-light mb-3">Payment Failed</h4>
          <div class="alert alert-danger">
            {{ error }}
          </div>
        </div>

        <div class="d-grid gap-2">
          <button @click="goBack" class="btn btn-primary">
            <i class="bi bi-arrow-left me-2"></i>
            Try Again
          </button>
          <button @click="$emit('close')" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-2"></i>
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { loadStripe, Stripe, StripeElements } from '@stripe/stripe-js'

// Props
interface Props {
  event: {
    id: number
    title: string
    event_date: string
    location: string
    price: number
  }
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  close: []
  paymentSuccess: [data: any]
}>()

// Router
const router = useRouter()

// State
const currentStep = ref<'form' | 'payment' | 'success' | 'error'>('form')
const processing = ref(false)
const error = ref('')
const quantity = ref(1)
const ticketPrice = ref(props.event.price)
const amount = ref(props.event.price)
const transactionId = ref('')
const clientSecret = ref('')

// Computed
const totalAmount = computed(() => {
  return (quantity.value * ticketPrice.value).toFixed(2)
})

// Stripe
const stripe = ref<Stripe | null>(null)
const elements = ref<StripeElements | null>(null)

// Initialize Stripe
onMounted(async () => {
  try {
    const stripeKey = import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY
    if (!stripeKey) {
      throw new Error('Stripe publishable key not found')
    }
    
    stripe.value = await loadStripe(stripeKey)
    if (!stripe.value) {
      throw new Error('Failed to load Stripe')
    }
  } catch (err: any) {
    error.value = 'Failed to initialize payment system: ' + err.message
    console.error('Stripe initialization error:', err)
  }
})

// Methods
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

const createPaymentIntent = async () => {
  if (amount.value < 0.50) {
    error.value = 'Minimum payment amount is $0.50'
    return
  }

  processing.value = true
  error.value = ''

  try {
    const response = await axios.post('/api/payments/create-intent', {
      event_id: props.event.id,
      amount: amount.value,
      quantity: quantity.value
    })

    if (response.data.status === 'success') {
      clientSecret.value = response.data.data.client_secret
      transactionId.value = response.data.data.transaction_id
      
      // Move to payment step
      currentStep.value = 'payment'
      
      // Initialize Stripe Elements
      await nextTick()
      await initializeStripeElements()
    } else {
      throw new Error(response.data.message || 'Failed to create payment intent')
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || err.message || 'Failed to create payment'
    console.error('Payment intent creation error:', err)
  } finally {
    processing.value = false
  }
}

const confirmWithBackend = async () => {
  try {
    const response = await axios.post('/api/payments/confirm', {
      transaction_id: transactionId.value
    })
    
    if (response.data.status === 'success') {
      currentStep.value = 'success'
      // Emit success event to parent component
      emit('paymentSuccess', response.data.data)
    } else {
      throw new Error(response.data.message || 'Failed to confirm payment')
    }
  } catch (error: any) {
    console.error('Backend confirmation error:', error)
    throw new Error(error.response?.data?.message || 'Failed to confirm payment with server')
  }
}

const initializeStripeElements = async () => {
  if (!stripe.value || !clientSecret.value) return

  const appearance = {
    theme: 'night' as const,
    variables: {
      colorPrimary: '#0570de',
      colorBackground: '#1a1a1a',
      colorText: '#ffffff',
      colorDanger: '#df1b41',
      fontFamily: 'Ideal Sans, system-ui, sans-serif',
      spacingUnit: '2px',
      borderRadius: '4px',
    },
  }

  elements.value = stripe.value.elements({ 
    appearance, 
    clientSecret: clientSecret.value 
  })

  const paymentElementOptions = {
    layout: 'tabs' as const,
  }

  const paymentElement = elements.value.create('payment', paymentElementOptions)
  paymentElement.mount('#payment-element')
}

const confirmPayment = async () => {
  if (!stripe.value || !elements.value) {
    error.value = 'Payment system not ready'
    return
  }

  processing.value = true
  error.value = ''

  try {
    const { error: stripeError } = await stripe.value.confirmPayment({
      elements: elements.value,
      confirmParams: {
        return_url: `${window.location.origin}/payment/success`,
      },
      redirect: 'if_required'
    })

    if (stripeError) {
      throw new Error(stripeError.message || 'Payment failed')
    }

    // Payment succeeded, now confirm with backend to create ticket
    await confirmWithBackend()
  } catch (err: any) {
    error.value = err.message || 'Payment failed'
    currentStep.value = 'error'
    console.error('Payment confirmation error:', err)
  } finally {
    processing.value = false
  }
}

const goBack = () => {
  error.value = ''
  if (currentStep.value === 'payment') {
    currentStep.value = 'form'
  } else if (currentStep.value === 'error') {
    currentStep.value = 'payment'
  }
}

const viewTicket = () => {
  emit('close')
  router.push('/tickets')
}

const downloadReceipt = () => {
  // TODO: Implement receipt download functionality
  alert('Receipt download feature coming soon!')
}

// Quantity Management Functions
const increaseQuantity = () => {
  if (quantity.value < 10) {
    quantity.value++
    updateAmount()
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
    updateAmount()
  }
}

const updateAmount = () => {
  amount.value = parseFloat(totalAmount.value)
}
</script>

<style scoped>
.stripe-payment {
  color: #ffffff;
}

.modal-header {
  background: rgba(255, 255, 255, 0.05);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-body {
  background: rgba(0, 0, 0, 0.3);
}

.card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.form-control {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #ffffff;
}

.form-control:focus {
  background: rgba(255, 255, 255, 0.15);
  border-color: #0570de;
  box-shadow: 0 0 0 0.2rem rgba(5, 112, 222, 0.25);
  color: #ffffff;
}

.input-group-text {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #ffffff;
}

.alert {
  border: none;
}

.progress {
  background: rgba(255, 255, 255, 0.1);
}

#payment-element {
  padding: 1rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 0.375rem;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.success-icon, .error-icon {
  animation: bounceIn 0.6s ease-out;
}

@keyframes bounceIn {
  0% {
    transform: scale(0.3);
    opacity: 0;
  }
  50% {
    transform: scale(1.05);
  }
  70% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.btn:disabled {
  opacity: 0.6;
}

/* Quantity Selector Styles */
.quantity-selector {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  max-width: 200px;
  margin: 0 auto;
}

.btn-quantity {
  width: 50px;
  height: 50px;
  border-radius: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  transition: all 0.3s ease;
}

.btn-quantity:first-child {
  border-top-left-radius: 0.375rem;
  border-bottom-left-radius: 0.375rem;
  border-right: none;
}

.btn-quantity:last-child {
  border-top-right-radius: 0.375rem;
  border-bottom-right-radius: 0.375rem;
  border-left: none;
}

.quantity-input {
  width: 100px;
  height: 50px;
  border-radius: 0;
  border-left: none;
  border-right: none;
  background: rgba(255, 255, 255, 0.1);
  color: white;
  font-weight: bold;
  font-size: 1.1rem;
}

.quantity-input:focus {
  background: rgba(255, 255, 255, 0.15);
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.quantity-info {
  font-size: 0.9rem;
}

/* Success Animation Styles */
.payment-success {
  background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(25, 135, 84, 0.1));
  border-radius: 20px;
  padding: 2rem;
  min-height: 600px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.success-container {
  max-width: 500px;
  width: 100%;
}

/* Animated Checkmark */
.success-animation {
  display: flex;
  justify-content: center;
  align-items: center;
}

.success-checkmark {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  display: block;
  stroke-width: 3;
  stroke: #28a745;
  stroke-miterlimit: 10;
  box-shadow: inset 0px 0px 0px #28a745;
  animation: fill 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both;
  position: relative;
}

.success-checkmark .check-icon {
  width: 120px;
  height: 120px;
  position: absolute;
  border-radius: 50%;
  box-sizing: border-box;
  border: 3px solid #28a745;
  background: rgba(40, 167, 69, 0.1);
  backdrop-filter: blur(10px);
}

.check-icon .icon-line {
  height: 3px;
  background-color: #28a745;
  display: block;
  border-radius: 2px;
  position: absolute;
  z-index: 10;
}

.check-icon .line-tip {
  top: 46px;
  left: 14px;
  width: 25px;
  transform: rotate(45deg);
  animation: icon-line-tip 0.75s;
}

.check-icon .line-long {
  top: 38px;
  right: 8px;
  width: 47px;
  transform: rotate(-45deg);
  animation: icon-line-long 0.75s;
}

.check-icon .icon-circle {
  top: -3px;
  left: -3px;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  position: absolute;
  border: 3px solid rgba(40, 167, 69, 0.2);
  animation: icon-circle 0.6s ease-in-out;
}

.check-icon .icon-fix {
  top: 8px;
  width: 5px;
  left: 26px;
  z-index: 1;
  height: 85px;
  position: absolute;
  transform: rotate(-45deg);
  background-color: rgba(0, 0, 0, 0.8);
}

/* Success Content Animations */
.animate-fade-in {
  animation: fadeIn 0.8s ease-out;
}

.animate-fade-in-delay {
  animation: fadeIn 0.8s ease-out 0.3s both;
}

.animate-fade-in-delay-2 {
  animation: fadeIn 0.8s ease-out 0.6s both;
}

.animate-fade-in-delay-3 {
  animation: fadeIn 0.8s ease-out 0.9s both;
}

.animate-slide-up {
  animation: slideUp 0.8s ease-out 0.4s both;
}

/* Success Card Styling */
.success-details-card .card {
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.success-ticket-icon {
  animation: bounce 1s ease-in-out 0.5s both;
}

/* Button Enhancements */
.btn-primary.btn-lg {
  background: linear-gradient(45deg, #007bff, #0056b3);
  border: none;
  border-radius: 15px;
  padding: 15px 30px;
  font-weight: 600;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.btn-primary.btn-lg::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.btn-primary.btn-lg:hover::before {
  left: 100%;
}

.btn-primary.btn-lg:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
}

/* Keyframe Animations */
@keyframes fill {
  100% {
    box-shadow: inset 0px 0px 0px 60px #28a745;
  }
}

@keyframes scale {
  0%, 100% {
    transform: none;
  }
  50% {
    transform: scale3d(1.1, 1.1, 1);
  }
}

@keyframes icon-line-tip {
  0% {
    width: 0;
    left: 1px;
    top: 19px;
  }
  54% {
    width: 0;
    left: 1px;
    top: 19px;
  }
  70% {
    width: 50px;
    left: -8px;
    top: 37px;
  }
  84% {
    width: 17px;
    left: 21px;
    top: 48px;
  }
  100% {
    width: 25px;
    left: 14px;
    top: 45px;
  }
}

@keyframes icon-line-long {
  0% {
    width: 0;
    right: 46px;
    top: 54px;
  }
  65% {
    width: 0;
    right: 46px;
    top: 54px;
  }
  84% {
    width: 55px;
    right: 0px;
    top: 35px;
  }
  100% {
    width: 47px;
    right: 8px;
    top: 38px;
  }
}

@keyframes icon-circle {
  0% {
    opacity: 1;
    transform: scale(0);
  }
  100% {
    opacity: 0;
    transform: scale(1.2);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translate3d(0, 0, 0);
  }
  40%, 43% {
    transform: translate3d(0, -15px, 0);
  }
  70% {
    transform: translate3d(0, -7px, 0);
  }
  90% {
    transform: translate3d(0, -2px, 0);
  }
}
</style>
