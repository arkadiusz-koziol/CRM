import React, { createContext, useContext, useState, useEffect } from 'react'
import type { ReactNode } from 'react'
import type { AuthContextType, AuthUser, LoginCredentials, AuthResponse } from '@/types/auth'
import { apiClient } from '@/services/api'
import { 
  SECURITY_CONSTANTS, 
  isValidTokenFormat, 
  isValidUserData, 
  sanitizeUserData,
  detectSuspiciousActivity,
  loginRateLimiter 
} from '@/utils/security'

const AuthContext = createContext<AuthContextType | undefined>(undefined)

interface AuthProviderProps {
  children: ReactNode
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<AuthUser | null>(null)
  const [token, setToken] = useState<string | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [isRefreshing, setIsRefreshing] = useState(false)

  // Check for existing token on mount and verify with backend
  useEffect(() => {
    const initializeAuth = async () => {
      const storedToken = localStorage.getItem(SECURITY_CONSTANTS.TOKEN_KEY)
      const storedUser = localStorage.getItem(SECURITY_CONSTANTS.USER_KEY)
      
      if (storedToken && isValidTokenFormat(storedToken)) {
        try {
          // Verify token with backend for security
          const response = await apiClient.get<{ data: AuthUser }>('/auth/me')
          const userData = response.data.data
          
          // Validate user data structure
          if (isValidUserData(userData)) {
            const sanitizedUser = sanitizeUserData(userData)
            if (sanitizedUser) {
              // Token is valid, set both user and token
              setUser(sanitizedUser)
              setToken(storedToken)
              // Update stored user data in case it's outdated
              localStorage.setItem(SECURITY_CONSTANTS.USER_KEY, JSON.stringify(sanitizedUser))
            } else {
              throw new Error('Invalid user data structure')
            }
          } else {
            throw new Error('Invalid user data received')
          }
        } catch (error) {
          // Token is invalid or expired, clear everything
          localStorage.removeItem(SECURITY_CONSTANTS.TOKEN_KEY)
          localStorage.removeItem(SECURITY_CONSTANTS.USER_KEY)
          setUser(null)
          setToken(null)
        }
      } else if (storedToken) {
        // Invalid token format, clear it
        localStorage.removeItem(SECURITY_CONSTANTS.TOKEN_KEY)
        localStorage.removeItem(SECURITY_CONSTANTS.USER_KEY)
      }
      
      setIsLoading(false)
    }
    
    initializeAuth()
  }, [])

  const login = async (credentials: LoginCredentials): Promise<void> => {
    try {
      setIsLoading(true)
      
      // Check for suspicious activity
      if (detectSuspiciousActivity()) {
        throw new Error('Access denied due to security policy')
      }
      
      // Rate limiting check
      const identifier = credentials.email.toLowerCase()
      if (!loginRateLimiter.canAttemptLogin(identifier)) {
        const remainingTime = Math.ceil(loginRateLimiter.getRemainingTime(identifier) / 60000)
        throw new Error(`Too many login attempts. Please try again in ${remainingTime} minutes.`)
      }
      
      // Validate credentials before sending
      if (!credentials.email || !credentials.password) {
        throw new Error('Email and password are required')
      }
      
      // Basic email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!emailRegex.test(credentials.email)) {
        throw new Error('Please enter a valid email address')
      }
      
      // Password strength check
      if (credentials.password.length < 6) {
        throw new Error('Password must be at least 6 characters long')
      }
      
      const response = await apiClient.post<{ data: AuthResponse }>('/auth/login', credentials)
      
      // Check if data is wrapped in 'data' property or directly accessible
      const responseData = response.data.data || response.data
      
      const { user: userData, token: authToken } = responseData
      
      if (!userData || !authToken) {
        throw new Error('Invalid response from server')
      }
      
      // Validate token format
      if (!isValidTokenFormat(authToken)) {
        throw new Error('Invalid token format received')
      }
      
      // Validate and sanitize user data
      if (!isValidUserData(userData)) {
        throw new Error('Invalid user data received')
      }
      
      const sanitizedUser = sanitizeUserData(userData)
      if (!sanitizedUser) {
        throw new Error('Failed to process user data')
      }
      
      // Reset rate limiting on successful login
      loginRateLimiter.resetAttempts(identifier)
      
      // Set both user and token together to ensure they're synchronized
      setUser(sanitizedUser)
      setToken(authToken)
      localStorage.setItem(SECURITY_CONSTANTS.TOKEN_KEY, authToken)
      localStorage.setItem(SECURITY_CONSTANTS.USER_KEY, JSON.stringify(sanitizedUser))
      
    } catch (error: any) {
      // Clear any partial auth state on error
      setUser(null)
      setToken(null)
      localStorage.removeItem(SECURITY_CONSTANTS.TOKEN_KEY)
      localStorage.removeItem(SECURITY_CONSTANTS.USER_KEY)
      
      // Provide user-friendly error messages
      if (error.response?.status === 401) {
        throw new Error('Invalid email or password')
      } else if (error.response?.status === 429) {
        throw new Error('Too many login attempts. Please try again later.')
      } else if (error.response?.status >= 500) {
        throw new Error('Server error. Please try again later.')
      } else if (error.message) {
        throw error
      } else {
        throw new Error('Login failed. Please try again.')
      }
    } finally {
      setIsLoading(false)
    }
  }

  const refreshToken = async (): Promise<boolean> => {
    if (isRefreshing || !token) return false
    
    try {
      setIsRefreshing(true)
      const response = await apiClient.get<{ data: AuthUser }>('/auth/me')
      const userData = response.data.data
      
      // Validate and sanitize user data
      if (isValidUserData(userData)) {
        const sanitizedUser = sanitizeUserData(userData)
        if (sanitizedUser) {
          setUser(sanitizedUser)
          localStorage.setItem(SECURITY_CONSTANTS.USER_KEY, JSON.stringify(sanitizedUser))
          return true
        }
      }
      
      throw new Error('Invalid user data received during refresh')
    } catch (error) {
      logout()
      return false
    } finally {
      setIsRefreshing(false)
    }
  }

  const logout = async () => {
    try {
      // Call logout endpoint to invalidate token on server
      if (token) {
        await apiClient.post('/auth/logout')
      }
    } catch (error) {
      // Logout API call failed, but continue with local cleanup
    } finally {
      // Always clear local state regardless of API call result
      setUser(null)
      setToken(null)
      localStorage.removeItem(SECURITY_CONSTANTS.TOKEN_KEY)
      localStorage.removeItem(SECURITY_CONSTANTS.USER_KEY)
    }
  }

  const isAuthenticated = !!user && !!token

  const value: AuthContextType = {
    user,
    token,
    isAuthenticated,
    isLoading,
    isRefreshing,
    login,
    logout,
    refreshToken,
  }

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext)
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}
