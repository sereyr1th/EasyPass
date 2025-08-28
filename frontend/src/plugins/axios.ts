import axios from 'axios'

// Configure axios defaults
axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8002'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'
axios.defaults.withCredentials = true

// Request interceptor
axios.interceptors.request.use(
  (config) => {
    // Add auth token if available
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
axios.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    // Handle 401 errors (unauthorized)
    if (error.response?.status === 401) {
      // Clear token and redirect to login
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
      
      // Only redirect if not already on auth pages
      if (!window.location.pathname.includes('/auth/')) {
        window.location.href = '/auth/login'
      }
    }
    
    return Promise.reject(error)
  }
)

export default axios
