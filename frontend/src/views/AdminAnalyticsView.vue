<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="text-light fw-bold mb-1">Analytics Dashboard</h2>
            <p class="text-muted mb-0">Comprehensive insights and performance metrics</p>
          </div>
          <div class="d-flex gap-2">
            <button 
              @click="refreshAnalytics" 
              :disabled="adminStore.isLoading"
              class="btn btn-outline-primary"
            >
              <i class="bi bi-arrow-clockwise me-2"></i>
              {{ adminStore.isLoading ? 'Loading...' : 'Refresh' }}
            </button>
          </div>
        </div>

        <!-- Error Alert -->
        <div v-if="adminStore.hasError" class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>
          {{ adminStore.error }}
          <button type="button" class="btn-close" @click="adminStore.clearError()"></button>
        </div>

        <!-- Loading State -->
        <div v-if="adminStore.isLoading && !adminStore.analytics" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="text-muted mt-3">Loading analytics data...</p>
        </div>

        <!-- Analytics Content -->
        <div v-else-if="adminStore.analytics">
          <!-- Summary Cards -->
          <div class="row mb-4">
            <div class="col-md-6 col-xl-3 mb-3">
              <div class="card bg-gradient text-white h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="card-title text-white-50 mb-1">New Users Today</h6>
                      <h3 class="mb-0">{{ adminStore.analytics?.summary?.new_users_today || 0 }}</h3>
                    </div>
                    <i class="bi bi-people display-6 text-white-50"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3">
              <div class="card bg-gradient text-white h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="card-title text-white-50 mb-1">Events Created Today</h6>
                      <h3 class="mb-0">{{ adminStore.analytics?.summary?.new_events_today || 0 }}</h3>
                    </div>
                    <i class="bi bi-calendar-plus display-6 text-white-50"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3">
              <div class="card bg-gradient text-white h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="card-title text-white-50 mb-1">Tickets Sold Today</h6>
                      <h3 class="mb-0">{{ adminStore.analytics?.summary?.tickets_sold_today || 0 }}</h3>
                    </div>
                    <i class="bi bi-ticket display-6 text-white-50"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3">
              <div class="card bg-gradient text-white h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="card-title text-white-50 mb-1">Revenue Today</h6>
                      <h3 class="mb-0">${{ formatCurrency(adminStore.analytics?.summary?.revenue_today) }}</h3>
                    </div>
                    <i class="bi bi-currency-dollar display-6 text-white-50"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Charts Row 1 -->
          <div class="row mb-4">
            <!-- Revenue Trends -->
            <div class="col-lg-8 mb-4">
              <div class="card bg-dark border-secondary h-100">
                <div class="card-header bg-transparent border-secondary">
                  <h5 class="card-title text-light mb-0">
                    <i class="bi bi-graph-up me-2 text-success"></i>Revenue Trends (Last 12 Months)
                  </h5>
                </div>
                <div class="card-body">
                  <canvas ref="revenueChart" height="300"></canvas>
                </div>
              </div>
            </div>

            <!-- Category Distribution -->
            <div class="col-lg-4 mb-4">
              <div class="card bg-dark border-secondary h-100">
                <div class="card-header bg-transparent border-secondary">
                  <h5 class="card-title text-light mb-0">
                    <i class="bi bi-pie-chart me-2 text-warning"></i>Event Categories
                  </h5>
                </div>
                <div class="card-body">
                  <canvas ref="categoryChart" height="300"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Charts Row 2 -->
          <div class="row mb-4">
            <!-- User Registration Trends -->
            <div class="col-lg-6 mb-4">
              <div class="card bg-dark border-secondary h-100">
                <div class="card-header bg-transparent border-secondary">
                  <h5 class="card-title text-light mb-0">
                    <i class="bi bi-person-plus me-2 text-info"></i>User Registration Trends
                  </h5>
                </div>
                <div class="card-body">
                  <canvas ref="userChart" height="300"></canvas>
                </div>
              </div>
            </div>

            <!-- Ticket Status Distribution -->
            <div class="col-lg-6 mb-4">
              <div class="card bg-dark border-secondary h-100">
                <div class="card-header bg-transparent border-secondary">
                  <h5 class="card-title text-light mb-0">
                    <i class="bi bi-clipboard-check me-2 text-primary"></i>Ticket Status Distribution
                  </h5>
                </div>
                <div class="card-body">
                  <canvas ref="ticketStatusChart" height="300"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Top Performers -->
          <div class="row mb-4">
            <!-- Top Events -->
            <div class="col-lg-6 mb-4">
              <div class="card bg-dark border-secondary h-100">
                <div class="card-header bg-transparent border-secondary">
                  <h5 class="card-title text-light mb-0">
                    <i class="bi bi-trophy me-2 text-warning"></i>Top Events by Ticket Sales
                  </h5>
                </div>
                <div class="card-body p-0">
                  <div class="list-group list-group-flush">
                    <div v-for="(event, index) in (adminStore.analytics?.topPerformers?.events || [])" :key="event.id" 
                         class="list-group-item bg-transparent border-secondary">
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                          <span class="badge me-3" 
                                :class="{
                                  'bg-warning text-dark': index === 0,
                                  'bg-secondary': index === 1,
                                  'bg-info text-dark': index === 2,
                                  'bg-dark': index > 2
                                }">
                            #{{ index + 1 }}
                          </span>
                          <div>
                            <div class="fw-semibold text-light">{{ event.title }}</div>
                            <small class="text-muted">
                              <i class="bi bi-tag me-1"></i>{{ event.category }}
                              <i class="bi bi-calendar ms-2 me-1"></i>{{ formatDate(event.event_date) }}
                            </small>
                          </div>
                        </div>
                        <div class="text-end">
                          <div class="fw-bold text-success">{{ event.tickets_count }} tickets</div>
                          <small class="text-muted">${{ formatCurrency((event.price || 0) * (event.tickets_count || 0)) }}</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Top Creators -->
            <div class="col-lg-6 mb-4">
              <div class="card bg-dark border-secondary h-100">
                <div class="card-header bg-transparent border-secondary">
                  <h5 class="card-title text-light mb-0">
                    <i class="bi bi-star me-2 text-success"></i>Top Event Creators
                  </h5>
                </div>
                <div class="card-body p-0">
                  <div class="list-group list-group-flush">
                    <div v-for="(creator, index) in (adminStore.analytics?.topPerformers?.creators || [])" :key="creator.id" 
                         class="list-group-item bg-transparent border-secondary">
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                          <span class="badge me-3" 
                                :class="{
                                  'bg-warning text-dark': index === 0,
                                  'bg-secondary': index === 1,
                                  'bg-info text-dark': index === 2,
                                  'bg-dark': index > 2
                                }">
                            #{{ index + 1 }}
                          </span>
                          <div>
                            <div class="fw-semibold text-light">{{ creator.name }}</div>
                            <small class="text-muted">{{ creator.email }}</small>
                          </div>
                        </div>
                        <div class="text-end">
                          <div class="fw-bold text-primary">{{ creator.events_count }} events</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Daily Performance Chart -->
          <div class="row">
            <div class="col-12">
              <div class="card bg-dark border-secondary">
                <div class="card-header bg-transparent border-secondary">
                  <h5 class="card-title text-light mb-0">
                    <i class="bi bi-calendar3 me-2 text-info"></i>Daily Performance (Last 30 Days)
                  </h5>
                </div>
                <div class="card-body">
                  <canvas ref="dailyChart" height="400"></canvas>
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
import { onMounted, ref, nextTick, watch } from 'vue'
import { useAdminStore } from '@/stores/admin'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  Filler,
  LineController,
  BarController,
  PieController,
  DoughnutController
} from 'chart.js'
import { Line, Bar, Pie } from 'vue-chartjs'

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  Filler,
  LineController,
  BarController,
  PieController,
  DoughnutController
)

const adminStore = useAdminStore()

// Chart refs
const revenueChart = ref<HTMLCanvasElement>()
const categoryChart = ref<HTMLCanvasElement>()
const userChart = ref<HTMLCanvasElement>()
const ticketStatusChart = ref<HTMLCanvasElement>()
const dailyChart = ref<HTMLCanvasElement>()

// Chart instances
let revenueChartInstance: ChartJS | null = null
let categoryChartInstance: ChartJS | null = null
let userChartInstance: ChartJS | null = null
let ticketStatusChartInstance: ChartJS | null = null
let dailyChartInstance: ChartJS | null = null
let isCreatingCharts = ref(false)

const formatCurrency = (amount: number | string | null | undefined): string => {
  const numAmount = typeof amount === 'string' ? parseFloat(amount) : (amount || 0)
  return isNaN(numAmount) ? '0.00' : numAmount.toFixed(2)
}

const formatDate = (dateString: string | undefined): string => {
  if (!dateString) return 'N/A'
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric'
  }).format(new Date(dateString))
}

const formatMonth = (monthString: string): string => {
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    year: '2-digit'
  }).format(new Date(monthString + '-01'))
}

const createCharts = async () => {
  if (!adminStore.analytics || isCreatingCharts.value) return
  
  isCreatingCharts.value = true
  
  try {
    await nextTick()

  // Safely get data with fallbacks
  const analytics = adminStore.analytics
  const revenueData = analytics.trends?.revenue || []
  const categoryData = analytics.distributions?.categories || []
  const userData = analytics.trends?.users || []
  const ticketStatusData = analytics.distributions?.ticketStatus || []
  const dailyData = analytics.trends?.daily || []

  // Revenue Chart
  if (revenueChart.value) {
    const ctx = revenueChart.value.getContext('2d')
    if (ctx) {
      revenueChartInstance?.destroy()
      revenueChartInstance = new ChartJS(ctx, {
        type: 'line',
        data: {
          labels: revenueData.map((item: any) => formatMonth(item.month)),
          datasets: [{
            label: 'Revenue ($)',
            data: revenueData.map((item: any) => parseFloat(item.revenue || 0)),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: { color: '#e5e7eb' }
            }
          },
          scales: {
            x: {
              ticks: { color: '#9ca3af' },
              grid: { color: 'rgba(156, 163, 175, 0.1)' }
            },
            y: {
              ticks: { color: '#9ca3af' },
              grid: { color: 'rgba(156, 163, 175, 0.1)' }
            }
          }
        }
      })
    }
  }

  // Category Chart
  if (categoryChart.value) {
    const ctx = categoryChart.value.getContext('2d')
    if (ctx) {
      categoryChartInstance?.destroy()
      categoryChartInstance = new ChartJS(ctx, {
        type: 'doughnut',
        data: {
          labels: categoryData.map((item: any) => item.category || 'Unknown'),
          datasets: [{
            data: categoryData.map((item: any) => parseInt(item.events_count || 0)),
            backgroundColor: [
              '#ef4444', '#f97316', '#eab308', '#22c55e', 
              '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899'
            ]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { color: '#e5e7eb', padding: 20 }
            }
          }
        }
      })
    }
  }

  // User Chart
  if (userChart.value) {
    const ctx = userChart.value.getContext('2d')
    if (ctx) {
      userChartInstance?.destroy()
      userChartInstance = new ChartJS(ctx, {
        type: 'bar',
        data: {
          labels: userData.map((item: any) => formatMonth(item.month)),
          datasets: [{
            label: 'New Users',
            data: userData.map((item: any) => parseInt(item.count || 0)),
            backgroundColor: '#3b82f6',
            borderColor: '#1d4ed8',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: { color: '#e5e7eb' }
            }
          },
          scales: {
            x: {
              ticks: { color: '#9ca3af' },
              grid: { color: 'rgba(156, 163, 175, 0.1)' }
            },
            y: {
              ticks: { color: '#9ca3af' },
              grid: { color: 'rgba(156, 163, 175, 0.1)' }
            }
          }
        }
      })
    }
  }

  // Ticket Status Chart
  if (ticketStatusChart.value) {
    const ctx = ticketStatusChart.value.getContext('2d')
    if (ctx) {
      ticketStatusChartInstance?.destroy()
      ticketStatusChartInstance = new ChartJS(ctx, {
        type: 'pie',
        data: {
          labels: ticketStatusData.map((item: any) => {
            const status = item.status || 'unknown'
            return status.charAt(0).toUpperCase() + status.slice(1)
          }),
          datasets: [{
            data: ticketStatusData.map((item: any) => parseInt(item.count || 0)),
            backgroundColor: ['#22c55e', '#ef4444', '#f59e0b']
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { color: '#e5e7eb', padding: 20 }
            }
          }
        }
      })
    }
  }

  // Daily Chart
  if (dailyChart.value) {
    const ctx = dailyChart.value.getContext('2d')
    if (ctx) {
      dailyChartInstance?.destroy()
      dailyChartInstance = new ChartJS(ctx, {
        type: 'line',
        data: {
          labels: dailyData.map((item: any) => formatDate(item.date)),
          datasets: [
            {
              label: 'Tickets Sold',
              data: dailyData.map((item: any) => parseInt(item.tickets_sold || 0)),
              borderColor: '#06b6d4',
              backgroundColor: 'rgba(6, 182, 212, 0.1)',
              yAxisID: 'y',
              tension: 0.4
            },
            {
              label: 'Daily Revenue ($)',
              data: dailyData.map((item: any) => parseFloat(item.daily_revenue || 0)),
              borderColor: '#10b981',
              backgroundColor: 'rgba(16, 185, 129, 0.1)',
              yAxisID: 'y1',
              tension: 0.4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: { color: '#e5e7eb' }
            }
          },
          scales: {
            x: {
              ticks: { color: '#9ca3af' },
              grid: { color: 'rgba(156, 163, 175, 0.1)' }
            },
            y: {
              type: 'linear',
              display: true,
              position: 'left',
              ticks: { color: '#9ca3af' },
              grid: { color: 'rgba(156, 163, 175, 0.1)' }
            },
            y1: {
              type: 'linear',
              display: true,
              position: 'right',
              ticks: { color: '#9ca3af' },
              grid: { drawOnChartArea: false }
            }
          }
        }
      })
    }
  }
  } catch (error) {
    console.error('Error creating charts:', error)
  } finally {
    isCreatingCharts.value = false
  }
}

const refreshAnalytics = async () => {
  await adminStore.fetchAnalytics()
}

// Watch for analytics data changes to recreate charts
watch(() => adminStore.analytics, () => {
  if (adminStore.analytics) {
    createCharts()
  }
}, { deep: true })

onMounted(async () => {
  await adminStore.fetchAnalytics()
  if (adminStore.analytics) {
    await createCharts()
  }
})
</script>

<style scoped>
.card {
  transition: transform 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-2px);
}

.bg-gradient {
  background-size: 200% 200%;
  animation: gradientShift 6s ease infinite;
}

@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.list-group-item {
  transition: background-color 0.2s ease;
}

.list-group-item:hover {
  background-color: rgba(255, 255, 255, 0.05) !important;
}
</style>
