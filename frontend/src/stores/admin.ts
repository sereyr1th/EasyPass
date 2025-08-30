import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export interface AdminStats {
  totalUsers: number
  totalAdmins: number
  totalEvents: number
  activeEvents: number
  totalTickets: number
  totalRevenue: number
}

export interface User {
  id: number
  name: string
  email: string
  role: 'user' | 'admin'
  email_verified_at: string | null
  created_at: string
  updated_at: string
}

export interface Event {
  id: number
  title: string
  category: string
  event_date: string
  created_at: string
  creator?: {
    id: number
    name: string
  }
}

export interface AdminTicket {
  id: number
  event_id: number
  user_id: number
  status: 'valid' | 'cancelled' | 'used'
  verification_code: string
  created_at: string
  updated_at: string
  event?: {
    id: number
    title: string
    category: string
    event_date: string
    price: number
  }
  user?: {
    id: number
    name: string
    email: string
  }
  purchase?: {
    id: number
    amount_paid: number
    payment_method: string
    payment_status: string
    created_at: string
  }
}

export interface AdminDashboardData {
  stats: AdminStats
  recent: {
    users: User[]
    events: Event[]
    tickets: any[]
  }
  charts: {
    monthlyUsers: Array<{ month: number; count: number }>
    monthlyRevenue: Array<{ month: number; revenue: number }>
  }
}

export interface Analytics {
  trends: {
    users: Array<{ month: string; count: number }>
    revenue: Array<{ month: string; revenue: number }>
    tickets: Array<{ month: string; count: number }>
    daily: Array<{ date: string; tickets_sold: number; daily_revenue: number }>
  }
  distributions: {
    categories: Array<{ category: string; events_count: number; total_attendees: number }>
    paymentMethods: Array<{ payment_method: string; count: number; total_revenue: number }>
    eventStatus: Array<{ status: string; count: number }>
    ticketStatus: Array<{ status: string; count: number }>
    categoryRevenue: Array<{ category: string; revenue: number; tickets_sold: number }>
  }
  topPerformers: {
    events: Array<{ 
      id: number
      title: string
      category: string
      event_date: string
      price: number
      tickets_count: number
      creator?: { id: number; name: string }
    }>
    creators: Array<{ 
      id: number
      name: string
      email: string
      events_count: number
    }>
  }
  summary: {
    new_users_today: number
    new_events_today: number
    tickets_sold_today: number
    revenue_today: number
    active_events: number
  }
}

export const useAdminStore = defineStore('admin', () => {
  // State
  const dashboardData = ref<AdminDashboardData | null>(null)
  const analytics = ref<Analytics | null>(null)
  const users = ref<User[]>([])
  const events = ref<Event[]>([])
  const tickets = ref<AdminTicket[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // Pagination state
  const usersPagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
  })

  const eventsPagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
  })

  const ticketsPagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0
  })

  // Computed
  const isLoading = computed(() => loading.value)
  const hasError = computed(() => error.value !== null)

  // Actions
  const clearError = () => {
    error.value = null
  }

  const fetchDashboardData = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get('/api/admin/dashboard')
      if (response.data.status === 'success') {
        dashboardData.value = response.data.data
      } else {
        throw new Error(response.data.message || 'Failed to fetch dashboard data')
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch dashboard data'
      console.error('Dashboard fetch error:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchAnalytics = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get('/api/admin/analytics')
      if (response.data.status === 'success') {
        analytics.value = response.data.data
      } else {
        throw new Error(response.data.message || 'Failed to fetch analytics')
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch analytics'
      console.error('Analytics fetch error:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchUsers = async (page = 1, search = '', role = '') => {
    loading.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: usersPagination.value.per_page.toString()
      })
      
      if (search) params.append('search', search)
      if (role) params.append('role', role)

      const response = await axios.get(`/api/admin/users?${params}`)
      if (response.data.status === 'success') {
        users.value = response.data.data.data
        usersPagination.value = {
          current_page: response.data.data.current_page,
          last_page: response.data.data.last_page,
          per_page: response.data.data.per_page,
          total: response.data.data.total
        }
      } else {
        throw new Error(response.data.message || 'Failed to fetch users')
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch users'
      console.error('Users fetch error:', err)
    } finally {
      loading.value = false
    }
  }

  const createUser = async (userData: {
    name: string
    email: string
    password: string
    role: 'user' | 'admin'
  }) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.post('/api/admin/users', userData)
      if (response.data.status === 'success') {
        // Refresh users list
        await fetchUsers(usersPagination.value.current_page)
        return response.data.data
      } else {
        throw new Error(response.data.message || 'Failed to create user')
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to create user'
      console.error('User creation error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateUser = async (userId: number, userData: {
    name?: string
    email?: string
    password?: string
    role?: 'user' | 'admin'
  }) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.put(`/api/admin/users/${userId}`, userData)
      if (response.data.status === 'success') {
        // Refresh users list
        await fetchUsers(usersPagination.value.current_page)
        return response.data.data
      } else {
        throw new Error(response.data.message || 'Failed to update user')
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to update user'
      console.error('User update error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteUser = async (userId: number) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.delete(`/api/admin/users/${userId}`)
      if (response.data.status === 'success') {
        // Refresh users list
        await fetchUsers(usersPagination.value.current_page)
        return true
      } else {
        throw new Error(response.data.message || 'Failed to delete user')
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to delete user'
      console.error('User deletion error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchEvents = async (page = 1, search = '', status = '') => {
    loading.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: eventsPagination.value.per_page.toString()
      })
      
      if (search) params.append('search', search)
      if (status) params.append('status', status)

      const response = await axios.get(`/api/admin/events?${params}`)
      if (response.data.status === 'success') {
        events.value = response.data.data.data
        eventsPagination.value = {
          current_page: response.data.data.current_page,
          last_page: response.data.data.last_page,
          per_page: response.data.data.per_page,
          total: response.data.data.total
        }
      } else {
        throw new Error(response.data.message || 'Failed to fetch events')
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch events'
      console.error('Events fetch error:', err)
    } finally {
      loading.value = false
    }
  }

  const deleteEvent = async (eventId: number) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.delete(`/api/admin/events/${eventId}`)
      if (response.data.status === 'success') {
        // Refresh events list
        await fetchEvents(eventsPagination.value.current_page)
        return true
      } else {
        throw new Error(response.data.message || 'Failed to delete event')
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to delete event'
      console.error('Event deletion error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchTickets = async (page: number = 1) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get(`/api/admin/tickets?page=${page}`)
      if (response.data.status === 'success') {
        tickets.value = response.data.data.data
        ticketsPagination.value = {
          current_page: response.data.data.current_page,
          last_page: response.data.data.last_page,
          per_page: response.data.data.per_page,
          total: response.data.data.total
        }
      } else {
        error.value = response.data.message || 'Failed to fetch tickets'
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch tickets'
      console.error('Tickets fetch error:', err)
    } finally {
      loading.value = false
    }
  }

  return {
    // State
    dashboardData,
    analytics,
    users,
    events,
    tickets,
    usersPagination,
    eventsPagination,
    ticketsPagination,
    loading,
    error,
    
    // Computed
    isLoading,
    hasError,
    
    // Actions
    clearError,
    fetchDashboardData,
    fetchAnalytics,
    fetchUsers,
    createUser,
    updateUser,
    deleteUser,
    fetchEvents,
    deleteEvent,
    fetchTickets
  }
})
