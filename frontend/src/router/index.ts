import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/HomeView.vue'),
      meta: { title: 'Home - EasyPass' }
    },
    {
      path: '/events',
      name: 'events',
      component: () => import('../views/EventsView.vue'),
      meta: { title: 'Events - EasyPass' }
    },
    {
      path: '/events/:id',
      name: 'event-detail',
      component: () => import('../views/EventDetailView.vue'),
      meta: { title: 'Event Details - EasyPass' }
    },
    {
      path: '/auth',
      children: [
        {
          path: 'login',
          name: 'login',
          component: () => import('../views/auth/LoginView.vue'),
          meta: { title: 'Login - EasyPass', guest: true }
        },
        {
          path: 'register',
          name: 'register',
          component: () => import('../views/auth/RegisterView.vue'),
          meta: { title: 'Register - EasyPass', guest: true }
        },
        {
          path: 'forgot-password',
          name: 'forgot-password',
          component: () => import('../views/auth/ForgotPasswordView.vue'),
          meta: { title: 'Forgot Password - EasyPass', guest: true }
        },
        {
          path: 'reset-password',
          name: 'reset-password',
          component: () => import('../views/auth/ResetPasswordView.vue'),
          meta: { title: 'Reset Password - EasyPass', guest: true }
        }
      ]
    },
    {
      path: '/tickets',
      name: 'tickets',
      component: () => import('../views/TicketsView.vue'),
      meta: { title: 'My Tickets - EasyPass', requiresAuth: true }
    },
    {
      path: '/tickets/:id',
      name: 'ticket-detail',
      component: () => import('../views/TicketDetailView.vue'),
      meta: { title: 'Ticket Details - EasyPass', requiresAuth: true }
    },
    {
      path: '/validate',
      name: 'validate-ticket',
      component: () => import('../views/ValidateTicketView.vue'),
      meta: { title: 'Validate Ticket - EasyPass' }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/DashboardView.vue'),
      meta: { title: 'Dashboard - EasyPass', requiresAuth: true }
    },
    {
      path: '/dashboard/events/create',
      name: 'create-event',
      component: () => import('../views/CreateEventView.vue'),
      meta: { title: 'Create Event - EasyPass', requiresAuth: true }
    },
    {
      path: '/dashboard/events/:id/edit',
      name: 'edit-event',
      component: () => import('../views/EditEventView.vue'),
      meta: { title: 'Edit Event - EasyPass', requiresAuth: true }
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/ProfileView.vue'),
      meta: { title: 'Profile - EasyPass', requiresAuth: true }
    },
    {
      path: '/contact',
      name: 'contact',
      component: () => import('../views/ContactView.vue'),
      meta: { title: 'Contact - EasyPass' }
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/NotFoundView.vue'),
      meta: { title: '404 - Page Not Found' }
    }
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Set page title
  if (to.meta.title) {
    document.title = to.meta.title as string
  }
  
  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      next({ name: 'login', query: { redirect: to.fullPath } })
      return
    }
  }
  
  // Check if route is for guests only
  if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'home' })
    return
  }
  
  next()
})

export default router
