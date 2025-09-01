import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export interface User {
  id: number
  name: string
  email: string
  role: 'user' | 'admin'
  email_verified_at: string | null
  created_at: string
  updated_at: string
}

export interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
}

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))
  const loading = ref(false)
  const error = ref<string | null>(null)
  const authChecked = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userName = computed(() => user.value?.name || '')
  const userEmail = computed(() => user.value?.email || '')
  const isAdmin = computed(() => user.value?.role === 'admin')

  // Actions
  const setToken = (newToken: string) => {
    token.value = newToken
    localStorage.setItem('token', newToken)
    axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
  }

  const clearToken = () => {
    token.value = null
    user.value = null
    authChecked.value = true
    localStorage.removeItem('token')
    delete axios.defaults.headers.common['Authorization']
  }

  const setError = (message: string) => {
    error.value = message
    setTimeout(() => {
      error.value = null
    }, 5000)
  }

  const checkAuth = async () => {
    const storedToken = localStorage.getItem('token')
    
    if (!storedToken) {
      authChecked.value = true
      return
    }

    try {
      axios.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`
      const response = await axios.get('/api/auth/profile')
      
      if (response.data.status === 'success') {
        token.value = storedToken
        user.value = response.data.data.user
      } else {
        clearToken()
      }
    } catch (err) {
      console.error('Auth check failed:', err)
      clearToken()
    } finally {
      authChecked.value = true
    }
  }

  const login = async (email: string, password: string) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post('/api/auth/login', {
        email,
        password
      })

      if (response.data.status === 'success') {
        const { user: userData, token: userToken } = response.data.data
        user.value = userData
        setToken(userToken)
        authChecked.value = true
        return { success: true }
      } else {
        setError(response.data.message || 'Login failed')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Login failed'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const register = async (name: string, email: string, password: string, passwordConfirmation: string) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post('/api/auth/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation
      })

      if (response.data.status === 'success') {
        const { user: userData, token: userToken } = response.data.data
        user.value = userData
        setToken(userToken)
        authChecked.value = true
        return { success: true }
      } else {
        setError(response.data.message || 'Registration failed')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Registration failed'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    loading.value = true

    try {
      await axios.post('/api/auth/logout')
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      clearToken()
      loading.value = false
    }
  }

  const forgotPassword = async (email: string) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post('/api/auth/forgot-password', { email })
      
      if (response.data.status === 'success') {
        return { success: true, message: response.data.message }
      } else {
        setError(response.data.message || 'Failed to send reset link')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to send reset link'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const resetPassword = async (token: string, email: string, password: string, passwordConfirmation: string) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post('/api/auth/reset-password', {
        token,
        email,
        password,
        password_confirmation: passwordConfirmation
      })

      if (response.data.status === 'success') {
        return { success: true, message: response.data.message }
      } else {
        setError(response.data.message || 'Password reset failed')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Password reset failed'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  const updateProfile = async (name: string, email: string) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.put('/api/auth/profile', {
        name,
        email
      })

      if (response.data.status === 'success') {
        user.value = response.data.data.user
        return { success: true, message: response.data.message }
      } else {
        setError(response.data.message || 'Profile update failed')
        return { success: false, message: response.data.message }
      }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Profile update failed'
      setError(message)
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  // Initialize axios defaults if token exists
  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  }

  return {
    // State
    user,
    token,
    loading,
    error,
    authChecked,
    
    // Getters
    isAuthenticated,
    userName,
    userEmail,
    isAdmin,
    
    // Actions
    login,
    register,
    logout,
    forgotPassword,
    resetPassword,
    updateProfile,
    checkAuth,
    setError,
    clearToken
  }
})
