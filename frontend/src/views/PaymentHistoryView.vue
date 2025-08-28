<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container" style="max-width: 1200px;">
      <div class="row">
        <div class="col-12">
          <h1 class="display-5 fw-bold text-light mb-4 professional-title">Payment History</h1>
          
          <div v-if="loading" class="d-flex justify-content-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>

          <div v-else-if="paymentsStore.paymentHistory.length === 0" class="text-center py-5">
            <i class="bi bi-credit-card display-1 text-muted mb-4"></i>
            <h2 class="h4 fw-bold text-muted mb-3 professional-title">No Payment History</h2>
            <p class="text-muted mb-4">You haven't made any payments yet.</p>
            <RouterLink to="/events" class="btn btn-primary d-inline-flex align-items-center">
              <i class="bi bi-calendar-event me-2"></i>
              Browse Events
            </RouterLink>
          </div>

          <div v-else class="row g-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">Payment Transactions</h5>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead class="table-dark">
                        <tr>
                          <th>Transaction ID</th>
                          <th>Event</th>
                          <th>Amount</th>
                          <th>Payment Method</th>
                          <th>Status</th>
                          <th>Date</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="payment in paymentsStore.paymentHistory" :key="payment.id">
                          <td>
                            <code class="text-muted">{{ payment.transaction_id }}</code>
                          </td>
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
                            <span class="badge bg-info text-capitalize">{{ payment.payment_method }}</span>
                          </td>
                          <td>
                            <span 
                              :class="{
                                'badge bg-success': payment.payment_status === 'completed',
                                'badge bg-warning': payment.payment_status === 'pending',
                                'badge bg-danger': payment.payment_status === 'failed' || payment.payment_status === 'expired',
                                'badge bg-secondary': payment.payment_status === 'cancelled'
                              }"
                            >
                              {{ formatStatus(payment.payment_status) }}
                            </span>
                          </td>
                          <td>
                            <small class="text-muted">{{ formatDate(payment.created_at) }}</small>
                          </td>
                          <td>
                            <div class="btn-group btn-group-sm">
                              <RouterLink
                                v-if="payment.ticket"
                                :to="`/tickets/${payment.ticket.id}`"
                                class="btn btn-outline-primary btn-sm"
                              >
                                <i class="bi bi-ticket me-1"></i>
                                View Ticket
                              </RouterLink>
                              <button
                                v-if="payment.payment_status === 'pending'"
                                @click="checkStatus(payment.transaction_id)"
                                class="btn btn-outline-info btn-sm"
                                :disabled="checkingStatus"
                              >
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Check Status
                              </button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment Statistics -->
            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">
                    <i class="bi bi-graph-up text-success me-2"></i>
                    Total Paid
                  </h5>
                  <h3 class="text-success">${{ paymentsStore.totalPaid }}</h3>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">
                    <i class="bi bi-check-circle text-primary me-2"></i>
                    Completed Payments
                  </h5>
                  <h3 class="text-primary">{{ paymentsStore.completedPayments.length }}</h3>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">
                    <i class="bi bi-clock text-warning me-2"></i>
                    Pending Payments
                  </h5>
                  <h3 class="text-warning">{{ paymentsStore.pendingPayments.length }}</h3>
                </div>
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
import { RouterLink } from 'vue-router'
import { usePaymentsStore } from '@/stores/payments'

const paymentsStore = usePaymentsStore()
const loading = ref(true)
const checkingStatus = ref(false)

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

const formatStatus = (status: string) => {
  switch (status) {
    case 'completed':
      return 'Completed'
    case 'pending':
      return 'Pending'
    case 'failed':
      return 'Failed'
    case 'expired':
      return 'Expired'
    case 'cancelled':
      return 'Cancelled'
    default:
      return status.charAt(0).toUpperCase() + status.slice(1)
  }
}

const checkStatus = async (transactionId: string) => {
  checkingStatus.value = true
  try {
    await paymentsStore.checkPaymentStatus(transactionId)
    // Refresh payment history to get updated status
    await paymentsStore.fetchPaymentHistory()
  } catch (error) {
    console.error('Failed to check payment status:', error)
  } finally {
    checkingStatus.value = false
  }
}

onMounted(async () => {
  try {
    await paymentsStore.fetchPaymentHistory()
  } catch (error) {
    console.error('Failed to fetch payment history:', error)
  } finally {
    loading.value = false
  }
})
</script>
