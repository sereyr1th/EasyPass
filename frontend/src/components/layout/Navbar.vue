<template>
  <nav class="fixed top-0 left-0 right-0 z-50 bg-dark-900/95 backdrop-blur-md border-b border-dark-700/50 shadow-dark-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo and Brand -->
        <div class="flex items-center">
          <RouterLink to="/" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-glow transition-all duration-300 group-hover:scale-110">
              <span class="text-white font-bold text-xl">E</span>
            </div>
            <span class="text-2xl font-black text-white group-hover:text-primary-400 transition-colors duration-300 tracking-tight font-poppins">EasyPass</span>
          </RouterLink>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden md:block">
          <div class="ml-10 flex items-baseline space-x-4">
            <RouterLink
              to="/"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path === '/' }"
            >
              Home
            </RouterLink>
            <RouterLink
              to="/events"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path.startsWith('/events') }"
            >
              Events
            </RouterLink>
            <RouterLink
              v-if="authStore.isAuthenticated"
              to="/tickets"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path.startsWith('/tickets') }"
            >
              My Tickets
            </RouterLink>
            <RouterLink
              v-if="authStore.isAuthenticated"
              to="/dashboard"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path.startsWith('/dashboard') }"
            >
              Dashboard
            </RouterLink>
          </div>
        </div>

        <!-- User Menu -->
        <div class="flex items-center space-x-4">
          <template v-if="authStore.isAuthenticated">
            <!-- User Dropdown -->
            <div class="relative" ref="userMenuRef">
              <button
                @click="showUserMenu = !showUserMenu"
                class="flex items-center space-x-2 text-dark-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-xl p-2 transition-all duration-300 hover:bg-dark-800/30 backdrop-blur-sm"
              >
                <div class="w-8 h-8 bg-gradient-to-br from-primary-600 to-primary-500 rounded-full flex items-center justify-center shadow-md">
                  <span class="text-white font-semibold text-xs">
                    {{ authStore.userName.charAt(0).toUpperCase() }}
                  </span>
                </div>
                <span class="hidden md:block font-semibold text-sm tracking-wide font-poppins">{{ authStore.userName }}</span>
                <ChevronDownIcon class="w-3 h-3 transition-transform duration-300" :class="{ 'rotate-180': showUserMenu }" />
              </button>

              <!-- Dropdown Menu -->
              <Transition name="fade">
                <div
                  v-if="showUserMenu"
                  class="absolute right-0 mt-2 w-48 bg-dark-800/95 backdrop-blur-md rounded-xl shadow-dark-lg border border-dark-700/50 py-1 z-50"
                >
                  <RouterLink
                    to="/profile"
                    @click="showUserMenu = false"
                    class="dropdown-item"
                  >
                    <UserIcon class="w-4 h-4" />
                    Profile
                  </RouterLink>
                  <RouterLink
                    to="/tickets"
                    @click="showUserMenu = false"
                    class="dropdown-item"
                  >
                    <TicketIcon class="w-4 h-4" />
                    My Tickets
                  </RouterLink>
                  <RouterLink
                    to="/contact"
                    @click="showUserMenu = false"
                    class="dropdown-item"
                  >
                    <EnvelopeIcon class="w-4 h-4" />
                    Contact Support
                  </RouterLink>
                  <hr class="border-gray-700 my-1">
                  <button
                    @click="handleLogout"
                    class="dropdown-item w-full text-left text-red-400 hover:text-red-300 hover:bg-red-900/20"
                  >
                    <ArrowRightOnRectangleIcon class="w-4 h-4" />
                    Logout
                  </button>
                </div>
              </Transition>
            </div>
          </template>

          <template v-else>
            <!-- Auth Buttons -->
            <RouterLink
              to="/auth/login"
              class="btn btn-outline text-sm"
            >
              Login
            </RouterLink>
            <RouterLink
              to="/auth/register"
              class="btn btn-primary text-sm"
            >
              Register
            </RouterLink>
          </template>

          <!-- Mobile Menu Button -->
          <button
            @click="showMobileMenu = !showMobileMenu"
            class="md:hidden text-dark-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-xl p-3 transition-all duration-300 hover:bg-dark-800/50 backdrop-blur-sm"
          >
            <Bars3Icon v-if="!showMobileMenu" class="w-6 h-6" />
            <XMarkIcon v-else class="w-6 h-6" />
          </button>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <Transition name="slide">
        <div v-if="showMobileMenu" class="md:hidden border-t border-dark-700/50 py-6 bg-dark-800/50 backdrop-blur-md">
          <div class="space-y-2">
            <RouterLink
              to="/"
              @click="showMobileMenu = false"
              class="mobile-nav-link"
              :class="{ 'mobile-nav-link-active': $route.path === '/' }"
            >
              Home
            </RouterLink>
            <RouterLink
              to="/events"
              @click="showMobileMenu = false"
              class="mobile-nav-link"
              :class="{ 'mobile-nav-link-active': $route.path.startsWith('/events') }"
            >
              Events
            </RouterLink>
            <RouterLink
              v-if="authStore.isAuthenticated"
              to="/tickets"
              @click="showMobileMenu = false"
              class="mobile-nav-link"
              :class="{ 'mobile-nav-link-active': $route.path.startsWith('/tickets') }"
            >
              My Tickets
            </RouterLink>
            <RouterLink
              v-if="authStore.isAuthenticated"
              to="/dashboard"
              @click="showMobileMenu = false"
              class="mobile-nav-link"
              :class="{ 'mobile-nav-link-active': $route.path.startsWith('/dashboard') }"
            >
              Dashboard
            </RouterLink>
          </div>

          <template v-if="!authStore.isAuthenticated">
            <div class="mt-6 pt-6 border-t border-dark-700/50 space-y-3">
              <RouterLink
                to="/auth/login"
                @click="showMobileMenu = false"
                class="btn btn-outline w-full text-center"
              >
                Login
              </RouterLink>
              <RouterLink
                to="/auth/register"
                @click="showMobileMenu = false"
                class="btn btn-primary w-full text-center"
              >
                Register
              </RouterLink>
            </div>
          </template>
        </div>
      </Transition>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  ChevronDownIcon,
  UserIcon,
  TicketIcon,
  EnvelopeIcon,
  ArrowRightOnRectangleIcon,
  Bars3Icon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const router = useRouter()

const showUserMenu = ref(false)
const showMobileMenu = ref(false)
const userMenuRef = ref<HTMLElement>()

const handleLogout = async () => {
  showUserMenu.value = false
  showMobileMenu.value = false
  await authStore.logout()
  router.push('/')
}

// Close menus when clicking outside
const handleClickOutside = (event: Event) => {
  if (userMenuRef.value && !userMenuRef.value.contains(event.target as Node)) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.nav-link {
  @apply text-dark-300 hover:text-white px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 hover:bg-dark-800/50 backdrop-blur-sm tracking-wide font-poppins;
}

.nav-link-active {
  @apply text-white bg-gradient-to-r from-primary-600 to-primary-500 shadow-glow;
}

.mobile-nav-link {
  @apply block text-dark-300 hover:text-white px-4 py-3 rounded-xl text-base font-bold transition-all duration-300 hover:bg-dark-700/50 tracking-wide font-poppins;
}

.mobile-nav-link-active {
  @apply text-white bg-gradient-to-r from-primary-600 to-primary-500 shadow-glow;
}

.dropdown-item {
  @apply flex items-center space-x-2 px-3 py-2 mx-1 text-sm font-semibold text-dark-300 hover:text-white hover:bg-dark-700/50 rounded-lg transition-all duration-300 tracking-wide font-poppins;
}
</style>
