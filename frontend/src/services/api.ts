import axios from 'axios'
import type { AxiosInstance, AxiosResponse } from 'axios'
import type { ApiResponse } from '@/types'
import { AUTH_CONSTANTS } from '@/constants/auth'

class ApiClient {
  private client: AxiosInstance

  constructor() {
    this.client = axios.create({
      baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8199/api/v1',
      timeout: 10000,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    })

    this.setupInterceptors()
  }

  private setupInterceptors() {
    // Request interceptor
    this.client.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem(AUTH_CONSTANTS.TOKEN_KEY)
        if (token) {
          config.headers.Authorization = `Bearer ${token}`
        }
        
        // Add security headers
        config.headers['X-Requested-With'] = 'XMLHttpRequest'
        config.headers['Cache-Control'] = 'no-cache, no-store, must-revalidate'
        config.headers['Pragma'] = 'no-cache'
        config.headers['Expires'] = '0'
        
        return config
      },
      (error) => {
        return Promise.reject(error)
      }
    )

    // Response interceptor
    this.client.interceptors.response.use(
      (response: AxiosResponse) => {
        return response
      },
      async (error) => {
        const originalRequest = error.config
        
        if (error.response?.status === 401 && !originalRequest._retry) {
          originalRequest._retry = true
          
          // Try to refresh token if available
          const token = localStorage.getItem(AUTH_CONSTANTS.TOKEN_KEY)
          if (token) {
            try {
              // Attempt to refresh user data
              const refreshResponse = await this.client.get('/auth/me')
              const userData = refreshResponse.data.data
              
              // Update stored user data
              localStorage.setItem(AUTH_CONSTANTS.USER_KEY, JSON.stringify(userData))
              
              // Retry the original request
              return this.client(originalRequest)
            } catch {
              // Refresh failed, clear auth data and redirect
              this.clearAuthData()
              this.redirectToLogin()
            }
          } else {
            // No token available, clear auth data and redirect
            this.clearAuthData()
            this.redirectToLogin()
          }
        }
        
        return Promise.reject(error)
      }
    )
  }

  private clearAuthData() {
    localStorage.removeItem(AUTH_CONSTANTS.TOKEN_KEY)
    localStorage.removeItem(AUTH_CONSTANTS.USER_KEY)
  }

  private redirectToLogin() {
    // Only redirect if not already on login page
    if (window.location.pathname !== '/login') {
      window.location.href = '/login'
    }
  }

  async get<T>(url: string, params?: Record<string, unknown>): Promise<ApiResponse<T>> {
    const response = await this.client.get(url, { params })
    return response.data
  }

  async post<T>(url: string, data?: unknown): Promise<ApiResponse<T>> {
    const response = await this.client.post(url, data)
    return response.data
  }

  async put<T>(url: string, data?: unknown): Promise<ApiResponse<T>> {
    const response = await this.client.put(url, data)
    return response.data
  }

  async delete<T>(url: string): Promise<ApiResponse<T>> {
    const response = await this.client.delete(url)
    return response.data
  }

  async patch<T>(url: string, data?: unknown): Promise<ApiResponse<T>> {
    const response = await this.client.patch(url, data)
    return response.data
  }
}

export const apiClient = new ApiClient()
