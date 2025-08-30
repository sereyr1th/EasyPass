<template>
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
      <!-- Logo and Brand -->
      <RouterLink to="/" class="navbar-brand d-flex align-items-center text-decoration-none">
        <div class="position-relative me-3">
          <img src="/src/assets/images/logo.svg" alt="EasyPass Logo" style="width: 50px; height: 35px;" class="glow-animation">
        </div>
        <div class="d-flex flex-column">
          <span class="fs-4 fw-bold text-light professional-title">EasyPass</span>
          <small class="text-muted fw-medium" style="font-size: 0.7rem; letter-spacing: 0.1em;">EVENT MANAGEMENT</small>
        </div>
      </RouterLink>

      <!-- Mobile toggle button -->
      <button 
        class="navbar-toggler" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#navbarNav"
        aria-controls="navbarNav" 
        aria-expanded="false" 
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navigation items -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <RouterLink
              to="/"
              class="nav-link"
              :class="{ 'active': $route.path === '/' }"
            >
              <i class="bi bi-house-door me-2"></i>Home
            </RouterLink>
          </li>
          <li class="nav-item">
            <RouterLink
              to="/events"
              class="nav-link"
              :class="{ 'active': $route.path.startsWith('/events') }"
            >
              <i class="bi bi-calendar-event me-2"></i>Events
            </RouterLink>
          </li>
          <li class="nav-item" v-if="authStore.isAuthenticated">
            <RouterLink
              to="/tickets"
              class="nav-link"
              :class="{ 'active': $route.path.startsWith('/tickets') }"
            >
              <i class="bi bi-ticket-perforated me-2"></i>My Tickets
            </RouterLink>
          </li>
          <li class="nav-item" v-if="authStore.isAuthenticated">
            <RouterLink
              to="/dashboard"
              class="nav-link"
              :class="{ 'active': $route.path.startsWith('/dashboard') }"
            >
              <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </RouterLink>
          </li>
          <li class="nav-item" v-if="authStore.isAuthenticated && authStore.isAdmin">
            <RouterLink
              to="/admin"
              class="nav-link"
              :class="{ 'active': $route.path.startsWith('/admin') }"
            >
              <i class="bi bi-shield-check me-2"></i>Admin
            </RouterLink>
          </li>
        </ul>

        <!-- User Menu -->
        <div class="d-flex align-items-center">
          <template v-if="authStore.isAuthenticated">
            <!-- User Dropdown -->
            <div class="dropdown">
              <button
                class="btn btn-link dropdown-toggle d-flex align-items-center text-decoration-none"
                type="button"
                id="userDropdown"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <div class="d-flex align-items-center justify-content-center rounded-3 me-2" 
                     style="width: 32px; height: 32px; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                  <span class="text-white fw-semibold small">
                    {{ authStore.userName.charAt(0).toUpperCase() }}
                  </span>
                </div>
                <div class="d-none d-md-flex flex-column align-items-start me-2">
                  <span class="fw-semibold small text-light professional-title">{{ authStore.userName }}</span>
                  <small class="text-muted" style="font-size: 0.7rem;">Account</small>
                </div>
              </button>

              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                  <RouterLink to="/profile" class="dropdown-item">
                    <i class="bi bi-person me-2 text-primary"></i>Profile
                  </RouterLink>
                </li>
                <li>
                  <RouterLink to="/tickets" class="dropdown-item">
                    <i class="bi bi-ticket-perforated me-2 text-info"></i>My Tickets
                  </RouterLink>
                </li>
                <li>
                  <RouterLink to="/payments" class="dropdown-item">
                    <i class="bi bi-credit-card me-2 text-warning"></i>Payment History
                  </RouterLink>
                </li>
                <li>
                  <RouterLink to="/contact" class="dropdown-item">
                    <i class="bi bi-envelope me-2 text-success"></i>Contact Support
                  </RouterLink>
                </li>
                <li v-if="authStore.isAdmin">
                  <RouterLink to="/admin" class="dropdown-item">
                    <i class="bi bi-shield-check me-2 text-danger"></i>Admin Panel
                  </RouterLink>
                </li>
                <li v-if="authStore.isAdmin">
                  <RouterLink to="/admin/tickets" class="dropdown-item">
                    <i class="bi bi-ticket me-2 text-warning"></i>All Tickets
                  </RouterLink>
                </li>
                <li v-if="authStore.isAdmin">
                  <RouterLink to="/admin/analytics" class="dropdown-item">
                    <i class="bi bi-graph-up me-2 text-info"></i>Analytics
                  </RouterLink>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <button @click="handleLogout" class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                  </button>
                </li>
              </ul>
            </div>
          </template>

          <template v-else>
            <!-- Auth Buttons -->
            <div class="d-flex gap-2">
              <RouterLink to="/auth/login" class="btn btn-outline-light btn-sm">
                Login
              </RouterLink>
              <RouterLink to="/auth/register" class="btn btn-primary btn-sm">
                Register
              </RouterLink>
            </div>
          </template>

        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/')
}
</script>




