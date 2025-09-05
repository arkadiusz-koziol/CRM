export interface LoginCredentials {
  email: string
  password: string
}

export interface AuthUser {
  id: number
  name: string
  surname: string | null
  email: string
  phone: string | null
  created_at: string
  updated_at: string
  roles?: string[]
}

export interface AuthResponse {
  user: AuthUser
  token: string
}

export interface AuthContextType {
  user: AuthUser | null
  token: string | null
  isAuthenticated: boolean
  isLoading: boolean
  login: (credentials: LoginCredentials) => Promise<void>
  logout: () => void
}
