<template>
  <div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <EnvelopeIcon class="w-16 h-16 text-green-500 mx-auto mb-4" />
        <h1 class="text-4xl font-bold text-white mb-4">Contact Support</h1>
        <p class="text-gray-300 text-lg">
          Have questions or need help? We're here to assist you!
        </p>
      </div>

      <!-- Contact Form -->
      <div class="card p-8">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Success Message -->
          <div v-if="submitted" class="bg-green-900/50 border border-green-700 rounded-lg p-4">
            <div class="flex">
              <CheckCircleIcon class="w-5 h-5 text-green-400 mr-2 flex-shrink-0 mt-0.5" />
              <div>
                <p class="text-green-300 text-sm font-medium">Message sent successfully!</p>
                <p class="text-green-200 text-sm mt-1">We'll get back to you as soon as possible.</p>
              </div>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="bg-red-900/50 border border-red-700 rounded-lg p-4">
            <div class="flex">
              <ExclamationTriangleIcon class="w-5 h-5 text-red-400 mr-2 flex-shrink-0 mt-0.5" />
              <p class="text-red-300 text-sm">{{ error }}</p>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Subject -->
            <div>
              <label for="subject" class="form-label">Subject</label>
              <select
                id="subject"
                v-model="form.subject"
                class="form-input"
                required
                :disabled="loading"
              >
                <option value="">Select a subject</option>
                <option value="General Inquiry">General Inquiry</option>
                <option value="Technical Support">Technical Support</option>
                <option value="Event Management">Event Management</option>
                <option value="Ticket Issues">Ticket Issues</option>
                <option value="Payment Problems">Payment Problems</option>
                <option value="Account Issues">Account Issues</option>
                <option value="Feature Request">Feature Request</option>
                <option value="Bug Report">Bug Report</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <!-- Custom Subject (if Other is selected) -->
            <div v-if="form.subject === 'Other'">
              <label for="customSubject" class="form-label">Custom Subject</label>
              <input
                id="customSubject"
                v-model="form.customSubject"
                type="text"
                class="form-input"
                placeholder="Please specify your subject"
                required
                :disabled="loading"
              />
            </div>

            <!-- Message -->
            <div>
              <label for="message" class="form-label">Message</label>
              <textarea
                id="message"
                v-model="form.message"
                rows="6"
                class="form-input resize-none"
                placeholder="Please describe your issue or question in detail..."
                required
                :disabled="loading"
              ></textarea>
              <p class="mt-1 text-xs text-gray-400">
                {{ form.message.length }}/2000 characters
              </p>
            </div>

            <!-- User Info (if not authenticated) -->
            <div v-if="!authStore.isAuthenticated" class="space-y-4">
              <div>
                <label for="name" class="form-label">Your Name</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="form-input"
                  placeholder="Enter your full name"
                  required
                  :disabled="loading"
                />
              </div>

              <div>
                <label for="email" class="form-label">Your Email</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="form-input"
                  placeholder="Enter your email address"
                  required
                  :disabled="loading"
                />
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div>
            <button
              type="submit"
              :disabled="loading || !isFormValid"
              class="btn btn-primary w-full flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div v-if="loading" class="spinner mr-2"></div>
              <PaperAirplaneIcon v-else class="w-5 h-5 mr-2" />
              {{ loading ? 'Sending...' : 'Send Message' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Contact Info -->
      <div class="mt-8 text-center">
        <h3 class="text-lg font-semibold text-white mb-4">Other Ways to Reach Us</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="card p-4">
            <EnvelopeIcon class="w-6 h-6 text-green-500 mx-auto mb-2" />
            <h4 class="font-medium text-white">Email</h4>
            <p class="text-gray-400 text-sm">support@easypass.com</p>
          </div>
          <div class="card p-4">
            <ClockIcon class="w-6 h-6 text-green-500 mx-auto mb-2" />
            <h4 class="font-medium text-white">Response Time</h4>
            <p class="text-gray-400 text-sm">Within 24 hours</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import {
  EnvelopeIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  PaperAirplaneIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()

const loading = ref(false)
const submitted = ref(false)
const error = ref('')

const form = reactive({
  subject: '',
  customSubject: '',
  message: '',
  name: authStore.userName || '',
  email: authStore.userEmail || ''
})

const isFormValid = computed(() => {
  const hasSubject = form.subject && (form.subject !== 'Other' || form.customSubject.trim())
  const hasMessage = form.message.trim() && form.message.length <= 2000
  const hasContactInfo = authStore.isAuthenticated || (form.name.trim() && form.email.trim())
  
  return hasSubject && hasMessage && hasContactInfo
})

const handleSubmit = async () => {
  if (!isFormValid.value) return

  loading.value = true
  error.value = ''

  try {
    const subject = form.subject === 'Other' ? form.customSubject : form.subject
    const message = `Name: ${form.name || authStore.userName}
Email: ${form.email || authStore.userEmail}

Message:
${form.message}`

    const response = await axios.post('/api/contact', {
      subject,
      message
    })

    if (response.data.status === 'success') {
      submitted.value = true
      // Reset form
      Object.assign(form, {
        subject: '',
        customSubject: '',
        message: '',
        name: authStore.userName || '',
        email: authStore.userEmail || ''
      })
    } else {
      error.value = response.data.message || 'Failed to send message'
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to send message'
  } finally {
    loading.value = false
  }
}
</script>
