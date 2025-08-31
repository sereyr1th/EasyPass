import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { useNotifications } from '@/composables/useNotifications'

export interface Ticket {
  id: number
  event_id: number
  user_id: number
  ticket_number: string
  qr_code: string
  status: 'valid' | 'used' | 'cancelled'
  purchase_date: string
  used_at: string | null
  created_at: string
  updated_at: string
  event?: {
    id: number
    title: string
    description: string
    category: string
    location: string
    event_date: string
    price: number
    image_url: string | null
    refundable: boolean
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
    transaction_id: string
    payment_status: string
  }
}

export interface TicketValidation {
  ticket: Ticket
  event: any
  user: any
  validated_at: string
}

export const useTicketsStore = defineStore('tickets', () => {
  // Notifications
  const { success: showSuccess, error: showError } = useNotifications()
  
  // State
  const tickets = ref<Ticket[]>([])
  const currentTicket = ref<Ticket | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const validationResult = ref<TicketValidation | null>(null)

  // Actions
  const setError = (message: string) => {
    error.value = message
    setTimeout(() => {
      error.value = null
    }, 5000)
  }

  const fetchTickets = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get('/api/tickets')
      
      if (response.data.status === 'success') {
        tickets.value = response.data.data.data || response.data.data
        return { success: true }
      } else {
        setError(response.data.message || 'Failed to fetch tickets')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to fetch tickets'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const fetchTicket = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get(`/api/tickets/${id}`)
      
      if (response.data.status === 'success') {
        currentTicket.value = response.data.data
        return { success: true, data: response.data.data }
      } else {
        setError(response.data.message || 'Ticket not found')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to fetch ticket'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const purchaseTicket = async (eventId: number, paymentMethod = 'online') => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post('/api/tickets', {
        event_id: eventId,
        payment_method: paymentMethod
      })
      
      if (response.data.status === 'success') {
        // Add new ticket to the list
        tickets.value.unshift(response.data.data.ticket)
        return { 
          success: true, 
          data: response.data.data,
          message: response.data.message 
        }
      } else {
        setError(response.data.message || 'Failed to purchase ticket')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to purchase ticket'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const validateTicket = async (ticketNumber: string) => {
    loading.value = true
    error.value = null
    validationResult.value = null

    try {
      const response = await axios.post('/api/tickets/validate', {
        ticket_number: ticketNumber
      })
      
      if (response.data.status === 'success') {
        validationResult.value = response.data.data
        
        // Update ticket in the list if it exists
        const ticketIndex = tickets.value.findIndex(
          ticket => ticket.ticket_number === ticketNumber
        )
        if (ticketIndex !== -1) {
          tickets.value[ticketIndex].status = 'used'
          tickets.value[ticketIndex].used_at = response.data.data.validated_at
        }
        
        return { 
          success: true, 
          data: response.data.data,
          message: response.data.message 
        }
      } else {
        setError(response.data.message || 'Ticket validation failed')
        return { success: false, message: response.data.message, data: response.data.data }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Ticket validation failed'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const cancelTicket = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.put(`/api/tickets/${id}/cancel`)
      
      if (response.data.status === 'success') {
        // Update ticket status in the list
        const ticketIndex = tickets.value.findIndex(ticket => ticket.id === id)
        if (ticketIndex !== -1) {
          tickets.value[ticketIndex].status = 'cancelled'
        }
        
        // Update current ticket if it's the same
        if (currentTicket.value?.id === id) {
          currentTicket.value.status = 'cancelled'
        }
        
        // Show success notification with refund info
        const refundInfo = response.data.refund_info || response.data.message
        showSuccess('Ticket Cancelled', refundInfo)
        
        return { 
          success: true,
          message: response.data.message 
        }
      } else {
        const errorMsg = response.data.message || 'Failed to cancel ticket'
        setError(errorMsg)
        showError('Cancellation Failed', errorMsg)
        return { success: false, message: errorMsg }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to cancel ticket'
      setError(message)
      showError('Cancellation Failed', message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const downloadTicket = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get(`/api/tickets/${id}/download`)
      
      if (response.data.status === 'success') {
        return { 
          success: true,
          data: response.data.data,
          message: response.data.message 
        }
      } else {
        setError(response.data.message || 'Failed to download ticket')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to download ticket'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const clearValidationResult = () => {
    validationResult.value = null
  }

  return {
    // State
    tickets,
    currentTicket,
    loading,
    error,
    validationResult,
    
    // Actions
    fetchTickets,
    fetchTicket,
    purchaseTicket,
    validateTicket,
    cancelTicket,
    downloadTicket,
    clearValidationResult,
    setError
  }
})
