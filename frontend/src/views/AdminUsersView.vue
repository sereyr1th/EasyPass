<template>
  <div class="min-vh-100" style="padding-top: 100px;">
    <div class="container" style="max-width: 1400px;">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
          <h1 class="display-5 fw-bold text-light mb-3 professional-title">
            <i class="bi bi-people me-3 text-primary"></i>User Management
          </h1>
          <p class="lead text-muted">Manage system users and their roles</p>
        </div>
        <button @click="showCreateModal = true" class="btn btn-primary">
          <i class="bi bi-person-plus me-2"></i>Add User
        </button>
      </div>

      <!-- Filters -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Search</label>
              <input 
                v-model="searchQuery" 
                @input="debouncedSearch"
                type="text" 
                class="form-control" 
                placeholder="Search by name or email..."
              >
            </div>
            <div class="col-md-3">
              <label class="form-label">Role</label>
              <select v-model="roleFilter" @change="fetchUsers" class="form-select">
                <option value="">All Roles</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">&nbsp;</label>
              <button @click="clearFilters" class="btn btn-outline-secondary w-100">
                <i class="bi bi-x-circle me-2"></i>Clear
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="adminStore.isLoading && users.length === 0" class="d-flex justify-content-center py-5">
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

      <!-- Users Table -->
      <div v-else class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Users ({{ pagination.total }})</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td>{{ user.id }}</td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="d-flex align-items-center justify-content-center rounded-circle me-3" 
                           style="width: 32px; height: 32px; background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                        <span class="text-white fw-semibold small">
                          {{ user.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                      <span class="fw-semibold text-light">{{ user.name }}</span>
                    </div>
                  </td>
                  <td>{{ user.email }}</td>
                  <td>
                    <span :class="user.role === 'admin' ? 'badge bg-success' : 'badge bg-secondary'">
                      {{ user.role }}
                    </span>
                  </td>
                  <td>
                    <small class="text-muted">{{ formatDate(user.created_at) }}</small>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button 
                        @click="editUser(user)" 
                        class="btn btn-outline-primary"
                        :disabled="adminStore.isLoading"
                      >
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button 
                        @click="confirmDelete(user)" 
                        class="btn btn-outline-danger"
                        :disabled="adminStore.isLoading || user.role === 'admin' && adminStats.totalAdmins <= 1"
                      >
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="users.length === 0">
                  <td colspan="6" class="text-center py-4 text-muted">
                    No users found
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                <button @click="changePage(pagination.current_page - 1)" class="page-link">Previous</button>
              </li>
              
              <li v-for="page in visiblePages" :key="page" 
                  class="page-item" :class="{ active: page === pagination.current_page }">
                <button @click="changePage(page)" class="page-link">{{ page }}</button>
              </li>
              
              <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                <button @click="changePage(pagination.current_page + 1)" class="page-link">Next</button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div class="modal fade" :class="{ show: showCreateModal || showEditModal }" 
         :style="{ display: showCreateModal || showEditModal ? 'block' : 'none' }"
         tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content bg-dark">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingUser ? 'Edit User' : 'Create User' }}</h5>
            <button @click="closeModal" class="btn-close btn-close-white"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveUser">
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input v-model="userForm.name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="userForm.email" type="email" class="form-control" required>
              </div>
              <div v-if="!editingUser" class="mb-3">
                <label class="form-label">Password</label>
                <input v-model="userForm.password" type="password" class="form-control" required>
              </div>
              <div v-if="editingUser" class="mb-3">
                <label class="form-label">New Password (leave blank to keep current)</label>
                <input v-model="userForm.password" type="password" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Role</label>
                <select v-model="userForm.role" class="form-select" required>
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button @click="closeModal" class="btn btn-secondary">Cancel</button>
            <button @click="saveUser" class="btn btn-primary" :disabled="adminStore.isLoading">
              <span v-if="adminStore.isLoading">
                <div class="spinner-border spinner-border-sm me-2"></div>
                Saving...
              </span>
              <span v-else>{{ editingUser ? 'Update' : 'Create' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" :class="{ show: showDeleteModal }" 
         :style="{ display: showDeleteModal ? 'block' : 'none' }"
         tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content bg-dark">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button @click="showDeleteModal = false" class="btn-close btn-close-white"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the user <strong>{{ userToDelete?.name }}</strong>?</p>
            <p class="text-danger">This action cannot be undone.</p>
          </div>
          <div class="modal-footer">
            <button @click="showDeleteModal = false" class="btn btn-secondary">Cancel</button>
            <button @click="deleteUser" class="btn btn-danger" :disabled="adminStore.isLoading">
              <span v-if="adminStore.isLoading">
                <div class="spinner-border spinner-border-sm me-2"></div>
                Deleting...
              </span>
              <span v-else>Delete</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div v-if="showCreateModal || showEditModal || showDeleteModal" 
         class="modal-backdrop fade show" @click="closeModal"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, reactive } from 'vue'
import { useAdminStore } from '@/stores/admin'
import type { User } from '@/stores/admin'

const adminStore = useAdminStore()

// State
const searchQuery = ref('')
const roleFilter = ref('')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const editingUser = ref<User | null>(null)
const userToDelete = ref<User | null>(null)

const userForm = reactive({
  name: '',
  email: '',
  password: '',
  role: 'user' as 'user' | 'admin'
})

// Computed
const users = computed(() => adminStore.users)
const pagination = computed(() => adminStore.usersPagination)
const adminStats = computed(() => adminStore.dashboardData?.stats || { totalAdmins: 0 })

const visiblePages = computed(() => {
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []
  
  const start = Math.max(1, current - 2)
  const end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
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

const fetchUsers = () => {
  adminStore.fetchUsers(pagination.value.current_page, searchQuery.value, roleFilter.value)
}

const debouncedSearch = (() => {
  let timeout: NodeJS.Timeout
  return () => {
    clearTimeout(timeout)
    timeout = setTimeout(fetchUsers, 500)
  }
})()

const clearFilters = () => {
  searchQuery.value = ''
  roleFilter.value = ''
  fetchUsers()
}

const changePage = (page: number) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    adminStore.fetchUsers(page, searchQuery.value, roleFilter.value)
  }
}

const resetForm = () => {
  userForm.name = ''
  userForm.email = ''
  userForm.password = ''
  userForm.role = 'user'
}

const editUser = (user: User) => {
  editingUser.value = user
  userForm.name = user.name
  userForm.email = user.email
  userForm.password = ''
  userForm.role = user.role
  showEditModal.value = true
}

const confirmDelete = (user: User) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  showDeleteModal.value = false
  editingUser.value = null
  userToDelete.value = null
  resetForm()
}

const saveUser = async () => {
  try {
    if (editingUser.value) {
      const updateData: any = {
        name: userForm.name,
        email: userForm.email,
        role: userForm.role
      }
      
      if (userForm.password) {
        updateData.password = userForm.password
      }
      
      await adminStore.updateUser(editingUser.value.id, updateData)
    } else {
      await adminStore.createUser({
        name: userForm.name,
        email: userForm.email,
        password: userForm.password,
        role: userForm.role
      })
    }
    
    closeModal()
  } catch (error) {
    console.error('Failed to save user:', error)
  }
}

const deleteUser = async () => {
  if (userToDelete.value) {
    try {
      await adminStore.deleteUser(userToDelete.value.id)
      showDeleteModal.value = false
      userToDelete.value = null
    } catch (error) {
      console.error('Failed to delete user:', error)
    }
  }
}

onMounted(() => {
  fetchUsers()
  // Fetch dashboard data for admin stats
  if (!adminStore.dashboardData) {
    adminStore.fetchDashboardData()
  }
})
</script>

<style scoped>
.card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.modal-content {
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.table th {
  border-color: rgba(255, 255, 255, 0.1);
}

.table td {
  border-color: rgba(255, 255, 255, 0.05);
}

.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
}
</style>
