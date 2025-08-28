<template>
  <div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Edit Event</h1>
        <p class="text-gray-300">Update your event details</p>
      </div>

      <div v-if="loading" class="flex justify-center py-12">
        <div class="spinner"></div>
      </div>

      <div v-else-if="event" class="card p-8">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Error Message -->
          <div v-if="error" class="bg-red-900/50 border border-red-700 rounded-lg p-4">
            <div class="flex">
              <svg class="w-5 h-5 text-red-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <p class="text-red-300 text-sm">{{ error }}</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
              <label for="title" class="form-label">Event Title *</label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                required
                class="form-input"
                :disabled="updating"
              />
            </div>

            <!-- Category -->
            <div>
              <label for="category" class="form-label">Category *</label>
              <select
                id="category"
                v-model="form.category"
                required
                class="form-input"
                :disabled="updating"
              >
                <option value="">Select category</option>
                <option value="Conference">Conference</option>
                <option value="Workshop">Workshop</option>
                <option value="Concert">Concert</option>
                <option value="Sports">Sports</option>
                <option value="Arts">Arts</option>
                <option value="Food">Food</option>
                <option value="Technology">Technology</option>
                <option value="Business">Business</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <!-- Price -->
            <div>
              <label for="price" class="form-label">Price ($) *</label>
              <input
                id="price"
                v-model.number="form.price"
                type="number"
                min="0"
                step="0.01"
                required
                class="form-input"
                :disabled="updating"
              />
            </div>

            <!-- Location -->
            <div class="md:col-span-2">
              <label for="location" class="form-label">Location *</label>
              <input
                id="location"
                v-model="form.location"
                type="text"
                required
                class="form-input"
                :disabled="updating"
              />
            </div>

            <!-- Event Date -->
            <div>
              <label for="event_date" class="form-label">Event Date & Time *</label>
              <input
                id="event_date"
                v-model="form.event_date"
                type="datetime-local"
                required
                class="form-input"
                :disabled="updating"
              />
            </div>

            <!-- Registration Deadline -->
            <div>
              <label for="registration_deadline" class="form-label">Registration Deadline *</label>
              <input
                id="registration_deadline"
                v-model="form.registration_deadline"
                type="datetime-local"
                required
                class="form-input"
                :disabled="updating"
              />
            </div>

            <!-- Max Attendees -->
            <div>
              <label for="max_attendees" class="form-label">Maximum Attendees *</label>
              <input
                id="max_attendees"
                v-model.number="form.max_attendees"
                type="number"
                min="1"
                required
                class="form-input"
                :disabled="updating"
              />
            </div>

            <!-- Status -->
            <div>
              <label for="status" class="form-label">Status</label>
              <select
                id="status"
                v-model="form.status"
                class="form-input"
                :disabled="updating"
              >
                <option value="active">Active</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
              </select>
            </div>
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="form-label">Description *</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              required
              class="form-input resize-none"
              :disabled="updating"
            ></textarea>
          </div>

          <!-- Current Image -->
          <div v-if="event.image_url">
            <label class="form-label">Current Image</label>
            <div class="mb-4">
              <img
                :src="event.image_url"
                :alt="event.title"
                class="w-32 h-32 object-cover rounded-lg"
              />
            </div>
          </div>

          <!-- Image Upload -->
          <div>
            <label for="image" class="form-label">
              {{ event.image_url ? 'Replace Image' : 'Event Image' }}
            </label>
            <input
              id="image"
              ref="imageInput"
              type="file"
              accept="image/*"
              class="form-input"
              :disabled="updating"
              @change="handleImageChange"
            />
            <p class="text-xs text-gray-400 mt-1">
              Upload an image to make your event more attractive (max 2MB)
            </p>
          </div>

          <!-- Submit Button -->
          <div class="flex space-x-4">
            <button
              type="submit"
              :disabled="updating || !isFormValid"
              class="btn btn-primary flex-1 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div v-if="updating" class="spinner mr-2"></div>
              <svg v-else class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
              </svg>
              {{ updating ? 'Updating Event...' : 'Update Event' }}
            </button>

            <RouterLink
              to="/dashboard"
              class="btn btn-secondary flex-1 text-center"
            >
              Cancel
            </RouterLink>
          </div>
        </form>
      </div>

      <div v-else class="text-center py-12">
        <h2 class="text-2xl font-bold text-gray-400 mb-4">Event Not Found</h2>
        <p class="text-gray-500 mb-6">The event you're trying to edit doesn't exist or you don't have permission to edit it.</p>
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
import { useEventsStore } from '@/stores/events'

const route = useRoute()
const router = useRouter()
const eventsStore = useEventsStore()

const loading = ref(true)
const updating = ref(false)
const error = ref('')
const event = ref(null)
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
        formData.append('image', value)
      } else if (key !== 'image') {
        formData.append(key, value.toString())
      }
    })

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
  
  if (result.success) {
    event.value = result.data
    
    // Populate form with event data
    Object.assign(form, {
      title: event.value.title,
      description: event.value.description,
      category: event.value.category,
      location: event.value.location,
      event_date: formatDateForInput(event.value.event_date),
      registration_deadline: formatDateForInput(event.value.registration_deadline),
      price: event.value.price,
      max_attendees: event.value.max_attendees,
      status: event.value.status
    })
  }
  
  loading.value = false
})
</script>
