<template>
  <div class="min-vh-100 position-relative overflow-hidden auth-page" style="padding-top: 80px;">
    <!-- Animated Background -->
    <div class="position-absolute w-100 h-100 top-0 start-0" style="z-index: -1;">
      <div class="position-absolute w-100 h-100 auth-background"></div>
      <div class="position-absolute w-100 h-100 top-0 start-0 auth-overlay"></div>
      <div class="position-absolute top-0 start-0 w-100 h-100">
        <div class="floating-particles">
          <div class="particle particle-1"></div>
          <div class="particle particle-2"></div>
          <div class="particle particle-3"></div>
          <div class="particle particle-4"></div>
          <div class="particle particle-5"></div>
          <div class="particle particle-6"></div>
        </div>
      </div>
    </div>

    <div class="container position-relative d-flex align-items-center justify-content-center" style="z-index: 2; min-height: 100vh; padding-top: 50px; padding-bottom: 50px;">
      <div class="row justify-content-center w-100">
        <div class="col-lg-6 col-md-8 col-sm-10">
          <!-- Header -->
          <div class="text-center mb-4">
            <div class="position-relative d-inline-block mb-4">
              <div class="d-inline-flex align-items-center justify-content-center rounded-4 mx-auto glow-animation shadow-lg" 
                   style="width: 90px; height: 90px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                <span class="text-white fw-bold display-6">E</span>
              </div>
              <div class="position-absolute top-0 start-0 w-100 h-100 rounded-4 glow-ring"></div>
            </div>
            <h1 class="display-4 fw-bold text-light mb-3 professional-title">Join EasyPass</h1>
            <p class="lead text-light opacity-75 mb-0" style="font-size: 1.1rem;">
              Create your account and start managing events today
            </p>
          </div>

          <!-- Registration Form -->
          <div class="card border-0 shadow-lg" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.05);">
            <div class="card-body p-4">
              <form @submit.prevent="handleRegister">
                <!-- Error Message -->
                <div v-if="authStore.error" class="alert alert-danger d-flex align-items-center mb-4">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>
                  <div>{{ authStore.error }}</div>
                </div>

                <div class="row g-3">
                  <!-- Name Field -->
                  <div class="col-12">
                    <label for="name" class="form-label text-light fw-semibold">Full Name</label>
                    <div class="input-group input-group-lg">
                      <span class="input-group-text bg-transparent border-secondary">
                        <i class="bi bi-person text-primary"></i>
                      </span>
                      <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        autocomplete="name"
                        required
                        class="form-control form-control-lg bg-transparent border-secondary text-light"
                        placeholder="Enter your full name"
                        :disabled="authStore.loading"
                      />
                    </div>
                  </div>

                  <!-- Email Field -->
                  <div class="col-12">
                    <label for="email" class="form-label text-light fw-semibold">Email Address</label>
                    <div class="input-group input-group-lg">
                      <span class="input-group-text bg-transparent border-secondary">
                        <i class="bi bi-envelope text-primary"></i>
                      </span>
                      <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        required
                        class="form-control form-control-lg bg-transparent border-secondary text-light"
                        placeholder="Enter your email"
                        :disabled="authStore.loading"
                      />
                    </div>
                  </div>

                  <!-- Password Field -->
                  <div class="col-md-6">
                    <label for="password" class="form-label text-light fw-semibold">Password</label>
                    <div class="input-group input-group-lg">
                      <span class="input-group-text bg-transparent border-secondary">
                        <i class="bi bi-lock text-primary"></i>
                      </span>
                      <input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="new-password"
                        required
                        class="form-control form-control-lg bg-transparent border-secondary text-light"
                        placeholder="Create password"
                        :disabled="authStore.loading"
                      />
                      <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="btn btn-outline-secondary border-start-0"
                        style="border-color: #6b7280 !important;"
                      >
                        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'" class="text-primary"></i>
                      </button>
                    </div>
                    <div class="form-text text-light opacity-50 mt-1">
                      <small>At least 8 characters</small>
                    </div>
                  </div>

                  <!-- Confirm Password Field -->
                  <div class="col-md-6">
                    <label for="password_confirmation" class="form-label text-light fw-semibold">Confirm Password</label>
                    <div class="input-group input-group-lg">
                      <span class="input-group-text bg-transparent border-secondary">
                        <i class="bi bi-shield-check text-primary"></i>
                      </span>
                      <input
                        id="password_confirmation"
                        v-model="form.passwordConfirmation"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        autocomplete="new-password"
                        required
                        class="form-control form-control-lg bg-transparent border-secondary text-light"
                        placeholder="Confirm password"
                        :disabled="authStore.loading"
                      />
                      <button
                        type="button"
                        @click="showConfirmPassword = !showConfirmPassword"
                        class="btn btn-outline-secondary border-start-0"
                        style="border-color: #6b7280 !important;"
                      >
                        <i :class="showConfirmPassword ? 'bi bi-eye-slash' : 'bi bi-eye'" class="text-primary"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Password Match Indicator -->
                <div v-if="form.password && form.passwordConfirmation" class="mt-3">
                  <div v-if="form.password === form.passwordConfirmation" class="d-flex align-items-center text-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <small>Passwords match</small>
                  </div>
                  <div v-else class="d-flex align-items-center text-danger">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    <small>Passwords don't match</small>
                  </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="mt-4">
                  <div class="form-check">
                    <input
                      id="terms"
                      v-model="form.acceptTerms"
                      type="checkbox"
                      required
                      class="form-check-input"
                    />
                    <label for="terms" class="form-check-label text-light">
                      I agree to the
                      <a href="#" class="text-primary text-decoration-none">Terms of Service</a>
                      and
                      <a href="#" class="text-primary text-decoration-none">Privacy Policy</a>
                    </label>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mt-4 mb-4">
                  <button
                    type="submit"
                    :disabled="authStore.loading || !isFormValid"
                    class="btn btn-primary btn-lg d-flex align-items-center justify-content-center position-relative overflow-hidden"
                    style="min-height: 60px;"
                  >
                    <div class="position-absolute w-100 h-100 top-0 start-0 bg-gradient" 
                         style="background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%); 
                                animation: shimmer 2s infinite;"></div>
                    <div v-if="authStore.loading" class="spinner-border spinner-border-sm me-2" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    <i v-else class="bi bi-person-plus me-2 fs-5"></i>
                    {{ authStore.loading ? 'Creating Account...' : 'Create Account' }}
                  </button>
                </div>

                <!-- Sign In Link -->
                <div class="text-center">
                  <p class="text-light opacity-75 mb-0">
                    Already have an account?
                    <RouterLink
                      to="/auth/login"
                      class="text-primary text-decoration-none fw-semibold"
                    >
                      Sign in here
                    </RouterLink>
                  </p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const form = reactive({
  name: '',
  email: '',
  password: '',
  passwordConfirmation: '',
  acceptTerms: false
})

const isFormValid = computed(() => {
  return (
    form.name.trim() !== '' &&
    form.email.trim() !== '' &&
    form.password.length >= 8 &&
    form.password === form.passwordConfirmation &&
    form.acceptTerms
  )
})

const handleRegister = async () => {
  if (!isFormValid.value) return

  const result = await authStore.register(
    form.name,
    form.email,
    form.password,
    form.passwordConfirmation
  )
  
  if (result.success) {
    router.push('/dashboard')
  }
}
</script>

<style scoped>
/* Auth page background */
.auth-background {
  background-image: url('@/assets/images/concert-background.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  filter: blur(3px);
}

.auth-overlay {
  background: linear-gradient(
    135deg,
    rgba(0, 0, 0, 0.8) 0%,
    rgba(59, 130, 246, 0.2) 20%,
    rgba(29, 78, 216, 0.3) 50%,
    rgba(59, 130, 246, 0.2) 80%,
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
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(29, 78, 216, 0.1) 100%);
  border-radius: 50%;
  animation: floatParticle 12s ease-in-out infinite;
}

.particle-1 {
  width: 18px;
  height: 18px;
  top: 15%;
  left: 8%;
  animation-delay: 0s;
}

.particle-2 {
  width: 25px;
  height: 25px;
  top: 70%;
  right: 12%;
  animation-delay: 2s;
}

.particle-3 {
  width: 12px;
  height: 12px;
  bottom: 25%;
  left: 15%;
  animation-delay: 4s;
}

.particle-4 {
  width: 22px;
  height: 22px;
  top: 45%;
  right: 20%;
  animation-delay: 6s;
}

.particle-5 {
  width: 16px;
  height: 16px;
  bottom: 15%;
  right: 8%;
  animation-delay: 8s;
}

.particle-6 {
  width: 20px;
  height: 20px;
  top: 30%;
  left: 25%;
  animation-delay: 10s;
}

@keyframes floatParticle {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
    opacity: 0.2;
  }
  25% {
    transform: translateY(-35px) translateX(20px) rotate(90deg);
    opacity: 0.6;
  }
  50% {
    transform: translateY(-20px) translateX(-20px) rotate(180deg);
    opacity: 0.8;
  }
  75% {
    transform: translateY(-45px) translateX(15px) rotate(270deg);
    opacity: 0.4;
  }
}

/* Glow ring animation */
.glow-ring {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.4) 0%, rgba(29, 78, 216, 0.4) 100%);
  animation: pulse-ring 2.5s ease-in-out infinite;
}

@keyframes pulse-ring {
  0% {
    transform: scale(1);
    opacity: 0.7;
  }
  50% {
    transform: scale(1.15);
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
  box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.25) !important;
}

/* Professional title styling */
.professional-title {
  background: linear-gradient(135deg, #ffffff 0%, #e5e7eb 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
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
  animation: rotate 3s linear infinite;
  opacity: 0.8;
}

@keyframes rotate {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Form check styling */
.form-check-input:checked {
  background-color: #3b82f6;
  border-color: #3b82f6;
}

.form-check-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

/* Password match indicator animations */
.text-success {
  animation: fadeInSuccess 0.3s ease-in;
}

.text-danger {
  animation: fadeInError 0.3s ease-in;
}

@keyframes fadeInSuccess {
  from {
    opacity: 0;
    color: #6b7280;
  }
  to {
    opacity: 1;
    color: #22c55e;
  }
}

@keyframes fadeInError {
  from {
    opacity: 0;
    color: #6b7280;
  }
  to {
    opacity: 1;
    color: #ef4444;
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .auth-background {
    background-attachment: scroll;
  }
  
  .card-body {
    padding: 1.5rem !important;
  }
  
  .col-md-6 {
    margin-bottom: 1rem;
  }
}

/* Link hover effects */
a.text-primary:hover {
  color: #1d4ed8 !important;
  transition: color 0.3s ease;
}

/* Button hover effect */
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px -8px rgba(59, 130, 246, 0.5);
  transition: all 0.3s ease;
}

/* Disabled button styling */
.btn-primary:disabled {
  opacity: 0.6;
  transform: none;
  cursor: not-allowed;
}
</style>