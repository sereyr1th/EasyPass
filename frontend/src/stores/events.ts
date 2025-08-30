import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { useNotifications } from '@/composables/useNotifications'

export interface Event {
  id: number
  title: string
  description: string
  category: string
  location: string
  event_date: string
  registration_deadline: string
  price: number
  max_attendees: number
  current_attendees: number
  image_url: string | null
  status: 'active' | 'cancelled' | 'completed'
  created_by: number
  refundable: boolean
  created_at: string
  updated_at: string
  creator?: {
    id: number
    name: string
    email: string
  }
  tickets?: any[]
  ticket_purchases?: any[]
}

export interface EventFilters {
  search?: string
  category?: string
  date_from?: string
  date_to?: string
  sort_by?: string
  sort_order?: 'asc' | 'desc'
  per_page?: number
  page?: number
}

export const useEventsStore = defineStore('events', () => {
  // Notifications
  const { success: showSuccess, error: showError } = useNotifications()
  
  // State
  const events = ref<Event[]>([])
  const currentEvent = ref<Event | null>(null)
  const myEvents = ref<Event[]>([])
  const categories = ref<string[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 12,
    total: 0
  })

  // Getters
  const upcomingEvents = computed(() => 
    events.value.filter(event => 
      new Date(event.event_date) > new Date() && event.status === 'active'
    )
  )

  const featuredEvents = computed(() => 
    upcomingEvents.value.slice(0, 6)
  )

  // Actions
  const setError = (message: string) => {
    error.value = message
    setTimeout(() => {
      error.value = null
    }, 5000)
  }

  const fetchEvents = async (filters: EventFilters = {}) => {
    loading.value = true
    error.value = null

    try {
      const params = new URLSearchParams()
      
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, value.toString())
        }
      })

      const response = await axios.get(`/api/events?${params.toString()}`)
      
      if (response.data.status === 'success') {
        events.value = response.data.data.data || response.data.data
        
        if (response.data.data.current_page) {
          pagination.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total
          }
        }
        
        return { success: true }
      } else {
        setError(response.data.message || 'Failed to fetch events')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to fetch events'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const fetchEvent = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get(`/api/events/${id}`)
      
      if (response.data.status === 'success') {
        currentEvent.value = response.data.data
        return { success: true, data: response.data.data }
      } else {
        setError(response.data.message || 'Event not found')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to fetch event'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const createEvent = async (eventData: FormData) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post('/api/events', eventData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      if (response.data.status === 'success') {
        // Add to events list if we're viewing all events
        events.value.unshift(response.data.data)
        return { success: true, data: response.data.data }
      } else {
        setError(response.data.message || 'Failed to create event')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to create event'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const updateEvent = async (id: number, eventData: FormData) => {
    loading.value = true
    error.value = null

    try {
      // FormData doesn't support PUT, so we'll use POST with _method
      eventData.append('_method', 'PUT')
      
      const response = await axios.post(`/api/events/${id}`, eventData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      if (response.data.status === 'success') {
        // Update in events list
        const index = events.value.findIndex(event => event.id === id)
        if (index !== -1) {
          events.value[index] = response.data.data
        }
        
        // Update current event if it's the same
        if (currentEvent.value?.id === id) {
          currentEvent.value = response.data.data
        }
        
        return { success: true, data: response.data.data }
      } else {
        setError(response.data.message || 'Failed to update event')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to update event'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const deleteEvent = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.delete(`/api/events/${id}`)
      
      if (response.data.status === 'success') {
        // Remove from events list
        events.value = events.value.filter(event => event.id !== id)
        
        // Remove from myEvents list
        myEvents.value = myEvents.value.filter(event => event.id !== id)
        
        // Clear current event if it's the deleted one
        if (currentEvent.value?.id === id) {
          currentEvent.value = null
        }
        
        showSuccess('Event Deleted', 'Event has been deleted successfully')
        return { success: true }
      } else {
        const errorMsg = response.data.message || 'Failed to delete event'
        setError(errorMsg)
        showError('Delete Failed', errorMsg)
        return { success: false, message: errorMsg }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to delete event'
      setError(message)
      showError('Delete Failed', message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const fetchMyEvents = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get('/api/events/my/events')
      
      if (response.data.status === 'success') {
        myEvents.value = response.data.data.data || response.data.data
        return { success: true }
      } else {
        setError(response.data.message || 'Failed to fetch your events')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to fetch your events'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const fetchCategories = async () => {
    try {
      const response = await axios.get('/api/events/categories/list')
      
      if (response.data.status === 'success') {
        categories.value = response.data.data
        return { success: true }
      }
    } catch (err: any) {
      console.error('Failed to fetch categories:', err)
    }
  }

  return {
    // State
    events,
    currentEvent,
    myEvents,
    categories,
    loading,
    error,
    pagination,
    
    // Getters
    upcomingEvents,
    featuredEvents,
    
    // Actions
    fetchEvents,
    fetchEvent,
    createEvent,
    updateEvent,
    deleteEvent,
    fetchMyEvents,
    fetchCategories,
    setError
  }
})
