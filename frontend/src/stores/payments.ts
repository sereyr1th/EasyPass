import { defineStore } from 'pinia'
import axios from 'axios'

interface StripePaymentData {
  transaction_id: string
  payment_intent_id: string
  client_secret: string
  amount: number
  status: string
}

interface Payment {
  id: number
  transaction_id: string
  amount_paid: number
  payment_method: string
  payment_status: string
  created_at: string
  stripe_payment_intent_id?: string
  stripe_payment_method_id?: string
  event: any
  ticket?: any
}

export const usePaymentsStore = defineStore('payments', {
  state: () => ({
    currentPayment: null as StripePaymentData | null,
    paymentHistory: [] as Payment[],
    loading: false,
    error: null as string | null
  }),

  actions: {
    async createPaymentIntent(eventId: number, amount: number) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/payments/create-intent', {
          event_id: eventId,
          amount: amount
        })

        if (response.data.status === 'success') {
          this.currentPayment = {
            transaction_id: response.data.data.transaction_id,
            payment_intent_id: response.data.data.payment_intent_id,
            client_secret: response.data.data.client_secret,
            amount: response.data.data.amount,
            status: response.data.data.status
          }
          return response.data
        } else {
          throw new Error(response.data.message || 'Payment intent creation failed')
        }
      } catch (error: any) {
        this.error = error.response?.data?.message || error.message || 'Payment intent creation failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async checkPaymentStatus(transactionId: string) {
      try {
        const response = await axios.post('/api/payments/status', {
          transaction_id: transactionId
        })
        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to check payment status'
        throw error
      }
    },

    async confirmPayment(transactionId: string) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/payments/confirm', {
          transaction_id: transactionId
        })

        if (response.data.status === 'success') {
          this.currentPayment = null
          return response.data
        } else {
          throw new Error(response.data.message || 'Payment confirmation failed')
        }
      } catch (error: any) {
        this.error = error.response?.data?.message || error.message || 'Payment confirmation failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async cancelPayment(transactionId: string) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/payments/cancel', {
          transaction_id: transactionId
        })

        if (response.data.status === 'success') {
          this.currentPayment = null
          return response.data
        } else {
          throw new Error(response.data.message || 'Payment cancellation failed')
        }
      } catch (error: any) {
        this.error = error.response?.data?.message || error.message || 'Payment cancellation failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchPaymentHistory() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/api/payments/history')
        
        if (response.data.status === 'success') {
          this.paymentHistory = response.data.data.data || []
        } else {
          throw new Error(response.data.message || 'Failed to fetch payment history')
        }
      } catch (error: any) {
        this.error = error.response?.data?.message || error.message || 'Failed to fetch payment history'
        throw error
      } finally {
        this.loading = false
      }
    },

    clearError() {
      this.error = null
    },

    resetCurrentPayment() {
      this.currentPayment = null
    }
  },

  getters: {
    hasActivePayment: (state) => state.currentPayment !== null,
    
    completedPayments: (state) => 
      state.paymentHistory.filter(payment => payment.payment_status === 'completed'),
    
    pendingPayments: (state) => 
      state.paymentHistory.filter(payment => payment.payment_status === 'pending'),
    
    totalPaid: (state) => 
      state.paymentHistory
        .filter(payment => payment.payment_status === 'completed')
        .reduce((total, payment) => total + payment.amount_paid, 0)
  }
})