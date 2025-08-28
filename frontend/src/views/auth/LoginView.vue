<template>
  <div class="min-vh-100 position-relative overflow-hidden auth-page" style="padding-top: 100px;">
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
        </div>
      </div>
    </div>

    <div class="container position-relative d-flex align-items-center justify-content-center" style="z-index: 2; min-height: 100vh;">
      <div class="row justify-content-center w-100">
        <div class="col-lg-5 col-md-7 col-sm-9">
          <!-- Header -->
          <div class="text-center mb-5">
            <div class="position-relative d-inline-block mb-4">
              <div class="d-inline-flex align-items-center justify-content-center rounded-4 mx-auto glow-animation shadow-lg" 
                   style="width: 100px; height: 100px; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <span class="text-white fw-bold display-5">E</span>
              </div>
              <div class="position-absolute top-0 start-0 w-100 h-100 rounded-4 glow-ring"></div>
            </div>
            <h1 class="display-4 fw-bold text-light mb-3 professional-title">Welcome Back</h1>
            <p class="lead text-light opacity-75 mb-0" style="font-size: 1.1rem;">
              Sign in to your EasyPass account
            </p>
          </div>

          <!-- Login Form -->
          <div class="card border-0 shadow-lg" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.05);">
            <div class="card-body p-5">
              <form @submit.prevent="handleLogin">
                <!-- Error Message -->
                <div v-if="authStore.error" class="alert alert-danger d-flex align-items-center mb-4">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>
                  <div>{{ authStore.error }}</div>
                </div>

                <!-- Email Field -->
                <div class="mb-4">
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
                <div class="mb-4">
                  <label for="password" class="form-label text-light fw-semibold">Password</label>
                  <div class="input-group input-group-lg">
                    <span class="input-group-text bg-transparent border-secondary">
                      <i class="bi bi-lock text-primary"></i>
                    </span>
                    <input
                      id="password"
                      v-model="form.password"
                      :type="showPassword ? 'text' : 'password'"
                      autocomplete="current-password"
                      required
                      class="form-control form-control-lg bg-transparent border-secondary text-light"
                      placeholder="Enter your password"
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
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <div class="form-check">
                    <input
                      id="remember-me"
                      v-model="form.rememberMe"
                      type="checkbox"
                      class="form-check-input"
                    />
                    <label for="remember-me" class="form-check-label text-light">
                      Remember me
                    </label>
                  </div>
                  <RouterLink
                    to="/auth/forgot-password"
                    class="text-primary text-decoration-none"
                  >
                    Forgot password?
                  </RouterLink>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-4">
                  <button
                    type="submit"
                    :disabled="authStore.loading"
                    class="btn btn-primary btn-lg d-flex align-items-center justify-content-center position-relative overflow-hidden"
                    style="min-height: 60px;"
                  >
                    <div class="position-absolute w-100 h-100 top-0 start-0 bg-gradient" 
                         style="background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%); 
                                animation: shimmer 2s infinite;"></div>
                    <div v-if="authStore.loading" class="spinner-border spinner-border-sm me-2" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    <i v-else class="bi bi-box-arrow-in-right me-2 fs-5"></i>
                    {{ authStore.loading ? 'Signing In...' : 'Sign In' }}
                  </button>
                </div>

                <!-- Sign Up Link -->
                <div class="text-center">
                  <p class="text-light opacity-75 mb-0">
                    Don't have an account?
                    <RouterLink
                      to="/auth/register"
                      class="text-primary text-decoration-none fw-semibold"
                    >
                      Sign up here
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
import { ref, reactive } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const showPassword = ref(false)

const form = reactive({
  email: '',
  password: '',
  rememberMe: false
})

const handleLogin = async () => {
  const result = await authStore.login(form.email, form.password)
  
  if (result.success) {
    // Redirect to intended page or dashboard
    const redirectTo = route.query.redirect as string || '/dashboard'
    router.push(redirectTo)
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
    rgba(5, 150, 105, 0.2) 20%,
    rgba(16, 185, 129, 0.3) 50%,
    rgba(5, 150, 105, 0.2) 80%,
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
  animation: floatParticle 10s ease-in-out infinite;
}

.particle-1 {
  width: 20px;
  height: 20px;
  top: 20%;
  left: 10%;
  animation-delay: 0s;
}

.particle-2 {
  width: 30px;
  height: 30px;
  top: 60%;
  right: 15%;
  animation-delay: 2s;
}

.particle-3 {
  width: 15px;
  height: 15px;
  bottom: 30%;
  left: 20%;
  animation-delay: 4s;
}

.particle-4 {
  width: 25px;
  height: 25px;
  top: 40%;
  right: 25%;
  animation-delay: 6s;
}

.particle-5 {
  width: 18px;
  height: 18px;
  bottom: 20%;
  right: 10%;
  animation-delay: 8s;
}

@keyframes floatParticle {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
    opacity: 0.3;
  }
  25% {
    transform: translateY(-30px) translateX(15px) rotate(90deg);
    opacity: 0.7;
  }
  50% {
    transform: translateY(-15px) translateX(-15px) rotate(180deg);
    opacity: 0.9;
  }
  75% {
    transform: translateY(-40px) translateX(10px) rotate(270deg);
    opacity: 0.5;
  }
}

/* Glow ring animation */
.glow-ring {
  background: linear-gradient(135deg, rgba(5, 150, 105, 0.4) 0%, rgba(16, 185, 129, 0.4) 100%);
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
  background: linear-gradient(45deg, #10b981, #059669, #10b981);
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
  background-color: #10b981;
  border-color: #10b981;
}

.form-check-input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .auth-background {
    background-attachment: scroll;
  }
  
  .card-body {
    padding: 2rem !important;
  }
}

/* Link hover effects */
a.text-primary:hover {
  color: #059669 !important;
  transition: color 0.3s ease;
}

/* Button hover effect */
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px -8px rgba(16, 185, 129, 0.5);
  transition: all 0.3s ease;
}
</style>