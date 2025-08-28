<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container" style="max-width: 1200px;">
      <div class="row">
        <div class="col-12">
          <h1 class="display-5 fw-bold text-light mb-4 professional-title">Admin - Payment Management</h1>
          
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            This is a testing interface for manually confirming Bakong payments. In production, this would be handled automatically by webhooks.
          </div>

          <div v-if="loading" class="d-flex justify-content-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>

          <div v-else class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">Pending Payments</h5>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-dark">
                    <tr>
                      <th>Transaction ID</th>
                      <th>User</th>
                      <th>Event</th>
                      <th>Amount</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="payment in pendingPayments" :key="payment.id">
                      <td>
                        <code class="text-muted">{{ payment.transaction_id }}</code>
                      </td>
                      <td>{{ payment.user?.name || 'N/A' }}</td>
                      <td>
                        <div>
                          <div class="fw-semibold">{{ payment.event?.title }}</div>
                          <small class="text-muted">{{ payment.event?.category }}</small>
                        </div>
                      </td>
                      <td>
                        <span class="fw-bold text-success">${{ payment.amount_paid }}</span>
                      </td>
                      <td>
                        <small class="text-muted">{{ formatDate(payment.created_at) }}</small>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button
                            @click="confirmPayment(payment.transaction_id)"
                            class="btn btn-success btn-sm"
                            :disabled="confirming === payment.transaction_id"
                          >
                            <span v-if="confirming === payment.transaction_id">
                              <div class="spinner-border spinner-border-sm me-1" role="status"></div>
                              Confirming...
                            </span>
                            <span v-else>
                              <i class="bi bi-check-lg me-1"></i>
                              Confirm Payment
                            </span>
                          </button>
                          <button
                            @click="cancelPayment(payment.transaction_id)"
                            class="btn btn-danger btn-sm"
                            :disabled="cancelling === payment.transaction_id"
                          >
                            <span v-if="cancelling === payment.transaction_id">
                              <div class="spinner-border spinner-border-sm me-1" role="status"></div>
                              Cancelling...
                            </span>
                            <span v-else>
                              <i class="bi bi-x-lg me-1"></i>
                              Cancel
                            </span>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="pendingPayments.length === 0">
                      <td colspan="6" class="text-center py-4 text-muted">
                        No pending payments found
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

interface Payment {
  id: number
  transaction_id: string
  amount_paid: number
  payment_status: string
  created_at: string
  user?: { name: string }
  event?: { title: string; category: string }
}

const loading = ref(true)
const confirming = ref<string | null>(null)
const cancelling = ref<string | null>(null)
const pendingPayments = ref<Payment[]>([])

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const fetchPendingPayments = async () => {
  try {
    const response = await axios.get('/api/payments/history')
    if (response.data.status === 'success') {
      // Filter for pending payments only
      pendingPayments.value = response.data.data.data.filter(
        (payment: Payment) => payment.payment_status === 'pending'
      )
    }
  } catch (error) {
    console.error('Failed to fetch pending payments:', error)
  }
}

const confirmPayment = async (transactionId: string) => {
  confirming.value = transactionId
  try {
    const response = await axios.post('/api/payments/confirm', {
      transaction_id: transactionId,
      payment_reference: `MANUAL_CONFIRM_${Date.now()}`
    })

    if (response.data.status === 'success') {
      // Remove from pending list
      pendingPayments.value = pendingPayments.value.filter(
        payment => payment.transaction_id !== transactionId
      )
      alert('Payment confirmed successfully!')
    }
  } catch (error: any) {
    console.error('Failed to confirm payment:', error)
    alert('Failed to confirm payment: ' + (error.response?.data?.message || error.message))
  } finally {
    confirming.value = null
  }
}

const cancelPayment = async (transactionId: string) => {
  cancelling.value = transactionId
  try {
    const response = await axios.post('/api/payments/cancel', {
      transaction_id: transactionId
    })

    if (response.data.status === 'success') {
      // Remove from pending list
      pendingPayments.value = pendingPayments.value.filter(
        payment => payment.transaction_id !== transactionId
      )
      alert('Payment cancelled successfully!')
    }
  } catch (error: any) {
    console.error('Failed to cancel payment:', error)
    alert('Failed to cancel payment: ' + (error.response?.data?.message || error.message))
  } finally {
    cancelling.value = null
  }
}

onMounted(async () => {
  await fetchPendingPayments()
  loading.value = false
})
</script>
