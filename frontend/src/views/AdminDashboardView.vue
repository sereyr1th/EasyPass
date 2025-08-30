<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container" style="max-width: 1400px;">
      <!-- Header -->
      <div class="mb-5">
        <h1 class="display-4 fw-bold text-light mb-3 professional-title">
          <i class="bi bi-shield-check me-3 text-primary"></i>Admin Dashboard
        </h1>
        <p class="lead text-muted">System overview and management tools</p>
      </div>

      <!-- Loading State -->
      <div v-if="adminStore.isLoading && !dashboardData" class="d-flex justify-content-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="adminStore.hasError" class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ adminStore.error }}
        <button @click="adminStore.clearError" class="btn-close float-end"></button>
      </div>

      <!-- Dashboard Content -->
      <div v-else-if="dashboardData">
        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card h-100 bg-gradient-primary">
              <div class="card-body text-center">
                <i class="bi bi-people fs-1 text-white mb-3"></i>
                <h3 class="text-white fw-bold">{{ dashboardData.stats.totalUsers || 0 }}</h3>
                <p class="text-white-50 mb-0">Total Users</p>
              </div>
            </div>
          </div>
          
          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card h-100 bg-gradient-success">
              <div class="card-body text-center">
                <i class="bi bi-shield-check fs-1 text-white mb-3"></i>
                <h3 class="text-white fw-bold">{{ dashboardData.stats.totalAdmins || 0 }}</h3>
                <p class="text-white-50 mb-0">Admins</p>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card h-100 bg-gradient-info">
              <div class="card-body text-center">
                <i class="bi bi-calendar-event fs-1 text-white mb-3"></i>
                <h3 class="text-white fw-bold">{{ dashboardData.stats.totalEvents || 0 }}</h3>
                <p class="text-white-50 mb-0">Total Events</p>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card h-100 bg-gradient-warning">
              <div class="card-body text-center">
                <i class="bi bi-calendar-check fs-1 text-white mb-3"></i>
                <h3 class="text-white fw-bold">{{ dashboardData.stats.activeEvents || 0 }}</h3>
                <p class="text-white-50 mb-0">Active Events</p>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card h-100 bg-gradient-secondary">
              <div class="card-body text-center">
                <i class="bi bi-ticket-perforated fs-1 text-white mb-3"></i>
                <h3 class="text-white fw-bold">{{ dashboardData.stats.totalTickets || 0 }}</h3>
                <p class="text-white-50 mb-0">Total Tickets</p>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card h-100 bg-gradient-danger">
              <div class="card-body text-center">
                <i class="bi bi-currency-dollar fs-1 text-white mb-3"></i>
                <h3 class="text-white fw-bold">${{ formatCurrency(dashboardData.stats.totalRevenue) }}</h3>
                <p class="text-white-50 mb-0">Total Revenue</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mb-5">
          <div class="col-md-3">
            <RouterLink to="/admin/users" class="card h-100 text-decoration-none hover-scale">
              <div class="card-body d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-3 me-3" 
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                  <i class="bi bi-people fs-4 text-white"></i>
                </div>
                <div>
                  <h5 class="card-title fw-bold text-light mb-1">Manage Users</h5>
                  <p class="card-text text-muted small mb-0">Add, edit, and delete users</p>
                </div>
              </div>
            </RouterLink>
          </div>

          <div class="col-md-3">
            <RouterLink to="/admin/events" class="card h-100 text-decoration-none hover-scale">
              <div class="card-body d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-3 me-3" 
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                  <i class="bi bi-calendar-event fs-4 text-white"></i>
                </div>
                <div>
                  <h5 class="card-title fw-bold text-light mb-1">Manage Events</h5>
                  <p class="card-text text-muted small mb-0">Oversee all events</p>
                </div>
              </div>
            </RouterLink>
          </div>

          <div class="col-md-3">
            <RouterLink to="/admin/payments" class="card h-100 text-decoration-none hover-scale">
              <div class="card-body d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-3 me-3" 
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                  <i class="bi bi-credit-card fs-4 text-white"></i>
                </div>
                <div>
                  <h5 class="card-title fw-bold text-light mb-1">Payments</h5>
                  <p class="card-text text-muted small mb-0">Manage transactions</p>
                </div>
              </div>
            </RouterLink>
          </div>

          <div class="col-md-3">
            <RouterLink to="/admin/analytics" class="card h-100 text-decoration-none hover-scale">
              <div class="card-body d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-3 me-3" 
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                  <i class="bi bi-graph-up fs-4 text-white"></i>
                </div>
                <div>
                  <h5 class="card-title fw-bold text-light mb-1">Analytics</h5>
                  <p class="card-text text-muted small mb-0">View detailed reports</p>
                </div>
              </div>
            </RouterLink>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="row g-4">
          <!-- Recent Users -->
          <div class="col-lg-4">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-person-plus me-2 text-primary"></i>Recent Users
                </h5>
              </div>
              <div class="card-body p-0">
                <div class="list-group list-group-flush">
                  <div v-for="user in (dashboardData.recent.users || [])" :key="user.id" 
                       class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <div class="fw-semibold text-light">{{ user.name }}</div>
                      <small class="text-muted">{{ user.email }}</small>
                    </div>
                    <div class="text-end">
                      <span :class="user.role === 'admin' ? 'badge bg-success' : 'badge bg-secondary'">
                        {{ user.role }}
                      </span>
                      <br>
                      <small class="text-muted">{{ formatDate(user.created_at) }}</small>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <RouterLink to="/admin/users" class="btn btn-outline-primary btn-sm w-100">
                  View All Users
                </RouterLink>
              </div>
            </div>
          </div>

          <!-- Recent Events -->
          <div class="col-lg-4">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-calendar-plus me-2 text-success"></i>Recent Events
                </h5>
              </div>
              <div class="card-body p-0">
                <div class="list-group list-group-flush">
                  <div v-for="event in (dashboardData.recent.events || [])" :key="event.id" 
                       class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <div class="fw-semibold text-light">{{ event.title }}</div>
                        <small class="text-muted">{{ event.category }}</small>
                      </div>
                      <small class="text-muted">{{ formatDate(event.created_at) }}</small>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <RouterLink to="/admin/events" class="btn btn-outline-success btn-sm w-100">
                  View All Events
                </RouterLink>
              </div>
            </div>
          </div>

          <!-- Recent Tickets -->
          <div class="col-lg-4">
            <div class="card h-100">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="bi bi-ticket me-2 text-warning"></i>Recent Tickets
                </h5>
              </div>
              <div class="card-body p-0">
                <div class="list-group list-group-flush">
                  <div v-for="ticket in (dashboardData.recent.tickets || [])" :key="ticket.id" 
                       class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <div class="fw-semibold text-light">{{ ticket.event?.title || 'N/A' }}</div>
                        <small class="text-muted">{{ ticket.user?.name || 'N/A' }}</small>
                      </div>
                      <small class="text-muted">{{ formatDate(ticket.created_at) }}</small>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <RouterLink to="/admin/tickets" class="btn btn-outline-warning btn-sm w-100">
                  View All Tickets
                </RouterLink>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { useAdminStore } from '@/stores/admin'

const adminStore = useAdminStore()

const dashboardData = computed(() => adminStore.dashboardData)

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const formatCurrency = (value: number | string | null | undefined) => {
  const numValue = typeof value === 'number' ? value : parseFloat(String(value || '0'))
  return isNaN(numValue) ? '0.00' : numValue.toFixed(2)
}

onMounted(() => {
  adminStore.fetchDashboardData()
})
</script>

<style scoped>
.bg-gradient-primary {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.bg-gradient-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.bg-gradient-info {
  background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
}

.bg-gradient-warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.bg-gradient-secondary {
  background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
}

.bg-gradient-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.hover-scale {
  transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
  transform: translateY(-2px);
}

.card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.list-group-item {
  background: rgba(255, 255, 255, 0.02);
  border-color: rgba(255, 255, 255, 0.1);
}
</style>
