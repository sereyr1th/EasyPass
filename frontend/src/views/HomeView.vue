<template>
  <div class="min-h-screen bg-dark-900">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-primary-800 via-primary-700 to-primary-600 text-white overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-black/30 via-transparent to-black/20"></div>
      <!-- Animated background elements -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-primary-400 rounded-full animate-bounce-subtle"></div>
        <div class="absolute top-3/4 right-1/4 w-24 h-24 bg-primary-300 rounded-full animate-bounce-subtle" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-3/4 w-16 h-16 bg-primary-500 rounded-full animate-bounce-subtle" style="animation-delay: 2s;"></div>
      </div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
        <div class="text-center animate-fade-in">
          <h1 class="text-5xl md:text-7xl font-bold mb-8 leading-tight">
            Welcome to <span class="text-primary-300 animate-glow">EasyPass</span>
          </h1>
          <p class="text-xl md:text-2xl mb-12 text-primary-100 max-w-4xl mx-auto leading-relaxed">
            Your gateway to amazing events. Discover, book, and manage your event tickets with ease and style.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center animate-slide-up">
            <RouterLink
              to="/events"
              class="btn btn-primary text-lg px-8 py-3 inline-flex items-center justify-center shadow-glow hover:shadow-glow-lg transform hover:scale-105"
            >
              <CalendarIcon class="w-5 h-5 mr-2" />
              Browse Events
            </RouterLink>
            <RouterLink
              v-if="!authStore.isAuthenticated"
              to="/auth/register"
              class="btn btn-outline text-lg px-8 py-3 inline-flex items-center justify-center border-white text-white hover:bg-white hover:text-primary-700 transform hover:scale-105"
            >
              <UserPlusIcon class="w-5 h-5 mr-2" />
              Get Started
            </RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-dark-800/50 backdrop-blur-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-fade-in">
          <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Why Choose EasyPass?</h2>
          <p class="text-dark-300 text-xl max-w-3xl mx-auto leading-relaxed">
            We make event management simple, secure, and stylish for both organizers and attendees.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 animate-slide-up">
          <div class="card text-center p-10 hover:transform hover:scale-105 transition-all duration-500 group">
            <div class="w-20 h-20 bg-gradient-to-br from-primary-600 to-primary-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-glow group-hover:shadow-glow-lg transition-all duration-500">
              <TicketIcon class="w-10 h-10 text-white" />
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Easy Booking</h3>
            <p class="text-dark-300 text-lg leading-relaxed">
              Book your tickets in just a few clicks. Fast, secure, and hassle-free experience.
            </p>
          </div>

          <div class="card text-center p-10 hover:transform hover:scale-105 transition-all duration-500 group">
            <div class="w-20 h-20 bg-gradient-to-br from-primary-600 to-primary-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-glow group-hover:shadow-glow-lg transition-all duration-500">
              <QrCodeIcon class="w-10 h-10 text-white" />
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">QR Code Tickets</h3>
            <p class="text-dark-300 text-lg leading-relaxed">
              Digital tickets with QR codes for quick and easy event entry.
            </p>
          </div>

          <div class="card text-center p-10 hover:transform hover:scale-105 transition-all duration-500 group">
            <div class="w-20 h-20 bg-gradient-to-br from-primary-600 to-primary-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-glow group-hover:shadow-glow-lg transition-all duration-500">
              <ShieldCheckIcon class="w-10 h-10 text-white" />
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Secure & Reliable</h3>
            <p class="text-dark-300 text-lg leading-relaxed">
              Your data and payments are protected with enterprise-grade security.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Events Section -->
    <section class="py-24 bg-dark-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-12 animate-fade-in">
          <h2 class="text-4xl md:text-5xl font-bold text-white">Featured Events</h2>
          <RouterLink
            to="/events"
            class="text-primary-400 hover:text-primary-300 font-semibold text-lg inline-flex items-center group transition-all duration-300"
          >
            View All Events
            <ArrowRightIcon class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-300" />
          </RouterLink>
        </div>

        <div v-if="eventsStore.loading" class="flex justify-center py-12">
          <div class="spinner"></div>
        </div>

        <div v-else-if="eventsStore.featuredEvents.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <EventCard
            v-for="event in eventsStore.featuredEvents"
            :key="event.id"
            :event="event"
          />
        </div>

        <div v-else class="text-center py-16">
          <CalendarIcon class="w-20 h-20 text-dark-600 mx-auto mb-6" />
          <h3 class="text-2xl font-bold text-dark-400 mb-4">No Events Available</h3>
          <p class="text-dark-500 text-lg">Check back later for exciting events!</p>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-gradient-to-br from-primary-800 via-primary-700 to-primary-600 relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-black/20 via-transparent to-black/10"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 animate-fade-in">Ready to Get Started?</h2>
        <p class="text-primary-100 text-xl mb-12 max-w-3xl mx-auto leading-relaxed animate-slide-up">
          Join thousands of users who trust EasyPass for their event management needs.
        </p>
        <div v-if="!authStore.isAuthenticated" class="flex flex-col sm:flex-row gap-4 justify-center animate-slide-up">
          <RouterLink
            to="/auth/register"
            class="btn bg-white text-primary-700 hover:bg-dark-50 text-lg px-8 py-3 inline-flex items-center justify-center shadow-dark-lg hover:shadow-glow transform hover:scale-105"
          >
            <UserPlusIcon class="w-5 h-5 mr-2" />
            Create Account
          </RouterLink>
          <RouterLink
            to="/events"
            class="btn btn-outline border-white text-white hover:bg-white hover:text-primary-700 text-lg px-8 py-3 inline-flex items-center justify-center transform hover:scale-105"
          >
            <CalendarIcon class="w-5 h-5 mr-2" />
            Browse Events
          </RouterLink>
        </div>
        <div v-else class="flex justify-center animate-slide-up">
          <RouterLink
            to="/dashboard"
            class="btn bg-white text-primary-700 hover:bg-dark-50 text-lg px-8 py-3 inline-flex items-center justify-center shadow-dark-lg hover:shadow-glow transform hover:scale-105"
          >
            <PlusIcon class="w-5 h-5 mr-2" />
            Create Your First Event
          </RouterLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useEventsStore } from '@/stores/events'
import EventCard from '@/components/events/EventCard.vue'
import {
  CalendarIcon,
  UserPlusIcon,
  TicketIcon,
  QrCodeIcon,
  ShieldCheckIcon,
  ArrowRightIcon,
  PlusIcon
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const eventsStore = useEventsStore()

onMounted(async () => {
  // Fetch featured events
  await eventsStore.fetchEvents({ per_page: 6, sort_by: 'event_date', sort_order: 'asc' })
})
</script>
