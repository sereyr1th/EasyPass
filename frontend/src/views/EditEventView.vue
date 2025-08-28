<template>
  <div class="min-vh-100 position-relative overflow-hidden" style="padding-top: 100px;">
    <!-- Animated Background -->
    <div class="position-absolute w-100 h-100 top-0 start-0" style="z-index: -1;">
      <div class="position-absolute w-100 h-100" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(29, 78, 216, 0.1) 100%);"></div>
      <div class="position-absolute top-0 start-0 w-100 h-100">
        <div class="floating-shapes">
          <div class="shape shape-1"></div>
          <div class="shape shape-2"></div>
          <div class="shape shape-3"></div>
        </div>
      </div>
    </div>

    <div class="container" style="max-width: 900px;">
      <!-- Header -->
      <div class="text-center mb-5">
        <div class="position-relative d-inline-block mb-4">
          <div class="d-inline-flex align-items-center justify-content-center rounded-4 mx-auto glow-animation shadow-lg" 
               style="width: 100px; height: 100px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <i class="bi bi-pencil-square display-5 text-white"></i>
          </div>
          <div class="position-absolute top-0 start-0 w-100 h-100 rounded-4 glow-ring"></div>
        </div>
        <h1 class="display-3 fw-bold text-light mb-3 professional-title">Edit Event</h1>
        <p class="lead text-light opacity-75 mb-0" style="font-size: 1.25rem;">Perfect your event details to create an amazing experience</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="d-flex justify-content-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <!-- Edit Form -->
      <div v-else-if="event" class="card border-0 shadow-lg" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.05);">
        <div class="card-body p-5">
          <form @submit.prevent="handleSubmit">
            <!-- Error Message -->
            <div v-if="error" class="alert alert-danger d-flex align-items-center mb-4">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <div>{{ error }}</div>
            </div>

            <!-- Basic Information Section -->
            <div class="mb-5">
              <div class="d-flex align-items-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 me-3" 
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                  <i class="bi bi-info-circle text-white fs-5"></i>
                </div>
                <h3 class="fw-bold text-light mb-0 professional-title">Basic Information</h3>
              </div>
              <div class="row g-4">
                <!-- Title -->
                <div class="col-12">
                  <label for="title" class="form-label text-light fw-semibold">Event Title *</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text bg-transparent border-secondary">
                      <i class="bi bi-calendar-event text-primary"></i>
                    </span>
                    <input
                      id="title"
                      v-model="form.title"
                      type="text"
                      required
                      class="form-control form-control-lg bg-transparent border-secondary text-light"
                      :disabled="updating"
                    />
                  </div>
                </div>

                <!-- Category -->
                <div class="col-md-6">
                  <label for="category" class="form-label text-light fw-semibold">Category *</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text bg-transparent border-secondary">
                      <i class="bi bi-tags text-primary"></i>
                    </span>
                    <select
                      id="category"
                      v-model="form.category"
                      required
                      class="form-select form-select-lg bg-transparent border-secondary text-light"
                      :disabled="updating"
                    >
                      <option value="" class="text-dark">Select category</option>
                      <option value="Conference" class="text-dark">Conference</option>
                      <option value="Workshop" class="text-dark">Workshop</option>
                      <option value="Concert" class="text-dark">Concert</option>
                      <option value="Sports" class="text-dark">Sports</option>
                      <option value="Arts" class="text-dark">Arts</option>
                      <option value="Food" class="text-dark">Food</option>
                      <option value="Technology" class="text-dark">Technology</option>
                      <option value="Business" class="text-dark">Business</option>
                      <option value="Other" class="text-dark">Other</option>
                    </select>
                  </div>
                </div>

                <!-- Price -->
                <div class="col-md-6">
                  <label for="price" class="form-label text-light fw-semibold">Price ($) *</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text bg-transparent border-secondary">
                      <i class="bi bi-currency-dollar text-primary"></i>
                    </span>
                    <input
                      id="price"
                      v-model.number="form.price"
                      type="number"
                      min="0"
                      step="0.01"
                      required
                      class="form-control form-control-lg bg-transparent border-secondary text-light"
                      :disabled="updating"
                    />
                  </div>
                </div>

                <!-- Description -->
                <div class="col-12">
                  <label for="description" class="form-label text-light fw-semibold">Description *</label>
                  <div class="position-relative">
                    <textarea
                      id="description"
                      v-model="form.description"
                      rows="5"
                      required
                      class="form-control form-control-lg bg-transparent border-secondary text-light"
                      placeholder="Describe your event in detail... What makes it special?"
                      :disabled="updating"
                      style="resize: vertical; min-height: 120px;"
                    ></textarea>
                    <div class="position-absolute top-0 end-0 mt-2 me-2">
                      <i class="bi bi-chat-text text-primary opacity-50"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Location & Timing Section -->
            <div class="mb-5">
              <div class="d-flex align-items-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 me-3" 
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                  <i class="bi bi-geo-alt text-white fs-5"></i>
                </div>
                <h3 class="fw-bold text-light mb-0 professional-title">Location & Timing</h3>
              </div>
              <div class="row g-4">
                <!-- Location -->
                <div class="col-12">
                  <label for="location" class="form-label text-light fw-semibold">Location *</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text bg-transparent border-secondary">
                      <i class="bi bi-geo-alt-fill text-primary"></i>
                    </span>
                    <input
                      id="location"
                      v-model="form.location"
                      type="text"
                      required
                      class="form-control form-control-lg bg-transparent border-secondary text-light"
                      :disabled="updating"
                    />
                  </div>
                </div>

                <!-- Event Date -->
                <div class="col-md-6">
                  <label for="event_date" class="form-label text-light fw-semibold">Event Date & Time *</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text bg-transparent border-secondary">
                      <i class="bi bi-calendar-date text-primary"></i>
                    </span>
                    <input
                      id="event_date"
                      v-model="form.event_date"
                      type="datetime-local"
                      required
                      class="form-control form-control-lg bg-transparent border-secondary text-light"
                      :disabled="updating"
                    />
                  </div>
                </div>

                <!-- Registration Deadline -->
                <div class="col-md-6">
                  <label for="registration_deadline" class="form-label text-light fw-semibold">Registration Deadline *</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text bg-transparent border-secondary">
                      <i class="bi bi-clock text-primary"></i>
                    </span>
                    <input
                      id="registration_deadline"
                      v-model="form.registration_deadline"
                      type="datetime-local"
                      required
                      class="form-control form-control-lg bg-transparent border-secondary text-light"
                      :disabled="updating"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Event Settings Section -->
            <div class="mb-5">
              <div class="d-flex align-items-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 me-3" 
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                  <i class="bi bi-gear text-white fs-5"></i>
                </div>
                <h3 class="fw-bold text-light mb-0 professional-title">Event Settings</h3>
              </div>
              <div class="row g-4">
                <!-- Max Attendees -->
                <div class="col-md-6">
                  <label for="max_attendees" class="form-label text-light fw-semibold">Maximum Attendees *</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text bg-transparent border-secondary">
                      <i class="bi bi-people text-primary"></i>
                    </span>
                    <input
                      id="max_attendees"
                      v-model.number="form.max_attendees"
                      type="number"
                      min="1"
                      required
                      class="form-control form-control-lg bg-transparent border-secondary text-light"
                      :disabled="updating"
                    />
                  </div>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                  <label for="status" class="form-label text-light fw-semibold">Status</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text bg-transparent border-secondary">
                      <i class="bi bi-flag text-primary"></i>
                    </span>
                    <select
                      id="status"
                      v-model="form.status"
                      class="form-select form-select-lg bg-transparent border-secondary text-light"
                      :disabled="updating"
                    >
                      <option value="active" class="text-dark">Active</option>
                      <option value="cancelled" class="text-dark">Cancelled</option>
                      <option value="completed" class="text-dark">Completed</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Image Management Section -->
            <div class="mb-5">
              <div class="d-flex align-items-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 me-3" 
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                  <i class="bi bi-image text-white fs-5"></i>
                </div>
                <h3 class="fw-bold text-light mb-0 professional-title">Event Image</h3>
              </div>

              <!-- Current Image -->
              <div v-if="event.image_url" class="mb-4">
                <label class="form-label text-light fw-semibold">Current Image</label>
                <div class="mb-3">
                  <img
                    :src="event.image_url"
                    :alt="event.title"
                    class="rounded-3 shadow-lg"
                    style="width: 200px; height: 150px; object-fit: cover; border: 2px solid rgba(255, 255, 255, 0.1);"
                  />
                </div>
              </div>

              <!-- Image Upload -->
              <div class="mb-3">
                <label for="image" class="form-label text-light fw-semibold">
                  <i class="bi bi-cloud-upload me-2"></i>
                  {{ event.image_url ? 'Replace Image' : 'Event Image' }}
                </label>
                <div class="input-group input-group-lg">
                  <span class="input-group-text bg-transparent border-secondary">
                    <i class="bi bi-image-fill text-primary"></i>
                  </span>
                  <input
                    id="image"
                    ref="imageInput"
                    type="file"
                    accept="image/*"
                    class="form-control form-control-lg bg-transparent border-secondary text-light"
                    :disabled="updating"
                    @change="handleImageChange"
                  />
                </div>
                <div class="form-text text-light opacity-75 mt-2">
                  <i class="bi bi-info-circle me-1"></i>
                  Upload an attractive image to showcase your event (max 2MB)
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="d-flex gap-3 justify-content-center mt-5 pt-4 border-top border-secondary">
              <RouterLink
                to="/dashboard"
                class="btn btn-outline-light btn-lg px-4"
              >
                <i class="bi bi-arrow-left me-2"></i>
                Cancel
              </RouterLink>
              <button
                type="submit"
                :disabled="updating || !isFormValid"
                class="btn btn-primary btn-lg px-5 d-flex align-items-center position-relative overflow-hidden"
                style="min-width: 180px;"
              >
                <div class="position-absolute w-100 h-100 top-0 start-0 bg-gradient" 
                     style="background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%); 
                            animation: shimmer 2s infinite;"></div>
                <div v-if="updating" class="spinner-border spinner-border-sm me-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <i v-else class="bi bi-pencil me-2"></i>
                {{ updating ? 'Updating Event...' : 'Update Event' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Event Not Found -->
      <div v-else class="text-center py-5">
        <h2 class="h4 fw-bold text-muted mb-4 professional-title">Event Not Found</h2>
        <p class="text-muted mb-4">The event you're trying to edit doesn't exist or you don't have permission to edit it.</p>
        <RouterLink to="/dashboard" class="btn btn-primary">
          Back to Dashboard
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useEventsStore, type Event as EventType } from '@/stores/events'

const route = useRoute()
const router = useRouter()
const eventsStore = useEventsStore()

const loading = ref(true)
const updating = ref(false)
const error = ref('')
const event = ref<EventType | null>(null)
const imageInput = ref<HTMLInputElement>()

const form = reactive({
  title: '',
  description: '',
  category: '',
  location: '',
  event_date: '',
  registration_deadline: '',
  price: 0,
  max_attendees: 100,
  status: 'active',
  image: null as File | null
})

const isFormValid = computed(() => {
  return (
    form.title.trim() !== '' &&
    form.description.trim() !== '' &&
    form.category !== '' &&
    form.location.trim() !== '' &&
    form.event_date !== '' &&
    form.registration_deadline !== '' &&
    form.price >= 0 &&
    form.max_attendees > 0
  )
})

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (file) {
    if (file.size > 2 * 1024 * 1024) { // 2MB limit
      error.value = 'Image file size must be less than 2MB'
      target.value = ''
      return
    }
    form.image = file
  }
}

const handleSubmit = async () => {
  if (!isFormValid.value) return

  updating.value = true
  error.value = ''

  try {
    const formData = new FormData()
    
    // Append all form fields
    Object.entries(form).forEach(([key, value]) => {
      if (key === 'image' && value) {
        formData.append('image', value as File)
      } else if (key !== 'image' && value !== null) {
        formData.append(key, String(value))
      }
    })

    if (!event.value) return
    
    const result = await eventsStore.updateEvent(event.value.id, formData)
    
    if (result.success) {
      router.push('/dashboard')
    } else {
      error.value = result.message || 'Failed to update event'
    }
  } catch (err: any) {
    error.value = 'An unexpected error occurred'
  } finally {
    updating.value = false
  }
}

const formatDateForInput = (dateString: string) => {
  const date = new Date(dateString)
  return date.toISOString().slice(0, 16)
}

onMounted(async () => {
  const eventId = route.params.id as string
  const result = await eventsStore.fetchEvent(parseInt(eventId))
  
  if (result.success && result.data) {
    const eventData = result.data
    event.value = eventData
    
    // Populate form with event data
    Object.assign(form, {
      title: eventData.title,
      description: eventData.description,
      category: eventData.category,
      location: eventData.location,
      event_date: formatDateForInput(eventData.event_date),
      registration_deadline: formatDateForInput(eventData.registration_deadline),
      price: eventData.price,
      max_attendees: eventData.max_attendees,
      status: eventData.status
    })
  }
  
  loading.value = false
})
</script>

<style scoped>
/* Floating shapes animation */
.floating-shapes {
  position: absolute;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.shape {
  position: absolute;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(29, 78, 216, 0.05) 100%);
  border-radius: 50%;
  animation: float 6s ease-in-out infinite;
}

.shape-1 {
  width: 80px;
  height: 80px;
  top: 20%;
  left: 10%;
  animation-delay: 0s;
}

.shape-2 {
  width: 60px;
  height: 60px;
  top: 60%;
  right: 15%;
  animation-delay: 2s;
}

.shape-3 {
  width: 100px;
  height: 100px;
  bottom: 20%;
  left: 20%;
  animation-delay: 4s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
    opacity: 0.5;
  }
  50% {
    transform: translateY(-20px) rotate(180deg);
    opacity: 0.8;
  }
}

/* Glow ring animation */
.glow-ring {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(29, 78, 216, 0.3) 100%);
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
  border-color: #3b82f6 !important;
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25) !important;
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
  background: linear-gradient(45deg, #3b82f6, #1d4ed8, #3b82f6);
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
</style>