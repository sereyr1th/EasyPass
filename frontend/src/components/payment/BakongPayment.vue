<template>
  <div class="modern-payment-experience">
    <!-- Enhanced Progress Indicator -->
    <div class="progress-section mb-5">
      <div class="progress-container d-flex align-items-center justify-content-between position-relative">
        <!-- Step 1: Payment Details -->
        <div class="progress-step d-flex flex-column align-items-center">
          <div :class="['step-circle d-flex align-items-center justify-content-center', paymentStep === 'form' ? 'step-active' : (paymentStep === 'processing' || paymentStep === 'success' || paymentStep === 'error') ? 'step-completed' : 'step-pending']">
            <i v-if="paymentStep === 'processing' || paymentStep === 'success' || paymentStep === 'error'" class="bi bi-check-lg"></i>
            <span v-else>1</span>
          </div>
          <span class="step-label mt-2 small fw-medium">Payment Details</span>
        </div>
        
        <!-- Progress Line 1 -->
        <div class="progress-line flex-grow-1 mx-3">
          <div :class="['progress-fill', (paymentStep === 'processing' || paymentStep === 'success' || paymentStep === 'error') ? 'progress-completed' : '']"></div>
        </div>
        
                 <!-- Step 2: Process & Pay -->
         <div class="progress-step d-flex flex-column align-items-center">
           <div :class="['step-circle d-flex align-items-center justify-content-center', paymentStep === 'processing' ? 'step-active' : paymentStep === 'success' ? 'step-completed' : 'step-pending']">
             <i v-if="paymentStep === 'success'" class="bi bi-check-lg"></i>
            <span v-else>2</span>
          </div>
           <span class="step-label mt-2 small fw-medium">Process & Pay</span>
        </div>
        
        <!-- Progress Line 2 -->
        <div class="progress-line flex-grow-1 mx-3">
          <div :class="['progress-fill', paymentStep === 'success' ? 'progress-completed' : '']"></div>
        </div>
        
        <!-- Step 3: Complete -->
        <div class="progress-step d-flex flex-column align-items-center">
          <div :class="['step-circle d-flex align-items-center justify-content-center', paymentStep === 'success' ? 'step-completed' : 'step-pending']">
            <i v-if="paymentStep === 'success'" class="bi bi-check-lg"></i>
            <span v-else>3</span>
          </div>
          <span class="step-label mt-2 small fw-medium">Complete</span>
        </div>
      </div>
    </div>

         <!-- Authentication Required Message -->
     <div v-if="!authStore.isAuthenticated" class="auth-required-modern">
       <div class="auth-required-card">
         <div class="auth-required-header text-center mb-4">
           <div class="auth-required-icon mb-3">
             <i class="bi bi-person-lock"></i>
            </div>
           <h2 class="auth-required-title mb-3">Authentication Required</h2>
           <p class="auth-required-subtitle">Please sign in to purchase tickets for this event</p>
         </div>

         <div class="auth-required-actions">
           <div class="row g-3">
             <div class="col-md-6">
               <a href="/auth/login" class="btn-auth-login w-100 position-relative overflow-hidden text-decoration-none d-block">
                 <div class="btn-auth-bg position-absolute top-0 start-0 w-100 h-100"></div>
                 <div class="btn-auth-content position-relative d-flex align-items-center justify-content-center">
                   <i class="bi bi-box-arrow-in-right me-2"></i>
                   Sign In
                 </div>
               </a>
             </div>
             <div class="col-md-6">
               <a href="/auth/register" class="btn-auth-register w-100 position-relative overflow-hidden text-decoration-none d-block">
                 <div class="btn-register-bg position-absolute top-0 start-0 w-100 h-100"></div>
                 <div class="btn-register-content position-relative d-flex align-items-center justify-content-center">
                   <i class="bi bi-person-plus me-2"></i>
                   Create Account
                 </div>
               </a>
             </div>
           </div>
         </div>
          </div>
        </div>

     <!-- Step 1: Enhanced Payment Form -->
     <div v-else-if="paymentStep === 'form'" class="payment-form-modern">
      <div class="payment-card">
        <!-- Modern Header -->
        <div class="payment-header text-center mb-5">
          <div class="payment-icon-container mb-4">
            <div class="payment-icon">
              <i class="bi bi-shield-lock"></i>
                </div>
            <div class="payment-icon-glow"></div>
                </div>
          <h2 class="payment-title mb-3">Secure Payment</h2>
          <p class="payment-subtitle">Complete your ticket purchase with Bakong</p>
              </div>

        <!-- Enhanced Form Content -->
        <div class="form-content">
          <form @submit.prevent="initiatePayment">
            <!-- Modern Event Summary -->
            <div class="event-summary-card mb-5">
              <div class="d-flex align-items-center mb-4">
                <div class="summary-icon me-3">
                  <i class="bi bi-ticket-perforated"></i>
                </div>
                <h3 class="summary-title mb-0">Ticket Summary</h3>
            </div>

              <div class="row align-items-center">
                <div class="col-8">
                  <h4 class="event-title mb-2">{{ event?.title }}</h4>
                  <div class="event-details">
                    <span class="event-category">{{ event?.category }}</span>
                    <span class="event-separator">•</span>
                    <span class="event-location">{{ event?.location }}</span>
                  </div>
                </div>
                                 <div class="col-4 text-end">
                   <div class="price-container">
                     <div class="price-amount">${{ event?.price || '0.00' }}</div>
                     <div class="price-label">Total Amount</div>
                   </div>
                </div>
              </div>
            </div>

            <!-- KHQR Payment Method -->
            <div class="payment-method-selection mb-5">
              <h4 class="payment-method-title mb-4">Pay with KHQR/Bakong</h4>
              <div class="khqr-payment-card method-active">
                <div class="method-icon">
                  <i class="bi bi-qr-code"></i>
                </div>
                <div class="method-info">
                  <div class="method-name">KHQR Payment</div>
                  <div class="method-description">Scan QR code with any Cambodian banking app</div>
                </div>
                <div class="method-badge khqr-badge">1% FEE ONLY</div>
              </div>
              
              <!-- Supported Banks -->
              <div class="supported-banks mt-3">
                <div class="banks-label">Supported by all major banks:</div>
                <div class="banks-icons">
                  <div class="bank-icon">ABA</div>
                  <div class="bank-icon">ACLEDA</div>
                  <div class="bank-icon">WING</div>
                  <div class="bank-icon">CANADIA</div>
                  <div class="bank-icon">PPB</div>
                </div>
              </div>
            </div>

            <!-- KHQR Payment Form -->
            <div class="payment-fields">
              <div class="row g-4">
                <div class="col-md-6">
                  <div class="form-field">
                    <label for="phone_number" class="form-label">
                      <i class="bi bi-phone field-icon"></i>
                      Phone Number
                    </label>
                    <div class="input-container">
                      <input
                        id="phone_number"
                        v-model="paymentForm.phone_number"
                        type="tel"
                        required
                        class="form-control-modern"
                        placeholder="+855 12 345 678"
                      />
                      <div class="input-focus-border"></div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-field">
                    <label for="customer_name" class="form-label">
                      <i class="bi bi-person field-icon"></i>
                      Full Name
                    </label>
                    <div class="input-container">
                      <input
                        id="customer_name"
                        v-model="paymentForm.customer_name"
                        type="text"
                        required
                        class="form-control-modern"
                        placeholder="Your full name"
                      />
                      <div class="input-focus-border"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Security Notice -->
            <div class="security-notice mb-4">
              <div class="d-flex align-items-center">
                <div class="security-icon me-3">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div class="security-text">
                  <div class="security-title">Secure Payment</div>
                  <div class="security-subtitle">Your transaction is protected by advanced encryption</div>
                </div>
              </div>
              </div>
              
            <!-- Modern Submit Button -->
            <div class="submit-section">
              <button
                type="submit"
                :disabled="loading"
                class="btn-submit-modern w-100 position-relative overflow-hidden"
              >
                <div class="btn-submit-bg position-absolute top-0 start-0 w-100 h-100"></div>
                <div class="btn-submit-content position-relative d-flex align-items-center justify-content-center">
                  <div v-if="loading" class="spinner-border spinner-border-sm me-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                  <i v-else class="bi bi-qr-code me-3"></i>
                  {{ loading ? 'Generating KHQR...' : 'Generate KHQR Payment' }}
                </div>
                <div class="btn-submit-glow position-absolute top-0 start-0 w-100 h-100"></div>
              </button>
            </div>
          </form>
        </div>
      </div>
              </div>
              
    <!-- Step 2: Modern Processing Payment -->
    <div v-else-if="paymentStep === 'processing'" class="processing-payment-modern">
      <div class="processing-payment-card">
        <!-- Enhanced Processing Header -->
        <div class="processing-header text-center mb-5">
          <div class="processing-icon-container mb-4">
            <div class="processing-icon">
              <i class="bi bi-credit-card-2-front"></i>
                </div>
            <div class="processing-spinner"></div>
          </div>
          <h2 class="processing-title mb-3">Processing Payment</h2>
          <p class="processing-subtitle mb-4">Please wait while we process your {{ getPaymentMethodName() }} payment</p>
              </div>
              
        <!-- Processing Steps -->
        <div class="processing-steps mb-5">
          <div class="row g-3">
            <div class="col-6 col-lg-3">
              <div class="processing-step-item text-center">
                <div class="step-icon-modern step-blue">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div class="step-text">Validating</div>
              </div>
            </div>
            
            <div class="col-6 col-lg-3">
              <div class="processing-step-item text-center">
                <div class="step-icon-modern step-purple">
                  <i class="bi bi-credit-card"></i>
                </div>
                <div class="step-text">Processing</div>
            </div>
          </div>

            <div class="col-6 col-lg-3">
              <div class="processing-step-item text-center">
                <div class="step-icon-modern step-green">
                  <i class="bi bi-check-circle"></i>
                </div>
                <div class="step-text">Confirming</div>
              </div>
          </div>

            <div class="col-6 col-lg-3">
              <div class="processing-step-item text-center">
                <div class="step-icon-modern step-orange">
                  <i class="bi bi-ticket-perforated"></i>
            </div>
                <div class="step-text">Generating</div>
            </div>
            </div>
          </div>
        </div>

        <!-- Transaction Info Card -->
        <div class="transaction-info-card text-center mb-4">
          <div class="transaction-label">Transaction ID</div>
          <div class="transaction-id">{{ transactionId }}</div>
                     <div class="amount-info mt-3">
             <span class="amount-label">Amount: </span>
             <span class="amount-value">${{ event?.price || '0.00' }}</span>
      </div>
    </div>

        <!-- Processing Message -->
        <div class="processing-message text-center">
          <p class="processing-text">
            <i class="bi bi-hourglass-split me-2"></i>
            This usually takes just a few seconds...
          </p>
        </div>
      </div>
          </div>
          
    <!-- Step 3: Modern Success State -->
    <div v-else-if="paymentStep === 'success'" class="success-payment-modern">
      <div class="success-payment-card">
        <!-- Enhanced Success Header -->
        <div class="success-header text-center mb-5">
          <div class="success-icon-container mb-4">
            <div class="success-icon">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="success-confetti">
              <div class="confetti-piece confetti-1"></div>
              <div class="confetti-piece confetti-2"></div>
              <div class="confetti-piece confetti-3"></div>
              <div class="confetti-piece confetti-4"></div>
              <div class="confetti-piece confetti-5"></div>
              <div class="confetti-piece confetti-6"></div>
          </div>
          </div>
          <h2 class="success-title mb-3">Payment Successful! 🎉</h2>
          <p class="success-subtitle">Your ticket has been generated and is ready to use</p>
        </div>

        <!-- Ticket Display with QR Code -->
        <div class="ticket-display mb-5">
          <div class="ticket-card">
            <!-- Ticket Header -->
            <div class="ticket-header">
              <div class="ticket-icon">
                <i class="bi bi-ticket-perforated"></i>
              </div>
              <div class="ticket-info">
                <div class="ticket-number">{{ ticket?.ticket_number || 'TICKET-' + Date.now() }}</div>
                <div class="ticket-status">Valid Ticket</div>
              </div>
              </div>
              
            <!-- Event Details -->
            <div class="ticket-event-details">
              <h3 class="event-title">{{ event?.title }}</h3>
              <div class="event-meta">
                <div class="event-detail">
                  <i class="bi bi-calendar3"></i>
                  <span>{{ formatEventDate(event?.event_date) }}</span>
              </div>
                <div class="event-detail">
                  <i class="bi bi-geo-alt"></i>
                  <span>{{ event?.location }}</span>
                </div>
                <div class="event-detail">
                  <i class="bi bi-tag"></i>
                  <span>{{ event?.category }}</span>
                </div>
            </div>
          </div>

            <!-- QR Code Section -->
            <div class="ticket-qr-section">
              <div class="qr-container">
                <canvas ref="ticketQrCanvas" class="ticket-qr-code"></canvas>
              </div>
              <div class="qr-instructions">
                <p>Show this QR code at the event entrance</p>
              </div>
            </div>
            
            <!-- Ticket Footer -->
            <div class="ticket-footer">
              <div class="payment-method">
                <i class="bi bi-credit-card"></i>
                <span>{{ getPaymentMethodName() }}</span>
              </div>
                             <div class="amount-paid">
                 <span class="amount-label">Amount:</span>
                 <span class="amount-value">${{ event?.price || '0.00' }}</span>
               </div>
            </div>
          </div>
            </div>
            
        <!-- Enhanced Action Buttons -->
        <div class="success-actions">
          <div class="row g-3">
            <div class="col-md-6">
              <button
                @click="downloadTicket"
                class="btn-download-ticket w-100 position-relative overflow-hidden"
              >
                <div class="btn-download-bg position-absolute top-0 start-0 w-100 h-100"></div>
                <div class="btn-download-content position-relative d-flex align-items-center justify-content-center">
                  <i class="bi bi-download me-2"></i>
                  Download Ticket
              </div>
                <div class="btn-download-glow position-absolute top-0 start-0 w-100 h-100"></div>
              </button>
            </div>
            <div class="col-md-6">
              <button
                                 @click="ticket && $emit('payment-success', ticket)"
                class="btn-view-ticket w-100 position-relative overflow-hidden"
              >
                <div class="btn-view-bg position-absolute top-0 start-0 w-100 h-100"></div>
                <div class="btn-view-content position-relative d-flex align-items-center justify-content-center">
                  <i class="bi bi-eye me-2"></i>
                  View in My Tickets
          </div>
                <div class="btn-view-glow position-absolute top-0 start-0 w-100 h-100"></div>
              </button>
        </div>
      </div>
    </div>

      </div>
    </div>

    <!-- Modern Error State -->
    <div v-else-if="paymentStep === 'error'" class="error-payment-modern">
      <div class="error-payment-card">
        <!-- Error Header -->
        <div class="error-header text-center mb-5">
          <div class="error-icon-container mb-4">
            <div class="error-icon">
              <i class="bi bi-exclamation-triangle"></i>
            </div>
          </div>
          <h2 class="error-title mb-3">Payment Failed</h2>
          <p class="error-subtitle">Don't worry, let's try again</p>
        </div>

          <!-- Error Message -->
        <div class="error-message-card mb-5">
          <div class="error-message-icon text-center mb-3">
            <i class="bi bi-info-circle"></i>
            </div>
          <h3 class="error-message-title text-center mb-2">What happened?</h3>
          <p class="error-message-text text-center">{{ errorMessage }}</p>
          </div>

        <!-- Error Action Buttons -->
        <div class="error-actions">
          <div class="row g-3">
            <div class="col-12">
            <button
              @click="resetPayment"
                class="btn-retry w-100 position-relative overflow-hidden"
              >
                <div class="btn-retry-bg position-absolute top-0 start-0 w-100 h-100"></div>
                <div class="btn-retry-content position-relative d-flex align-items-center justify-content-center">
                  <i class="bi bi-arrow-clockwise me-3"></i>
                Try Again
                </div>
                <div class="btn-retry-glow position-absolute top-0 start-0 w-100 h-100"></div>
            </button>
            </div>
            
            <div class="col-12">
            <button
              @click="$emit('payment-cancelled')"
                class="btn-cancel-error w-100"
            >
                <i class="bi bi-arrow-left me-2"></i>
              Cancel & Go Back
            </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onUnmounted, nextTick } from 'vue'
import axios from 'axios'
import QRCode from 'qrcode'
import { useAuthStore } from '@/stores/auth'

interface Event {
  id: number
  title: string
  price: number
  [key: string]: any
}

interface Ticket {
  id: number
  ticket_number: string
  [key: string]: any
}

const props = defineProps<{
  event: Event
}>()

defineEmits<{
  'payment-success': [ticket: Ticket]
  'payment-cancelled': []
}>()

const authStore = useAuthStore()
const paymentStep = ref<'form' | 'qr' | 'processing' | 'success' | 'error'>('form')
const loading = ref(false)
const errorMessage = ref('')
const transactionId = ref('')
const timeRemaining = ref(900) // 15 minutes in seconds
const ticket = ref<Ticket | null>(null)

let countdownInterval: NodeJS.Timeout | null = null
let statusCheckInterval: NodeJS.Timeout | null = null

const paymentForm = ref({
  payment_method: 'khqr',
  phone_number: '',
  customer_name: ''
})

// Template refs
const ticketQrCanvas = ref<HTMLCanvasElement>()

const getPaymentMethodName = () => {
  return 'KHQR/Bakong'
}

const formatEventDate = (dateString: string) => {
  if (!dateString) return 'TBD'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const generateTicketQRCode = async () => {
  if (!ticket.value || !ticketQrCanvas.value) return
  
  try {
    const qrData = JSON.stringify({
      ticket_id: ticket.value.id,
      ticket_number: ticket.value.ticket_number,
      event_id: props.event.id,
      event_title: props.event.title,
      user_id: ticket.value.user_id,
      generated_at: new Date().toISOString(),
      verification_code: ticket.value.verification_code || `VER-${Date.now()}`
    })

    await QRCode.toCanvas(ticketQrCanvas.value, qrData, {
      width: 200,
      margin: 2,
      color: {
        dark: '#000000',
        light: '#FFFFFF'
      }
    })
  } catch (error) {
    console.error('QR Code generation failed:', error)
  }
}

const downloadTicket = () => {
  if (!ticketQrCanvas.value || !ticket.value) return
  
  // Create a temporary canvas for the full ticket
  const canvas = document.createElement('canvas')
  const ctx = canvas.getContext('2d')
  if (!ctx) return

  canvas.width = 400
  canvas.height = 600

  // White background
  ctx.fillStyle = '#ffffff'
  ctx.fillRect(0, 0, canvas.width, canvas.height)

  // Add ticket content
  ctx.fillStyle = '#000000'
  ctx.font = 'bold 24px Arial'
  ctx.textAlign = 'center'
  ctx.fillText('EVENT TICKET', canvas.width / 2, 50)

  ctx.font = '18px Arial'
  ctx.fillText(props.event.title, canvas.width / 2, 100)

  ctx.font = '14px Arial'
  ctx.fillText(`Ticket: ${ticket.value.ticket_number}`, canvas.width / 2, 130)
  ctx.fillText(`Event: ${props.event.location}`, canvas.width / 2, 150)
     ctx.fillText(`Date: ${formatEventDate(props.event.event_date)}`, canvas.width / 2, 170)
   ctx.fillText(`Price: $${props.event.price || '0.00'}`, canvas.width / 2, 190)

   // Add QR code
  const qrImage = ticketQrCanvas.value.toDataURL()
  const img = new Image()
  img.onload = () => {
    ctx.drawImage(img, (canvas.width - 200) / 2, 220, 200, 200)
    
    // Add instructions
    ctx.font = '12px Arial'
    ctx.fillText('Show this QR code at the event entrance', canvas.width / 2, 470)
    ctx.fillText('Keep this ticket safe', canvas.width / 2, 490)

    // Download
    const link = document.createElement('a')
    link.download = `ticket-${ticket.value?.ticket_number || 'unknown'}.png`
    link.href = canvas.toDataURL()
    link.click()
  }
  img.src = qrImage
}

// Removed unused formatCardNumber function since we're not using credit cards

// Card validation function (unused but kept for future use)
// const validateCardNumber = (cardNumber: string, cardType: string) => {
//   const cleanNumber = cardNumber.replace(/\s/g, '')
//   if (cardType === 'visa_card') {
//     return /^4[0-9]{12}(?:[0-9]{3})?$/.test(cleanNumber)
//   } else if (cardType === 'mastercard') {
//     return /^5[1-5][0-9]{14}$/.test(cleanNumber)
//   }
//   return false
// }

const initiatePayment = async () => {
  // Check if user is authenticated
  if (!authStore.isAuthenticated) {
    errorMessage.value = 'Please log in to purchase tickets'
    paymentStep.value = 'error'
    return
  }
  
  loading.value = true
  errorMessage.value = ''

  // Prepare KHQR payment data
  const paymentData = {
    event_id: props.event.id,
    payment_method: 'khqr',
    phone_number: paymentForm.value.phone_number,
    customer_name: paymentForm.value.customer_name,
    amount: props.event.price,
    currency: 'USD'
  }

  console.log('Initiating payment with data:', paymentData)
  console.log('Auth state:', {
    isAuthenticated: authStore.isAuthenticated,
    token: authStore.token ? 'present' : 'missing',
    user: authStore.user ? authStore.user.name : 'no user'
  })

  try {
    const response = await axios.post('/api/payments/initiate', paymentData)

    console.log('Payment response:', response.data)
    
        if (response.data.status === 'success') {
      transactionId.value = response.data.data.transaction_id
      const khqrData = response.data.data.khqr_data
      
      if (response.data.data.status === 'pending') {
        if (khqrData && khqrData.qr_code) {
          // Show QR code for payment
          paymentStep.value = 'qr'
          // Store KHQR data for QR display
          // You can add a ref for this data if needed
          
          // Start checking payment status
          startPaymentStatusCheck()
        } else {
          // No QR code received, show processing instead
          paymentStep.value = 'processing'
          
          // Start checking payment status
          startPaymentStatusCheck()
        }
      } else if (response.data.data.status === 'completed') {
        // Payment completed immediately (demo mode)
        paymentStep.value = 'processing'
        
        setTimeout(async () => {
          ticket.value = response.data.data.ticket || { 
            id: Date.now(), 
            ticket_number: 'EP-' + Date.now(),
            event: props.event,
            user_id: 1,
            verification_code: `VER-${Date.now()}`
          }
          paymentStep.value = 'success'
          loading.value = false
          
          await nextTick()
          await generateTicketQRCode()
        }, 3000)
      }
    }
  } catch (error: any) {
    console.error('Payment initiation error:', error)
    console.error('Error response:', error.response?.data)
    console.error('Validation errors:', error.response?.data?.errors)
    console.error('Request headers:', error.config?.headers)
    
    // Show detailed validation errors
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const validationErrors = error.response.data.errors
      console.error('Detailed validation errors:')
      Object.keys(validationErrors).forEach(field => {
        console.error(`- ${field}:`, validationErrors[field])
      })
      
      // Create a more detailed error message
      const errorMessages = Object.values(validationErrors).flat()
      errorMessage.value = errorMessages.join(', ') || 'Validation failed'
    } else if (error.response?.status === 500) {
      errorMessage.value = 'Server error occurred. Please try again or contact support.'
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else if (error.message) {
      errorMessage.value = error.message
    } else {
      errorMessage.value = 'Failed to initiate payment. Please try again.'
    }
    
    paymentStep.value = 'error'
    loading.value = false
  }
}

// QR code generation removed - no longer needed for new payment methods

// Payment status checking and cancellation functions (unused in current implementation but kept for future use)
// const checkPaymentStatus = async () => { ... }
// const cancelPayment = async () => { ... }

const resetPayment = () => {
  paymentStep.value = 'form'
  errorMessage.value = ''
  transactionId.value = ''
  timeRemaining.value = 900
  stopCountdown()
  stopStatusChecking()
}

// Timer and status checking functions - simplified for instant payments
const stopCountdown = () => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
    countdownInterval = null
  }
}

const stopStatusChecking = () => {
  if (statusCheckInterval) {
    clearInterval(statusCheckInterval)
    statusCheckInterval = null
  }
}

const startPaymentStatusCheck = () => {
  // Check payment status every 5 seconds
  statusCheckInterval = setInterval(async () => {
    try {
      const response = await axios.post('/api/payments/status', {
        transaction_id: transactionId.value
      })
      
      if (response.data.data.payment_status === 'completed') {
        stopStatusChecking()
        ticket.value = response.data.data.ticket
        paymentStep.value = 'success'
        loading.value = false
        
        await nextTick()
        await generateTicketQRCode()
      } else if (response.data.data.payment_status === 'failed') {
        stopStatusChecking()
        errorMessage.value = 'Payment failed. Please try again.'
        paymentStep.value = 'error'
        loading.value = false
      }
    } catch (error) {
      console.error('Status check error:', error)
      // Continue checking, don't stop on network errors
    }
  }, 5000) // Check every 5 seconds
}

onUnmounted(() => {
  stopCountdown()
  stopStatusChecking()
})
</script>

<style scoped>
/* Modern Payment Experience Styles */
.modern-payment-experience {
  min-height: 600px;
  padding: 20px;
  background: linear-gradient(135deg, rgba(0, 0, 0, 0.9) 0%, rgba(16, 185, 129, 0.1) 50%, rgba(0, 0, 0, 0.9) 100%);
  border-radius: 24px;
  animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced Progress Indicator */
.progress-section {
  margin-bottom: 2rem;
}

.progress-container {
  padding: 20px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.progress-step {
  flex: none;
  min-width: 80px;
}

.step-circle {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  font-weight: 700;
  font-size: 1.1rem;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 3px solid transparent;
}

.step-pending {
  background: rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.5);
  border-color: rgba(255, 255, 255, 0.2);
}

.step-active {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
  animation: pulse 2s infinite;
}

.step-completed {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
}

.step-label {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.85rem;
  white-space: nowrap;
}

.progress-line {
  height: 3px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
  position: relative;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  width: 0;
  background: linear-gradient(90deg, #10b981, #059669);
  border-radius: 2px;
  transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.progress-completed {
  width: 100%;
}

/* Payment Form Modern */
.payment-form-modern {
  animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.payment-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 24px;
  padding: 40px;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.payment-header {
  margin-bottom: 2rem;
}

.payment-icon-container {
  position: relative;
  display: inline-block;
}

.payment-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  color: white;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
  animation: iconFloat 3s ease-in-out infinite;
}

.payment-icon-glow {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
  animation: iconGlow 2s ease-in-out infinite alternate;
}

.payment-title {
  font-size: 2.5rem;
  font-weight: 800;
  background: linear-gradient(135deg, #ffffff 0%, #10b981 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.payment-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1.2rem;
}

/* Payment Method Selection */
.payment-method-selection {
  margin-bottom: 2rem;
}

.payment-method-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: white;
  text-align: center;
}

.payment-method-card {
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

.payment-method-card:hover {
  border-color: rgba(16, 185, 129, 0.5);
  transform: translateY(-4px);
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2);
}

.method-active {
  border-color: #10b981 !important;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.1) 100%);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

.method-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  color: white;
  margin: 0 auto 16px;
  transition: all 0.3s ease;
}

.payment-method-card:hover .method-icon {
  transform: scale(1.1);
}

.method-active .method-icon {
  animation: pulse 2s infinite;
}

.method-info {
  text-align: center;
  margin-bottom: 12px;
}

.method-name {
  font-size: 1.1rem;
  font-weight: 700;
  color: white;
  margin-bottom: 4px;
}

.method-description {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.7);
}

 .method-badge {
   position: absolute;
   top: 12px;
   right: 12px;
   background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
   color: white;
   padding: 4px 12px;
   border-radius: 12px;
   font-size: 0.75rem;
   font-weight: 700;
   box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
 }

 .khqr-badge {
   background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
   box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4) !important;
 }

 .khqr-payment-card {
   background: rgba(255, 255, 255, 0.05);
   border: 2px solid #10b981;
   border-radius: 16px;
   padding: 20px;
   position: relative;
   overflow: hidden;
   backdrop-filter: blur(10px);
   background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.1) 100%);
   box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
 }

 .supported-banks {
   text-align: center;
   margin-top: 1rem;
 }

 .banks-label {
   color: rgba(255, 255, 255, 0.7);
   font-size: 0.9rem;
   margin-bottom: 0.5rem;
 }

 .banks-icons {
   display: flex;
   justify-content: center;
   gap: 0.5rem;
   flex-wrap: wrap;
 }

 .bank-icon {
   background: rgba(255, 255, 255, 0.1);
   border: 1px solid rgba(255, 255, 255, 0.2);
   border-radius: 8px;
   padding: 4px 8px;
   font-size: 0.75rem;
   font-weight: 600;
   color: rgba(255, 255, 255, 0.8);
   backdrop-filter: blur(5px);
 }

/* Event Summary Card */
.event-summary-card {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.summary-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
}

.summary-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: white;
}

.event-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: white;
}

.event-details {
  color: rgba(255, 255, 255, 0.7);
}

.event-separator {
  margin: 0 8px;
  color: rgba(16, 185, 129, 0.7);
}

.price-container {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  padding: 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.price-amount {
  font-size: 2rem;
  font-weight: 800;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.price-label {
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.9rem;
}

/* Modern Form Fields */
.payment-fields {
  margin-bottom: 2rem;
}

.form-field {
  margin-bottom: 1.5rem;
}

.form-label {
  display: flex;
  align-items: center;
  color: rgba(255, 255, 255, 0.9);
  font-weight: 600;
  margin-bottom: 8px;
  font-size: 0.95rem;
}

.field-icon {
  margin-right: 8px;
  color: #10b981;
  font-size: 1.1rem;
}

.input-container {
  position: relative;
}

.form-control-modern {
  width: 100%;
  padding: 16px 20px;
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  color: white;
  font-size: 1rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(10px);
}

.form-control-modern:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
  background: rgba(255, 255, 255, 0.08);
}

.form-control-modern::placeholder {
  color: rgba(255, 255, 255, 0.4);
}

.input-focus-border {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, #10b981, #059669);
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 1px;
}

.form-control-modern:focus + .input-focus-border {
  width: 100%;
}

/* Security Notice */
.security-notice {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
  border-radius: 12px;
  padding: 16px;
  border-left: 4px solid #10b981;
}

.security-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  color: white;
}

.security-title {
  font-weight: 700;
  color: white;
  font-size: 1rem;
}

.security-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.9rem;
}

/* Submit Button */
.submit-section {
  margin-top: 2rem;
}

.btn-submit-modern {
  padding: 18px 32px;
  border: none;
  border-radius: 16px;
  font-weight: 700;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 8px 30px rgba(16, 185, 129, 0.4);
}

.btn-submit-bg {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 16px;
}

.btn-submit-content {
  color: white;
  z-index: 2;
  font-size: 1.1rem;
}

.btn-submit-glow {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
  opacity: 0;
  border-radius: 16px;
  transition: opacity 0.3s ease;
}

.btn-submit-modern:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.5);
}

.btn-submit-modern:hover .btn-submit-glow {
  opacity: 1;
}

.btn-submit-modern:active {
  transform: translateY(-1px) scale(0.98);
}

.btn-submit-modern:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* QR Payment Modern */
.qr-payment-modern {
  animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.qr-payment-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 24px;
  padding: 40px;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.qr-header {
  margin-bottom: 2rem;
}

.qr-icon-container {
  position: relative;
  display: inline-block;
}

.qr-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  color: white;
  box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
}

.qr-icon-pulse {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(139, 92, 246, 0.3) 0%, transparent 70%);
  animation: pulse 2s ease-in-out infinite;
}

.qr-title {
  font-size: 2.5rem;
  font-weight: 800;
  background: linear-gradient(135deg, #ffffff 0%, #8b5cf6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.qr-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1.2rem;
}

.timer-badge {
  display: inline-flex;
  align-items: center;
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.2) 100%);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 50px;
  padding: 12px 20px;
  backdrop-filter: blur(10px);
  animation: timerPulse 1s ease-in-out infinite;
}

.timer-icon {
  margin-right: 8px;
  color: #ef4444;
  font-size: 1.1rem;
}

.timer-text {
  color: #fecaca;
  font-weight: 600;
  font-size: 0.95rem;
}

/* QR Code Display */
.qr-display-section {
  text-align: center;
}

.qr-code-container {
  position: relative;
  display: inline-block;
}

.phone-mockup {
  background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
  border-radius: 40px;
  padding: 8px;
  box-shadow: 
    0 25px 60px rgba(0, 0, 0, 0.4),
    0 0 0 1px rgba(255, 255, 255, 0.05);
  position: relative;
  transform: scale(1);
  transition: transform 0.3s ease;
}

.phone-mockup:hover {
  transform: scale(1.02);
}

.phone-screen {
  background: #000000;
  border-radius: 32px;
  padding: 30px;
  position: relative;
}

.phone-notch {
  position: absolute;
  top: 12px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 24px;
  background: #000000;
  border-radius: 12px;
  z-index: 10;
}

.qr-code-area {
  background: white;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 20px;
  box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.1);
}

.qr-code-canvas {
  border-radius: 8px;
  animation: qrGlow 3s ease-in-out infinite;
}

.transaction-info {
  background: #1f2937;
  border-radius: 12px;
  padding: 16px;
}

.transaction-label {
  color: #9ca3af;
  font-size: 0.8rem;
  margin-bottom: 4px;
}

.transaction-id {
  color: white;
  font-family: 'Courier New', monospace;
  font-size: 0.9rem;
  font-weight: 600;
}

.price-badge {
  position: absolute;
  top: -20px;
  right: -20px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 16px;
  padding: 12px 16px;
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
  transform: rotate(12deg);
  animation: float 4s ease-in-out infinite;
}

.price-badge-content {
  text-align: center;
}

.price-badge-label {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.75rem;
  font-weight: 500;
}

.price-badge-amount {
  color: white;
  font-size: 1.2rem;
  font-weight: 800;
}

/* Payment Steps Guide */
.payment-steps-guide {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.payment-step-item {
  padding: 16px;
}

.step-icon-modern {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  margin: 0 auto 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}

.step-blue {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.step-purple {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
}

.step-green {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.step-orange {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

.step-icon-modern:hover {
  transform: scale(1.1) rotate(5deg);
}

.step-text {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.9rem;
  font-weight: 600;
}

/* QR Actions */
.qr-actions {
  margin-top: 2rem;
}

.btn-check-payment {
  padding: 18px 32px;
  border: none;
  border-radius: 16px;
  font-weight: 700;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 8px 30px rgba(16, 185, 129, 0.4);
  margin-bottom: 12px;
}

.btn-check-bg {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 16px;
}

.btn-check-content {
  color: white;
  z-index: 2;
}

.btn-check-glow {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
  opacity: 0;
  border-radius: 16px;
  transition: opacity 0.3s ease;
}

.btn-check-payment:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.5);
}

.btn-check-payment:hover .btn-check-glow {
  opacity: 1;
}

.btn-cancel-payment {
  padding: 16px 32px;
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: 16px;
  color: rgba(255, 255, 255, 0.8);
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.btn-cancel-payment:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.4);
  color: white;
  transform: translateY(-2px);
}

/* Success Payment Modern */
.success-payment-modern {
  animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.success-payment-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 24px;
  padding: 40px;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.success-header {
  margin-bottom: 2rem;
}

.success-icon-container {
  position: relative;
  display: inline-block;
}

.success-icon {
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: white;
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.4);
  animation: successBounce 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.success-confetti {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.confetti-piece {
  position: absolute;
  width: 8px;
  height: 8px;
  border-radius: 2px;
  animation: confetti 3s ease-out infinite;
}

.confetti-1 {
  background: #10b981;
  top: 20%;
  left: 20%;
  animation-delay: 0s;
}

.confetti-2 {
  background: #059669;
  top: 30%;
  right: 20%;
  animation-delay: 0.5s;
}

.confetti-3 {
  background: #047857;
  bottom: 30%;
  left: 30%;
  animation-delay: 1s;
}

.confetti-4 {
  background: #065f46;
  bottom: 20%;
  right: 30%;
  animation-delay: 1.5s;
}

.confetti-5 {
  background: #10b981;
  top: 50%;
  left: 10%;
  animation-delay: 2s;
}

.confetti-6 {
  background: #059669;
  top: 50%;
  right: 10%;
  animation-delay: 2.5s;
}

.success-title {
  font-size: 2.8rem;
  font-weight: 800;
  background: linear-gradient(135deg, #ffffff 0%, #10b981 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.success-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1.2rem;
}

/* Success Details */
.success-details {
  text-align: center;
}

.ticket-generated-badge {
  display: inline-flex;
  align-items: center;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.2) 100%);
  border: 1px solid rgba(16, 185, 129, 0.3);
  border-radius: 50px;
  padding: 12px 24px;
  backdrop-filter: blur(10px);
}

.badge-icon {
  margin-right: 12px;
  color: #10b981;
  font-size: 1.2rem;
}

.badge-text {
  color: #10b981;
  font-weight: 600;
  font-size: 1rem;
}

.event-success-info {
  margin: 2rem 0;
}

.event-success-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: white;
}

.event-success-meta {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1.1rem;
}

.event-success-separator {
  margin: 0 12px;
  color: rgba(16, 185, 129, 0.7);
}

.payment-success-amount {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.amount-label {
  color: rgba(255, 255, 255, 0.6);
  font-size: 1rem;
  margin-bottom: 8px;
}

.amount-value {
  font-size: 2.5rem;
  font-weight: 800;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Success Actions */
.success-actions {
  margin-top: 2rem;
}

.btn-view-ticket {
  padding: 20px 40px;
  border: none;
  border-radius: 16px;
  font-weight: 700;
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 10px 35px rgba(59, 130, 246, 0.4);
}

.btn-view-bg {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  border-radius: 16px;
}

.btn-view-content {
  color: white;
  z-index: 2;
}

.btn-view-glow {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
  opacity: 0;
  border-radius: 16px;
  transition: opacity 0.3s ease;
}

.btn-view-ticket:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 20px 50px rgba(59, 130, 246, 0.5);
}

.btn-view-ticket:hover .btn-view-glow {
  opacity: 1;
}

/* Error Payment Modern */
.error-payment-modern {
  animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.error-payment-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 24px;
  padding: 40px;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.error-header {
  margin-bottom: 2rem;
}

.error-icon-container {
  display: inline-block;
}

.error-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  color: white;
  box-shadow: 0 10px 30px rgba(239, 68, 68, 0.4);
  animation: errorShake 0.8s ease-in-out;
}

.error-title {
  font-size: 2.5rem;
  font-weight: 800;
  background: linear-gradient(135deg, #ffffff 0%, #ef4444 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.error-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1.2rem;
}

.error-message-card {
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.error-message-icon {
  font-size: 2rem;
  color: #ef4444;
}

.error-message-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: white;
}

.error-message-text {
  color: rgba(255, 255, 255, 0.8);
  font-size: 1rem;
}

.error-actions {
  margin-top: 2rem;
}

.btn-retry {
  padding: 18px 32px;
  border: none;
  border-radius: 16px;
  font-weight: 700;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 8px 30px rgba(59, 130, 246, 0.4);
  margin-bottom: 12px;
}

.btn-retry-bg {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  border-radius: 16px;
}

.btn-retry-content {
  color: white;
  z-index: 2;
}

.btn-retry-glow {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
  opacity: 0;
  border-radius: 16px;
  transition: opacity 0.3s ease;
}

.btn-retry:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 15px 40px rgba(59, 130, 246, 0.5);
}

.btn-retry:hover .btn-retry-glow {
  opacity: 1;
}

.btn-cancel-error {
  padding: 16px 32px;
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: 16px;
  color: rgba(255, 255, 255, 0.8);
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.btn-cancel-error:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.4);
  color: white;
  transform: translateY(-2px);
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
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

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.05);
    opacity: 0.8;
  }
}

@keyframes iconFloat {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes iconGlow {
  0% {
    opacity: 0.3;
    transform: scale(1);
  }
  100% {
    opacity: 0.6;
    transform: scale(1.1);
  }
}

@keyframes timerPulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

@keyframes qrGlow {
  0%, 100% {
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
  }
  50% {
    box-shadow: 0 0 30px rgba(16, 185, 129, 0.6);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(12deg);
  }
  50% {
    transform: translateY(-10px) rotate(8deg);
  }
}

@keyframes successBounce {
  0% {
    transform: scale(0.3);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
  }
  70% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes confetti {
  0% {
    transform: translateY(0) rotateZ(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(-120px) rotateZ(720deg);
    opacity: 0;
  }
}

@keyframes errorShake {
  0%, 100% {
    transform: translateX(0);
  }
  10%, 30%, 50%, 70%, 90% {
    transform: translateX(-5px);
  }
  20%, 40%, 60%, 80% {
    transform: translateX(5px);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .modern-payment-experience {
    padding: 15px;
  }
  
  .payment-card,
  .qr-payment-card,
  .success-payment-card,
  .error-payment-card {
    padding: 24px;
  }
  
  .progress-container {
    padding: 16px;
  }
  
  .step-circle {
    width: 40px;
    height: 40px;
    font-size: 0.9rem;
  }
  
  .step-label {
    font-size: 0.75rem;
  }
  
  .payment-title,
  .qr-title,
  .success-title,
  .error-title {
    font-size: 2rem;
  }
  
  .phone-mockup {
    transform: scale(0.9);
  }
  
  .price-badge {
    transform: scale(0.85) rotate(8deg);
  }
  
  .step-icon-modern {
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
  }
}

@media (max-width: 576px) {
  .progress-step {
    min-width: 60px;
  }
  
  .step-circle {
    width: 35px;
    height: 35px;
    font-size: 0.8rem;
  }
  
  .step-label {
    font-size: 0.7rem;
  }
  
  .payment-title,
  .qr-title,
  .success-title,
  .error-title {
    font-size: 1.8rem;
  }
  
  .phone-mockup {
    transform: scale(0.8);
  }
}

/* Processing Payment Styles */
.processing-payment-modern {
  padding: 40px;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
  border-radius: 24px;
  border: 1px solid rgba(16, 185, 129, 0.2);
  backdrop-filter: blur(10px);
}

.processing-payment-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  padding: 40px;
  backdrop-filter: blur(15px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.processing-header {
  margin-bottom: 2rem;
}

.processing-icon-container {
  position: relative;
  display: inline-block;
}

.processing-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  color: white;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.processing-spinner {
  position: absolute;
  top: -10px;
  left: -10px;
  width: 100px;
  height: 100px;
  border: 3px solid transparent;
  border-top: 3px solid #10b981;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.processing-title {
  font-size: 2rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.5rem;
}

.processing-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1.1rem;
}

.processing-steps {
  margin-bottom: 2rem;
}

.processing-step-item {
  padding: 20px;
}

.transaction-info-card {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 24px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
}

.transaction-label {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 8px;
}

.transaction-id {
  font-size: 1.2rem;
  font-weight: 700;
  color: #10b981;
  font-family: 'Courier New', monospace;
}

.amount-info {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.amount-label {
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.9rem;
}

.amount-value {
  color: #10b981;
  font-weight: 700;
  font-size: 1.4rem;
}

.processing-message {
  margin-top: 2rem;
}

.processing-text {
  color: rgba(255, 255, 255, 0.8);
  font-size: 1rem;
  margin: 0;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Ticket Display Styles */
.ticket-display {
  display: flex;
  justify-content: center;
}

.ticket-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  max-width: 400px;
  width: 100%;
  position: relative;
  overflow: hidden;
}

.ticket-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #10b981 0%, #059669 50%, #10b981 100%);
}

.ticket-header {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px dashed rgba(16, 185, 129, 0.3);
}

.ticket-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  margin-right: 15px;
}

.ticket-info {
  flex: 1;
}

.ticket-number {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1f2937;
  font-family: 'Courier New', monospace;
}

.ticket-status {
  font-size: 0.9rem;
  color: #10b981;
  font-weight: 600;
}

.ticket-event-details {
  margin-bottom: 25px;
}

.event-title {
  font-size: 1.4rem;
  font-weight: 800;
  color: #1f2937;
  margin-bottom: 15px;
  line-height: 1.3;
}

.event-meta {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.event-detail {
  display: flex;
  align-items: center;
  font-size: 0.95rem;
  color: #6b7280;
}

.event-detail i {
  width: 18px;
  margin-right: 10px;
  color: #10b981;
}

.ticket-qr-section {
  text-align: center;
  margin-bottom: 25px;
  padding: 20px;
  background: rgba(16, 185, 129, 0.05);
  border-radius: 15px;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.qr-container {
  display: inline-block;
  padding: 15px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  margin-bottom: 15px;
}

.ticket-qr-code {
  border-radius: 8px;
}

.qr-instructions {
  margin: 0;
}

.qr-instructions p {
  font-size: 0.9rem;
  color: #6b7280;
  margin: 0;
  font-weight: 500;
}

.ticket-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 15px;
  border-top: 2px dashed rgba(16, 185, 129, 0.3);
  font-size: 0.9rem;
}

.payment-method {
  display: flex;
  align-items: center;
  color: #6b7280;
}

.payment-method i {
  margin-right: 8px;
  color: #10b981;
}

.amount-paid {
  font-weight: 700;
  color: #1f2937;
}

.amount-label {
  color: #6b7280;
  margin-right: 5px;
}

.amount-value {
  color: #10b981;
  font-size: 1.1rem;
}

/* Download Button Styles */
.btn-download-ticket {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border: none;
  border-radius: 12px;
  padding: 12px 24px;
  color: white;
  font-weight: 600;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-download-ticket:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.btn-download-bg {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border-radius: 12px;
}

.btn-download-content {
  z-index: 2;
}

.btn-download-glow {
  background: radial-gradient(circle at center, rgba(59, 130, 246, 0.4) 0%, transparent 70%);
    opacity: 0;
  transition: opacity 0.3s ease;
  }

.btn-download-ticket:hover .btn-download-glow {
    opacity: 1;
}

/* Authentication Required Styles */
.auth-required-modern {
  animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.auth-required-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 24px;
  padding: 40px;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  text-align: center;
}

.auth-required-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  color: white;
  box-shadow: 0 10px 30px rgba(245, 158, 11, 0.4);
  animation: iconFloat 3s ease-in-out infinite;
}

.auth-required-title {
  font-size: 2rem;
  font-weight: 800;
  background: linear-gradient(135deg, #ffffff 0%, #f59e0b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.auth-required-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1.1rem;
}

.btn-auth-login, .btn-auth-register {
  padding: 16px 24px;
  border-radius: 16px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  color: white;
}

.btn-auth-login:hover, .btn-auth-register:hover {
  transform: translateY(-3px) scale(1.02);
  text-decoration: none;
  color: white;
}

.btn-auth-bg {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 16px;
}

.btn-register-bg {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border-radius: 16px;
}

.btn-auth-content, .btn-register-content {
  z-index: 2;
}

.btn-auth-login:hover {
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.5);
}

.btn-auth-register:hover {
  box-shadow: 0 15px 40px rgba(59, 130, 246, 0.5);
}

/* Responsive Design */
@media (max-width: 768px) {
  .ticket-card {
    margin: 0 10px;
    padding: 20px;
  }
  
  .event-title {
    font-size: 1.2rem;
  }
  
  .ticket-footer {
    flex-direction: column;
    gap: 10px;
    text-align: center;
  }
  
  .auth-required-card {
    padding: 24px;
  }
  
  .auth-required-title {
    font-size: 1.6rem;
  }
}
</style>
