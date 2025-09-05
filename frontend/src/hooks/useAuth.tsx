import React, { createContext, useContext, useState, useEffect } from 'react'
import type { ReactNode } from 'react'
import type { AuthContextType, AuthUser, LoginCredentials, AuthResponse } from '@/types/auth'
import { apiClient } from '@/services/api'

const AuthContext = createContext<AuthContextType | undefined>(undefined)

interface AuthProviderProps {
  children: ReactNode
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<AuthUser | null>(null)
  const [token, setToken] = useState<string | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  // Check for existing token on mount
  useEffect(() => {
    const initializeAuth = async () => {
      const storedToken = localStorage.getItem('auth_token')
      
      if (storedToken) {
        setToken(storedToken)
        // For now, we'll assume the token is valid
        // In a real app, you might want to verify it with the backend
      }
      setIsLoading(false)
    }
    
    initializeAuth()
  }, [])

  const login = async (credentials: LoginCredentials): Promise<void> => {
    try {
      setIsLoading(true)
      
      const response = await apiClient.post<AuthResponse>('/auth/login', credentials)
      
      // Handle the actual response structure from backend
      const { user: userData, token: authToken } = response.data
      
      if (!userData || !authToken) {
        throw new Error('Missing user data or token in response')
      }
      
      // Set both user and token together to ensure they're synchronized
      setUser(userData)
      setToken(authToken)
      localStorage.setItem('auth_token', authToken)
      
    } catch (error: any) {
      throw error
    } finally {
      setIsLoading(false)
    }
  }

  const logout = () => {
    setUser(null)
    setToken(null)
    localStorage.removeItem('auth_token')
  }

  const isAuthenticated = !!user && !!token

  const value: AuthContextType = {
    user,
    token,
    isAuthenticated,
    isLoading,
    login,
    logout,
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
