<template>
  <div class="min-vh-100">
    <!-- Modern Hero Section -->
    <section class="position-relative text-white overflow-hidden hero-section" style="min-height: 100vh;">
      <!-- Dynamic Background -->
      <div class="position-absolute w-100 h-100 top-0 start-0 hero-background"></div>
      <div class="position-absolute w-100 h-100 top-0 start-0 hero-overlay"></div>
      
      <!-- Floating Elements -->
      <div class="floating-elements">
        <div class="floating-orb orb-1"></div>
        <div class="floating-orb orb-2"></div>
        <div class="floating-orb orb-3"></div>
        <div class="floating-orb orb-4"></div>
        <div class="floating-orb orb-5"></div>
      </div>
      
      <!-- Grid Pattern Overlay -->
      <div class="grid-overlay"></div>
      
      <div class="container position-relative d-flex align-items-center justify-content-center" style="z-index: 10; min-height: 100vh; padding-top: 80px;">
        <div class="row justify-content-center text-center w-100">
          <div class="col-lg-12 col-xl-10">
            <!-- Main Hero Content -->
            <div class="hero-content">
              <!-- Badge -->
              <div class="hero-badge mb-4" data-aos="fade-up" data-aos-delay="100">
                <span class="badge-text">
                  <i class="bi bi-sparkles me-2"></i>
                  The Future of Event Management
                </span>
              </div>
              
              <!-- Main Title -->
              <div class="hero-title-container mb-4" data-aos="fade-up" data-aos-delay="200">
                <h1 class="hero-main-title">
                  Welcome to
                  <span class="hero-brand-name">
                    <span class="brand-text">Easy</span><span class="brand-accent">Pass</span>
                  </span>
                </h1>
                <div class="title-decoration"></div>
              </div>
              
              <!-- Subtitle -->
              <div class="hero-subtitle-container mb-5" data-aos="fade-up" data-aos-delay="300">
                <p class="hero-subtitle">
                  Transform your event experience with our cutting-edge platform.<br>
                  <span class="subtitle-highlight">Discover, Book, and Manage</span> events like never before.
                </p>
              </div>
              
              <!-- CTA Buttons -->
              <div class="hero-cta-container" data-aos="fade-up" data-aos-delay="400">
                <div class="d-flex flex-column flex-sm-row gap-4 justify-content-center align-items-center">
                  <RouterLink to="/events" class="btn-modern btn-primary-modern">
                    <span class="btn-content">
                      <i class="bi bi-calendar-event me-2"></i>
                      Explore Events
                    </span>
                    <div class="btn-glow"></div>
                  </RouterLink>
                  <RouterLink 
                    v-if="!authStore.isAuthenticated" 
                    to="/auth/register" 
                    class="btn-modern btn-secondary-modern"
                  >
                    <span class="btn-content">
                      <i class="bi bi-rocket-takeoff me-2"></i>
                      Get Started Free
                    </span>
                    <div class="btn-glow"></div>
                  </RouterLink>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Scroll Indicator -->
      <div class="scroll-indicator" data-aos="fade-up" data-aos-delay="600">
        <div class="scroll-text">Scroll to explore</div>
        <div class="scroll-arrow">
          <i class="bi bi-chevron-down"></i>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
      <div class="container py-5">
        <div class="row justify-content-center text-center mb-5">
          <div class="col-lg-8">
            <h2 class="display-4 fw-bold text-white mb-4 professional-title">Why Choose EasyPass?</h2>
            <p class="lead text-light opacity-75">
              We make event management simple, secure, and stylish for both organizers and attendees.
            </p>
          </div>
        </div>

        <div class="row g-4">
          <div class="col-lg-4 col-md-6">
            <div class="card h-100 text-center">
              <div class="card-body p-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 glow-animation mb-4" 
                     style="width: 64px; height: 64px; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                  <i class="bi bi-ticket-perforated fs-3 text-white"></i>
                </div>
                <h3 class="h5 fw-bold text-light mb-3 professional-title">Easy Booking</h3>
                <p class="text-muted mb-0">
                  Book your tickets in just a few clicks. Fast, secure, and hassle-free experience.
                </p>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="card h-100 text-center">
              <div class="card-body p-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 glow-animation mb-4" 
                     style="width: 64px; height: 64px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                  <i class="bi bi-qr-code fs-3 text-white"></i>
                </div>
                <h3 class="h5 fw-bold text-light mb-3 professional-title">QR Code Tickets</h3>
                <p class="text-muted mb-0">
                  Digital tickets with QR codes for quick and easy event entry.
                </p>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="card h-100 text-center">
              <div class="card-body p-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 glow-animation mb-4" 
                     style="width: 64px; height: 64px; background: linear-gradient(135deg, #15803d 0%, #166534 100%);">
                  <i class="bi bi-shield-check fs-3 text-white"></i>
                </div>
                <h3 class="h5 fw-bold text-light mb-3 professional-title">Secure & Reliable</h3>
                <p class="text-muted mb-0">
                  Your data and payments are protected with enterprise-grade security.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Events Section -->
    <section class="py-5">
      <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
          <h2 class="display-4 fw-bold text-white professional-title">Featured Events</h2>
          <RouterLink to="/events" class="btn btn-outline-primary">
            View All Events <i class="bi bi-arrow-right ms-2"></i>
          </RouterLink>
        </div>

        <div v-if="eventsStore.loading" class="d-flex justify-content-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <div v-else-if="eventsStore.featuredEvents.length > 0" class="row g-4">
          <div v-for="event in eventsStore.featuredEvents" :key="event.id" class="col-lg-4 col-md-6">
            <EventCard :event="event" />
          </div>
        </div>

        <div v-else class="text-center py-5">
          <i class="bi bi-calendar-x display-1 text-muted mb-4"></i>
          <h3 class="h4 fw-bold text-light mb-3 professional-title">No Events Available</h3>
          <p class="text-muted">Check back later for exciting events!</p>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 position-relative overflow-hidden">
      <div class="container py-5 text-center">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <h2 class="display-4 fw-bold text-white mb-4 professional-title">Ready to Get Started?</h2>
            <p class="lead text-light opacity-75 mb-5">
              Join thousands of users who trust EasyPass for their event management needs.
            </p>
            <div v-if="!authStore.isAuthenticated" class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
              <RouterLink to="/auth/register" class="btn btn-light btn-lg d-inline-flex align-items-center">
                <i class="bi bi-person-plus me-2"></i>
                Create Account
              </RouterLink>
              <RouterLink to="/events" class="btn btn-outline-light btn-lg d-inline-flex align-items-center">
                <i class="bi bi-calendar-event me-2"></i>
                Browse Events
              </RouterLink>
            </div>
            <div v-else class="d-flex justify-content-center">
              <RouterLink to="/dashboard" class="btn btn-light btn-lg d-inline-flex align-items-center">
                <i class="bi bi-plus-circle me-2"></i>
                Create Your First Event
              </RouterLink>
            </div>
          </div>
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

const authStore = useAuthStore()
const eventsStore = useEventsStore()

onMounted(async () => {
  // Fetch featured events
  await eventsStore.fetchEvents({ per_page: 6, sort_by: 'event_date', sort_order: 'asc' })
})
</script>

<style scoped>
/* Modern Hero Section */
.hero-section {
  position: relative;
  min-height: 100vh;
  overflow: hidden;
}

.hero-background {
  background: 
    radial-gradient(circle at 20% 80%, rgba(5, 150, 105, 0.3) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.3) 0%, transparent 50%),
    radial-gradient(circle at 40% 40%, rgba(6, 78, 59, 0.2) 0%, transparent 50%),
    linear-gradient(135deg, #0a0a0a 0%, #111111 50%, #1a1a1a 100%);
  background-size: 100% 100%, 100% 100%, 100% 100%, 100% 100%;
  animation: backgroundShift 20s ease-in-out infinite;
}

.hero-overlay {
  background: 
    linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.1) 100%),
    radial-gradient(ellipse at center, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
  backdrop-filter: blur(1px);
}

/* Grid Pattern Overlay */
.grid-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    linear-gradient(rgba(5, 150, 105, 0.1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(5, 150, 105, 0.1) 1px, transparent 1px);
  background-size: 50px 50px;
  opacity: 0.3;
  animation: gridFloat 30s linear infinite;
}

/* Floating Elements */
.floating-elements {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  z-index: 1;
}

.floating-orb {
  position: absolute;
  border-radius: 50%;
  background: radial-gradient(circle at 30% 30%, rgba(5, 150, 105, 0.6), rgba(16, 185, 129, 0.3));
  filter: blur(2px);
  animation: float 6s ease-in-out infinite;
}

.orb-1 {
  width: 60px;
  height: 60px;
  top: 20%;
  left: 10%;
  animation-delay: 0s;
}

.orb-2 {
  width: 80px;
  height: 80px;
  top: 60%;
  right: 15%;
  animation-delay: 2s;
}

.orb-3 {
  width: 40px;
  height: 40px;
  top: 30%;
  right: 25%;
  animation-delay: 4s;
}

.orb-4 {
  width: 100px;
  height: 100px;
  bottom: 20%;
  left: 20%;
  animation-delay: 1s;
}

.orb-5 {
  width: 50px;
  height: 50px;
  top: 70%;
  left: 60%;
  animation-delay: 3s;
}

/* Hero Content */
.hero-content {
  position: relative;
  z-index: 10;
}

/* Hero Badge */
.hero-badge {
  display: inline-block;
}

.badge-text {
  display: inline-flex;
  align-items: center;
  padding: 8px 20px;
  background: rgba(5, 150, 105, 0.1);
  border: 1px solid rgba(5, 150, 105, 0.3);
  border-radius: 50px;
  color: rgba(16, 185, 129, 0.9);
  font-size: 0.9rem;
  font-weight: 500;
  backdrop-filter: blur(10px);
  box-shadow: 0 4px 20px rgba(5, 150, 105, 0.2);
  transition: all 0.3s ease;
}

.badge-text:hover {
  background: rgba(5, 150, 105, 0.2);
  border-color: rgba(5, 150, 105, 0.5);
  box-shadow: 0 6px 25px rgba(5, 150, 105, 0.3);
}

/* Hero Title */
.hero-title-container {
  position: relative;
}

.hero-main-title {
  font-family: 'Space Grotesk', sans-serif;
  font-size: clamp(2.5rem, 8vw, 5.5rem);
  font-weight: 800;
  line-height: 1.1;
  margin: 0;
  background: linear-gradient(135deg, #ffffff 0%, #f3f4f6 50%, #e5e7eb 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-shadow: 0 0 40px rgba(255, 255, 255, 0.1);
  animation: titleGlow 3s ease-in-out infinite alternate;
}

.hero-brand-name {
  display: inline-block;
  position: relative;
}

.brand-text {
  background: linear-gradient(135deg, #ffffff 0%, #10b981 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.brand-accent {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
}

.title-decoration {
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 3px;
  background: linear-gradient(90deg, transparent, #10b981, transparent);
  border-radius: 2px;
  animation: decorationPulse 2s ease-in-out infinite;
}

/* Hero Subtitle */
.hero-subtitle {
  font-family: 'Inter', sans-serif;
  font-size: clamp(1.1rem, 3vw, 1.4rem);
  line-height: 1.6;
  color: rgba(255, 255, 255, 0.8);
  max-width: 700px;
  margin: 0 auto;
  font-weight: 400;
}

.subtitle-highlight {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 600;
}

/* Modern Buttons */
.btn-modern {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 16px 32px;
  border-radius: 50px;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 1rem;
  text-decoration: none;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  border: none;
  cursor: pointer;
  min-width: 200px;
  backdrop-filter: blur(10px);
  user-select: none;
}

.btn-modern::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-modern:hover::before {
  left: 100%;
}

.btn-modern:active {
  transform: translateY(-1px) scale(0.98);
  transition: all 0.1s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-primary-modern {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  box-shadow: 
    0 8px 25px rgba(16, 185, 129, 0.4),
    0 0 0 1px rgba(255, 255, 255, 0.1) inset;
  position: relative;
}

.btn-primary-modern::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  border-radius: 50px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.btn-primary-modern:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 
    0 15px 40px rgba(16, 185, 129, 0.6),
    0 0 0 1px rgba(255, 255, 255, 0.2) inset,
    0 0 20px rgba(16, 185, 129, 0.3);
  color: white;
}

.btn-primary-modern:hover::after {
  opacity: 1;
}

.btn-secondary-modern {
  background: rgba(255, 255, 255, 0.05);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
  position: relative;
}

.btn-secondary-modern::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.btn-secondary-modern:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.4);
  transform: translateY(-4px) scale(1.02);
  box-shadow: 
    0 15px 40px rgba(0, 0, 0, 0.5),
    0 0 20px rgba(255, 255, 255, 0.1);
  color: white;
}

.btn-secondary-modern:hover::after {
  opacity: 1;
}

.btn-content {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  transition: transform 0.3s ease;
}

.btn-modern:hover .btn-content {
  transform: scale(1.05);
}

.btn-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  border-radius: 50px;
  opacity: 0;
  background: radial-gradient(circle at center, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
  transition: opacity 0.3s ease;
  animation: pulse 2s infinite;
}

.btn-modern:hover .btn-glow {
  opacity: 1;
  animation: pulseHover 1s infinite;
}

@keyframes pulse {
  0%, 100% { 
    transform: scale(1); 
    opacity: 0.3;
  }
  50% { 
    transform: scale(1.05); 
    opacity: 0.1;
  }
}

@keyframes pulseHover {
  0%, 100% { 
    transform: scale(1); 
    opacity: 0.8;
  }
  50% { 
    transform: scale(1.1); 
    opacity: 0.4;
  }
}



/* Scroll Indicator */
.scroll-indicator {
  position: absolute;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  text-align: center;
  color: rgba(255, 255, 255, 0.6);
  z-index: 10;
  animation: bounce 2s infinite;
}

.scroll-text {
  font-size: 0.8rem;
  margin-bottom: 8px;
  font-weight: 500;
}

.scroll-arrow {
  font-size: 1.2rem;
  animation: arrowBounce 1.5s ease-in-out infinite;
}

/* Animations */
@keyframes backgroundShift {
  0%, 100% { 
    background-position: 0% 0%, 0% 0%, 0% 0%, 0% 0%; 
  }
  50% { 
    background-position: 100% 100%, -100% -100%, 50% 50%, 0% 0%; 
  }
}

@keyframes gridFloat {
  0% { transform: translate(0, 0); }
  100% { transform: translate(50px, 50px); }
}

@keyframes float {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg); 
    opacity: 0.6;
  }
  50% { 
    transform: translateY(-20px) rotate(180deg); 
    opacity: 1;
  }
}

@keyframes titleGlow {
  0% { 
    text-shadow: 0 0 40px rgba(255, 255, 255, 0.1); 
  }
  100% { 
    text-shadow: 0 0 60px rgba(16, 185, 129, 0.3), 0 0 40px rgba(255, 255, 255, 0.1); 
  }
}

@keyframes decorationPulse {
  0%, 100% { 
    opacity: 0.5; 
    transform: translateX(-50%) scaleX(1);
  }
  50% { 
    opacity: 1; 
    transform: translateX(-50%) scaleX(1.2);
  }
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
  40% { transform: translateX(-50%) translateY(-10px); }
  60% { transform: translateX(-50%) translateY(-5px); }
}

@keyframes arrowBounce {
  0%, 100% { transform: translateY(0); opacity: 0.6; }
  50% { transform: translateY(5px); opacity: 1; }
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-main-title {
    font-size: clamp(2rem, 10vw, 3.5rem);
  }
  
  .hero-subtitle {
    font-size: clamp(1rem, 4vw, 1.2rem);
  }
  
  .btn-modern {
    padding: 14px 28px;
    font-size: 0.9rem;
    min-width: 180px;
  }
  
  .floating-orb {
    display: none;
  }
}
</style>

