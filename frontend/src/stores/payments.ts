import { defineStore } from 'pinia'
import axios from 'axios'

interface PaymentData {
  transaction_id: string
  amount: number
  currency: string
  merchant_name: string
  merchant_city: string
  bakong_account: string
  phone_number: string
  bill_number: string
  store_label: string
  terminal_label: string
  expiration_time: number
}

interface Payment {
  id: number
  transaction_id: string
  amount_paid: number
  payment_method: string
  payment_status: string
  created_at: string
  event: any
  ticket?: any
}

export const usePaymentsStore = defineStore('payments', {
  state: () => ({
    currentPayment: null as PaymentData | null,
    paymentHistory: [] as Payment[],
    loading: false,
    error: null as string | null
  }),

  actions: {
    async initiatePayment(eventId: number, paymentForm: any) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/payments/initiate', {
          event_id: eventId,
          ...paymentForm
        })

        if (response.data.status === 'success') {
          this.currentPayment = response.data.data.payment_data
          return response.data
        } else {
          throw new Error(response.data.message || 'Payment initiation failed')
        }
      } catch (error: any) {
        this.error = error.response?.data?.message || error.message || 'Payment initiation failed'
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

    async confirmPayment(transactionId: string, paymentReference?: string) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/payments/confirm', {
          transaction_id: transactionId,
          payment_reference: paymentReference
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
