<template>
  <div class="min-vh-100 position-relative overflow-hidden validate-page" style="padding-top: 100px;">
    <!-- Animated Background -->
    <div class="position-absolute w-100 h-100 top-0 start-0" style="z-index: -1;">
      <div class="position-absolute w-100 h-100 validate-background"></div>
      <div class="position-absolute w-100 h-100 top-0 start-0 validate-overlay"></div>
      <div class="position-absolute top-0 start-0 w-100 h-100">
        <div class="floating-particles">
          <div class="particle particle-1"></div>
          <div class="particle particle-2"></div>
          <div class="particle particle-3"></div>
          <div class="particle particle-4"></div>
        </div>
      </div>
    </div>

    <div class="container position-relative" style="z-index: 2; max-width: 900px;">
      <!-- Header -->
      <div class="text-center mb-5">
        <div class="position-relative d-inline-block mb-4">
          <div class="d-inline-flex align-items-center justify-content-center rounded-4 mx-auto glow-animation shadow-lg" 
               style="width: 100px; height: 100px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="bi bi-qr-code-scan display-5 text-white"></i>
          </div>
          <div class="position-absolute top-0 start-0 w-100 h-100 rounded-4 glow-ring"></div>
        </div>
        <h1 class="display-3 fw-bold text-light mb-3 professional-title">Validate Ticket</h1>
        <p class="lead text-light opacity-75 mb-0" style="font-size: 1.25rem;">
          Scan QR code or enter ticket number to validate entry
        </p>
      </div>

      <!-- QR Scanner Modal -->
      <div v-if="showScanner" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
           style="z-index: 1050; background: rgba(0, 0, 0, 0.9);">
        <div class="card border-0 shadow-lg" style="max-width: 500px; width: 90%;">
          <div class="card-header bg-dark text-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
              <i class="bi bi-camera me-2"></i>
              QR Code Scanner
            </h5>
            <button @click="closeQRScanner" class="btn-close btn-close-white"></button>
          </div>
          <div class="card-body bg-dark text-center">
            <div v-if="cameraError" class="alert alert-danger">
              <i class="bi bi-exclamation-triangle me-2"></i>
              {{ cameraError }}
            </div>
            <div v-else>
              <video
                ref="videoRef"
                style="width: 100%; max-width: 400px; border-radius: 8px;"
                autoplay
                muted
                playsinline
              ></video>
              <p class="text-muted mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Point your camera at the QR code
              </p>
            </div>
          </div>
          <div class="card-footer bg-dark border-top-0">
            <button @click="closeQRScanner" class="btn btn-secondary w-100">
              Cancel
            </button>
          </div>
        </div>
      </div>

      <!-- Validation Form -->
      <div class="card border-0 shadow-lg mb-5" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.05);">
        <div class="card-body p-5">
          <form @submit.prevent="validateTicket">
            <!-- Error Message -->
            <div v-if="ticketsStore.error" class="alert alert-danger d-flex align-items-center mb-4">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <div>{{ ticketsStore.error }}</div>
            </div>

            <!-- Ticket Number Input -->
            <div class="mb-4">
              <label for="ticketNumber" class="form-label text-light fw-semibold fs-5">Ticket Number</label>
              <div class="input-group input-group-lg">
                <span class="input-group-text bg-transparent border-secondary">
                  <i class="bi bi-ticket-perforated text-primary fs-4"></i>
                </span>
                <input
                  id="ticketNumber"
                  v-model="ticketNumber"
                  type="text"
                  placeholder="Enter ticket number (e.g., EP-ABC123)"
                  class="form-control form-control-lg bg-transparent border-secondary text-light text-center fw-bold"
                  style="font-family: 'Courier New', monospace; letter-spacing: 2px;"
                  required
                  :disabled="ticketsStore.loading"
                />
              </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-grid gap-2 mb-4">
              <!-- Check Validity Button (doesn't mark as used) -->
              <button
                type="button"
                @click="checkTicketValidity"
                :disabled="ticketsStore.loading || !ticketNumber.trim()"
                class="btn btn-info btn-lg d-flex align-items-center justify-content-center position-relative overflow-hidden"
                style="min-height: 60px;"
              >
                <div class="position-absolute w-100 h-100 top-0 start-0 bg-gradient" 
                     style="background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%); 
                            animation: shimmer 2s infinite;"></div>
                <div v-if="ticketsStore.loading" class="spinner-border spinner-border-sm me-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <i v-else class="bi bi-search me-2 fs-5"></i>
                {{ ticketsStore.loading ? 'Checking...' : 'Check Ticket Validity' }}
              </button>
              
              <!-- Validate & Mark as Used Button -->
              <button
                type="submit"
                :disabled="ticketsStore.loading || !ticketNumber.trim()"
                class="btn btn-success btn-lg d-flex align-items-center justify-content-center position-relative overflow-hidden"
                style="min-height: 60px;"
              >
                <div class="position-absolute w-100 h-100 top-0 start-0 bg-gradient" 
                     style="background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%); 
                            animation: shimmer 2s infinite;"></div>
                <div v-if="ticketsStore.loading" class="spinner-border spinner-border-sm me-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <i v-else class="bi bi-check-circle me-2 fs-5"></i>
                {{ ticketsStore.loading ? 'Processing Entry...' : 'Validate & Allow Entry' }}
              </button>
            </div>

            <!-- QR Code Scanner Button -->
            <div class="pt-4 border-top border-secondary">
              <button
                type="button"
                @click="openQRScanner"
                class="btn btn-outline-light btn-lg w-100 d-flex align-items-center justify-content-center"
              >
                <i class="bi bi-camera me-2 fs-5"></i>
                Scan QR Code
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Validation Result -->
      <div v-if="ticketsStore.validationResult" class="mb-5">
        <!-- Success Result -->
        <div v-if="validationSuccess" class="card border-0 shadow-lg" style="backdrop-filter: blur(10px); background: rgba(34, 197, 94, 0.1); border: 2px solid rgba(34, 197, 94, 0.3) !important;">
          <div class="card-header bg-transparent border-bottom border-success">
            <div class="d-flex align-items-center">
              <div class="d-inline-flex align-items-center justify-content-center rounded-3 me-3" 
                   style="width: 60px; height: 60px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                <i class="bi bi-check-circle text-white fs-3"></i>
              </div>
              <div>
                <h3 class="text-light fw-bold mb-1">Ticket Valid!</h3>
                <p class="text-success mb-0">
                  {{ ticketsStore.validationResult?.was_marked_used ? 'Entry approved - Ticket marked as used' : 'Ticket is valid and ready for entry' }}
                </p>
              </div>
            </div>
          </div>
          
          <div class="card-body p-4">
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

            <!-- Mark as Used Button (if ticket was only validated, not used) -->
            <div v-if="!ticketsStore.validationResult?.was_marked_used" class="mt-4 pt-4 border-top border-success">
              <button
                @click="markTicketAsUsed"
                :disabled="ticketsStore.loading"
                class="btn btn-warning btn-lg w-100 d-flex align-items-center justify-content-center"
              >
                <div v-if="ticketsStore.loading" class="spinner-border spinner-border-sm me-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <i v-else class="bi bi-door-open me-2 fs-5"></i>
                {{ ticketsStore.loading ? 'Processing Entry...' : 'Allow Entry & Mark as Used' }}
              </button>
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
import { ref, computed, onUnmounted } from 'vue'
import { useTicketsStore } from '@/stores/tickets'
import QrScanner from 'qr-scanner'

const ticketsStore = useTicketsStore()
const ticketNumber = ref('')
const showScanner = ref(false)
const cameraError = ref('')
const videoRef = ref<HTMLVideoElement | null>(null)
let qrScanner: QrScanner | null = null

const validationSuccess = computed(() => {
  return ticketsStore.validationResult && !ticketsStore.error
})

const checkTicketValidity = async () => {
  if (!ticketNumber.value.trim()) return
  
  await ticketsStore.validateTicket(ticketNumber.value.trim(), false) // false = don't mark as used
}

const validateTicket = async () => {
  if (!ticketNumber.value.trim()) return
  
  await ticketsStore.validateTicket(ticketNumber.value.trim(), true) // true = mark as used
}

const markTicketAsUsed = async () => {
  if (!ticketNumber.value.trim()) return
  
  await ticketsStore.validateTicket(ticketNumber.value.trim(), true) // true = mark as used
}

const openQRScanner = async () => {
  showScanner.value = true
  cameraError.value = ''
  
  // Wait for the next tick to ensure the video element is rendered
  await new Promise(resolve => setTimeout(resolve, 100))
  
  if (videoRef.value) {
    try {
      qrScanner = new QrScanner(
        videoRef.value,
        (result) => onQRDecode(result.data),
        {
          onDecodeError: (error) => {
            // Silently ignore decode errors as they happen continuously while scanning
            console.debug('QR decode error:', error)
          },
          preferredCamera: 'environment',
          highlightScanRegion: true,
          highlightCodeOutline: true,
        }
      )
      
      await qrScanner.start()
    } catch (error: any) {
      console.error('QR Scanner initialization failed:', error)
      cameraError.value = 'Camera access failed. Please ensure camera permissions are granted and try again.'
    }
  }
}

const closeQRScanner = () => {
  if (qrScanner) {
    qrScanner.stop()
    qrScanner.destroy()
    qrScanner = null
  }
  showScanner.value = false
  cameraError.value = ''
}

const onQRDecode = async (result: string) => {
  if (result) {
    let extractedTicketNumber = result
    
    try {
      // Try to parse as JSON first (our QR codes contain JSON data)
      const qrData = JSON.parse(result)
      if (qrData.ticket_number) {
        extractedTicketNumber = qrData.ticket_number
      }
    } catch (e) {
      // If not JSON, try other parsing methods
      
      // If it's a URL, try to extract ticket number from it
      if (result.includes('http')) {
        const urlParams = new URLSearchParams(result.split('?')[1] || '')
        extractedTicketNumber = urlParams.get('ticket') || urlParams.get('id') || result
      }
      
      // If it looks like a ticket number pattern, use it directly
      const ticketMatch = result.match(/EP-[A-Z0-9]+/i)
      if (ticketMatch) {
        extractedTicketNumber = ticketMatch[0]
      }
    }
    
    ticketNumber.value = extractedTicketNumber
    closeQRScanner()
    
    // Auto-check validity if we got a valid-looking ticket number (don't mark as used yet)
    if (extractedTicketNumber.trim()) {
      await checkTicketValidity()
    }
  }
}

// Cleanup on component unmount
onUnmounted(() => {
  if (qrScanner) {
    qrScanner.stop()
    qrScanner.destroy()
  }
})

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

<style scoped>
/* Validate page background */
.validate-background {
  background-image: url('@/assets/images/concert-background.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  filter: blur(2px);
}

.validate-overlay {
  background: linear-gradient(
    135deg,
    rgba(0, 0, 0, 0.8) 0%,
    rgba(16, 185, 129, 0.3) 30%,
    rgba(5, 150, 105, 0.4) 70%,
    rgba(0, 0, 0, 0.9) 100%
  );
  backdrop-filter: blur(1px);
}

/* Floating particles animation */
.floating-particles {
  position: absolute;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.particle {
  position: absolute;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.3) 0%, rgba(5, 150, 105, 0.1) 100%);
  border-radius: 50%;
  animation: floatParticle 8s ease-in-out infinite;
}

.particle-1 {
  width: 30px;
  height: 30px;
  top: 20%;
  left: 15%;
  animation-delay: 0s;
}

.particle-2 {
  width: 20px;
  height: 20px;
  top: 70%;
  right: 20%;
  animation-delay: 2s;
}

.particle-3 {
  width: 40px;
  height: 40px;
  bottom: 30%;
  left: 10%;
  animation-delay: 4s;
}

.particle-4 {
  width: 25px;
  height: 25px;
  top: 40%;
  right: 10%;
  animation-delay: 6s;
}

@keyframes floatParticle {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
    opacity: 0.3;
  }
  25% {
    transform: translateY(-20px) translateX(10px) rotate(90deg);
    opacity: 0.6;
  }
  50% {
    transform: translateY(-10px) translateX(-10px) rotate(180deg);
    opacity: 0.8;
  }
  75% {
    transform: translateY(-30px) translateX(5px) rotate(270deg);
    opacity: 0.4;
  }
}

/* Glow ring animation */
.glow-ring {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.4) 0%, rgba(5, 150, 105, 0.4) 100%);
  animation: pulse-ring 2s ease-in-out infinite;
}

@keyframes pulse-ring {
  0% {
    transform: scale(1);
    opacity: 0.7;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.3;
  }
  100% {
    transform: scale(1);
    opacity: 0.7;
  }
}

/* Button shimmer animation */
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

/* Enhanced form styling */
.form-control:focus,
.form-select:focus {
  border-color: #10b981 !important;
  box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25) !important;
  background-color: rgba(255, 255, 255, 0.1) !important;
}

.form-control::placeholder {
  color: rgba(255, 255, 255, 0.5) !important;
}

.input-group-text {
  border-color: #6b7280 !important;
}

/* Card glass effect */
.card {
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

/* Professional title styling */
.professional-title {
  background: linear-gradient(135deg, #ffffff 0%, #e5e7eb 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Enhanced glow animation */
.glow-animation {
  position: relative;
  overflow: hidden;
}

.glow-animation::before {
  content: '';
  position: absolute;
  top: -2px;
  left: -2px;
  right: -2px;
  bottom: -2px;
  background: linear-gradient(45deg, #10b981, #059669, #10b981);
  border-radius: inherit;
  z-index: -1;
  animation: rotate 2s linear infinite;
  opacity: 0.7;
}

@keyframes rotate {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Success card animation */
.card[style*="rgba(34, 197, 94"] {
  animation: successPulse 2s ease-in-out infinite;
}

@keyframes successPulse {
  0%, 100% {
    box-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
  }
  50% {
    box-shadow: 0 0 30px rgba(34, 197, 94, 0.5);
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .validate-background {
    background-attachment: scroll;
  }
}
</style>
