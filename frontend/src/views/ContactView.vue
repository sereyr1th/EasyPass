<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container" style="max-width: 1000px;">
      <!-- Header -->
      <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-4 glow-animation"
             style="width: 120px; height: 120px; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
          <i class="bi bi-envelope-heart fs-1 text-white"></i>
        </div>
        <h1 class="display-4 fw-bold text-light mb-3 professional-title">Contact & Support</h1>
        <p class="lead text-muted">
          Need assistance? Our support team is here to help you with any questions or issues.
        </p>
      </div>

      <!-- Contact Form -->
      <div class="card mb-4">
        <div class="card-body">
          <h2 class="h4 fw-bold text-light mb-4 professional-title">
            <i class="bi bi-chat-dots me-2"></i>Send us a Message
          </h2>

          <form @submit.prevent="handleSubmit">
            <!-- Success Message -->
            <div v-if="submitted" class="alert alert-success d-flex align-items-center mb-4">
              <i class="bi bi-check-circle-fill me-2"></i>
              <div>
                <div class="fw-semibold">Message sent successfully!</div>
                <small>We'll get back to you within 24 hours.</small>
              </div>
            </div>

            <!-- Error Message -->
            <div v-if="error" class="alert alert-danger d-flex align-items-center mb-4">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <div>{{ error }}</div>
            </div>

            <div class="row g-3">
              <!-- Subject -->
              <div class="col-12">
                <label for="subject" class="form-label">
                  <i class="bi bi-tag me-2"></i>Subject Category
                </label>
                <select
                  id="subject"
                  v-model="form.subject"
                  class="form-select"
                  required
                  :disabled="loading"
                >
                  <option value="">Select a subject category</option>
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
              <div v-if="form.subject === 'Other'" class="col-12">
                <label for="customSubject" class="form-label">Custom Subject</label>
                <input
                  id="customSubject"
                  v-model="form.customSubject"
                  type="text"
                  class="form-control"
                  placeholder="Please specify your subject"
                  required
                  :disabled="loading"
                />
              </div>

              <!-- Priority Level -->
              <div class="col-md-6">
                <label for="priority" class="form-label">
                  <i class="bi bi-exclamation-triangle me-2"></i>Priority Level
                </label>
                <select
                  id="priority"
                  v-model="form.priority"
                  class="form-select"
                  required
                  :disabled="loading"
                >
                  <option value="low">Low - General question</option>
                  <option value="medium" selected>Medium - Need assistance</option>
                  <option value="high">High - Urgent issue</option>
                  <option value="critical">Critical - Service disruption</option>
                </select>
              </div>

              <!-- User Info (if not authenticated) -->
              <template v-if="!authStore.isAuthenticated">
                <div class="col-md-6">
                  <label for="name" class="form-label">Your Name</label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="form-control"
                    placeholder="Enter your full name"
                    required
                    :disabled="loading"
                  />
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">Your Email</label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    placeholder="Enter your email address"
                    required
                    :disabled="loading"
                  />
                </div>
              </template>

              <!-- Message -->
              <div class="col-12">
                <label for="message" class="form-label">
                  <i class="bi bi-chat-text me-2"></i>Your Message
                </label>
                <textarea
                  id="message"
                  v-model="form.message"
                  rows="6"
                  class="form-control"
                  placeholder="Please describe your issue or question in detail. Include any relevant information that might help us assist you better..."
                  required
                  :disabled="loading"
                  style="resize: vertical;"
                ></textarea>
                <div class="form-text d-flex justify-content-between">
                  <small class="text-muted">Be as detailed as possible to help us provide better support</small>
                  <small class="text-muted">{{ form.message.length }}/2000 characters</small>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
              <button
                type="submit"
                :disabled="loading || !isFormValid"
                class="btn btn-primary w-100 d-flex align-items-center justify-content-center"
              >
                <div v-if="loading" class="spinner-border spinner-border-sm me-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <i v-else class="bi bi-send me-2"></i>
                {{ loading ? 'Sending Message...' : 'Send Message' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Support Resources -->
      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="card h-100 text-center">
            <div class="card-body">
              <div class="d-inline-flex align-items-center justify-content-center rounded-3 mx-auto mb-3 glow-animation"
                   style="width: 64px; height: 64px; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <i class="bi bi-envelope fs-2 text-white"></i>
              </div>
              <h3 class="h5 fw-bold text-light mb-2 professional-title">Email Support</h3>
              <p class="text-muted mb-2">support@easypass-ems.com</p>
              <small class="text-success">Response within 24 hours</small>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card h-100 text-center">
            <div class="card-body">
              <div class="d-inline-flex align-items-center justify-content-center rounded-3 mx-auto mb-3 glow-animation"
                   style="width: 64px; height: 64px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                <i class="bi bi-clock fs-2 text-white"></i>
              </div>
              <h3 class="h5 fw-bold text-light mb-2 professional-title">Support Hours</h3>
              <p class="text-muted mb-2">Monday - Friday</p>
              <small class="text-success">9:00 AM - 6:00 PM PST</small>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card h-100 text-center">
            <div class="card-body">
              <div class="d-inline-flex align-items-center justify-content-center rounded-3 mx-auto mb-3 glow-animation"
                   style="width: 64px; height: 64px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                <i class="bi bi-question-circle fs-2 text-white"></i>
              </div>
              <h3 class="h5 fw-bold text-light mb-2 professional-title">FAQ & Docs</h3>
              <p class="text-muted mb-2">Self-service resources</p>
              <a href="#" class="text-primary text-decoration-none small">Browse Help Center →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Common Issues -->
      <div class="card">
        <div class="card-body">
          <h3 class="h5 fw-bold text-light mb-4 professional-title">
            <i class="bi bi-lightbulb me-2"></i>Common Issues & Quick Solutions
          </h3>
          <div class="row g-4">
            <div class="col-md-6">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                       style="width: 40px; height: 40px; background: rgba(34, 197, 94, 0.2);">
                    <i class="bi bi-ticket-perforated text-success"></i>
                  </div>
                </div>
                <div>
                  <h4 class="h6 text-light mb-1">Ticket Issues</h4>
                  <p class="text-muted small mb-2">Can't find your ticket or QR code not working?</p>
                  <a href="/tickets" class="text-primary text-decoration-none small">View My Tickets →</a>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                       style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.2);">
                    <i class="bi bi-credit-card text-primary"></i>
                  </div>
                </div>
                <div>
                  <h4 class="h6 text-light mb-1">Payment Problems</h4>
                  <p class="text-muted small mb-2">Issues with payment or refunds?</p>
                  <a href="/payments" class="text-primary text-decoration-none small">Payment History →</a>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                       style="width: 40px; height: 40px; background: rgba(168, 85, 247, 0.2);">
                    <i class="bi bi-person-gear text-info"></i>
                  </div>
                </div>
                <div>
                  <h4 class="h6 text-light mb-1">Account Settings</h4>
                  <p class="text-muted small mb-2">Need to update your profile or password?</p>
                  <a href="/profile" class="text-primary text-decoration-none small">Manage Profile →</a>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="d-inline-flex align-items-center justify-content-center rounded-2"
                       style="width: 40px; height: 40px; background: rgba(245, 101, 101, 0.2);">
                    <i class="bi bi-calendar-event text-danger"></i>
                  </div>
                </div>
                <div>
                  <h4 class="h6 text-light mb-1">Event Management</h4>
                  <p class="text-muted small mb-2">Creating or managing events?</p>
                  <a href="/dashboard" class="text-primary text-decoration-none small">Event Dashboard →</a>
                </div>
              </div>
            </div>
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

const authStore = useAuthStore()

const loading = ref(false)
const submitted = ref(false)
const error = ref('')

const form = reactive({
  subject: '',
  customSubject: '',
  priority: 'medium',
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
Priority: ${form.priority.toUpperCase()}

Message:
${form.message}`

    const response = await axios.post('/api/contact', {
      subject,
      message,
      priority: form.priority
    })

    if (response.data.status === 'success') {
      submitted.value = true
      // Reset form
      Object.assign(form, {
        subject: '',
        customSubject: '',
        priority: 'medium',
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
