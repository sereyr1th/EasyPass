<template>
  <div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Create New Event</h1>
        <p class="text-gray-300">Fill in the details below to create your event</p>
      </div>

      <!-- Form -->
      <div class="card p-8">
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
                placeholder="Enter event title"
                :disabled="loading"
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
                :disabled="loading"
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
                placeholder="0.00"
                :disabled="loading"
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
                placeholder="Enter event location"
                :disabled="loading"
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
                :disabled="loading"
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
                :disabled="loading"
              />
            </div>

            <!-- Max Attendees -->
            <div class="md:col-span-2">
              <label for="max_attendees" class="form-label">Maximum Attendees *</label>
              <input
                id="max_attendees"
                v-model.number="form.max_attendees"
                type="number"
                min="1"
                required
                class="form-input"
                placeholder="100"
                :disabled="loading"
              />
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
              placeholder="Describe your event..."
              :disabled="loading"
            ></textarea>
          </div>

          <!-- Image Upload -->
          <div>
            <label for="image" class="form-label">Event Image</label>
            <input
              id="image"
              ref="imageInput"
              type="file"
              accept="image/*"
              class="form-input"
              :disabled="loading"
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
              :disabled="loading || !isFormValid"
              class="btn btn-primary flex-1 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div v-if="loading" class="spinner mr-2"></div>
              <svg v-else class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              {{ loading ? 'Creating Event...' : 'Create Event' }}
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
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useEventsStore } from '@/stores/events'

const eventsStore = useEventsStore()
const router = useRouter()

const loading = ref(false)
const error = ref('')
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
    form.max_attendees > 0 &&
    new Date(form.event_date) > new Date() &&
    new Date(form.registration_deadline) > new Date() &&
    new Date(form.registration_deadline) < new Date(form.event_date)
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

  loading.value = true
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

    const result = await eventsStore.createEvent(formData)
    
    if (result.success) {
      router.push('/dashboard')
    } else {
      error.value = result.message || 'Failed to create event'
    }
  } catch (err: any) {
    error.value = 'An unexpected error occurred'
  } finally {
    loading.value = false
  }
}
</script>
