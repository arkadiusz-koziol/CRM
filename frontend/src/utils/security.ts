/**
 * Security utilities for authentication and token management
 */

import type { AuthUser } from '@/types/auth'

// Type guards for better type safety
type UserCandidate = Record<string, unknown>

// Validation helper functions
const hasRequiredStringProperty = (obj: UserCandidate, key: string): boolean => {
  return typeof obj[key] === 'string' && obj[key] !== ''
}

const hasRequiredNumberProperty = (obj: UserCandidate, key: string): boolean => {
  return typeof obj[key] === 'number' && !isNaN(obj[key] as number)
}

const hasOptionalStringProperty = (obj: UserCandidate, key: string): boolean => {
  return obj[key] === null || obj[key] === undefined || typeof obj[key] === 'string'
}

// AuthUser validation schema
const AUTH_USER_SCHEMA = {
  required: {
    id: hasRequiredNumberProperty,
    email: hasRequiredStringProperty,
    name: hasRequiredStringProperty,
    created_at: hasRequiredStringProperty,
    updated_at: hasRequiredStringProperty,
  },
  optional: {
    surname: hasOptionalStringProperty,
    phone: hasOptionalStringProperty,
  },
} as const

export const SECURITY_CONSTANTS = {
  TOKEN_KEY: 'auth_token',
  USER_KEY: 'auth_user',
  MAX_LOGIN_ATTEMPTS: 5,
  LOCKOUT_DURATION: 15 * 60 * 1000, // 15 minutes in milliseconds
  TOKEN_REFRESH_THRESHOLD: 5 * 60 * 1000, // 5 minutes before expiry
} as const

/**
 * Check if the current environment is secure (HTTPS in production)
 */
export const isSecureEnvironment = (): boolean => {
  if (typeof window === 'undefined') return true // Server-side rendering
  
  const isProduction = import.meta.env.PROD
  const isHttps = window.location.protocol === 'https:'
  const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
  
  return !isProduction || isHttps || isLocalhost
}

/**
 * Validate token format (basic check)
 */
export const isValidTokenFormat = (token: string): boolean => {
  if (!token || typeof token !== 'string') return false
  
  return token.length > 10 && !token.includes(' ')
}

/**
 * Type guard to check if user data is valid AuthUser
 * @param user - Raw user data to validate
 * @returns True if user data matches AuthUser interface
 */
export const isValidUserData = (user: unknown): user is AuthUser => {
  if (!user || typeof user !== 'object') return false
  
  const candidate = user as UserCandidate
  
  // Validate required properties
  const requiredValid = Object.entries(AUTH_USER_SCHEMA.required).every(
    ([key, validator]) => validator(candidate, key)
  )
  
  // Validate optional properties
  const optionalValid = Object.entries(AUTH_USER_SCHEMA.optional).every(
    ([key, validator]) => validator(candidate, key)
  )
  
  return requiredValid && optionalValid
}

/**
 * Sanitize user data before storing
 * @param user - Raw user data from API response
 * @returns Sanitized AuthUser object or null if invalid
 */
export const sanitizeUserData = (user: unknown): AuthUser | null => {
  if (!isValidUserData(user)) return null
  
  return {
    id: user.id,
    name: user.name,
    surname: user.surname || null,
    email: user.email,
    phone: user.phone || null,
    created_at: user.created_at,
    updated_at: user.updated_at,
  }
}

/**
 * Check for suspicious activity patterns
 */
export const detectSuspiciousActivity = (): boolean => {
  if (typeof window === 'undefined') return false
  
  // Check for common bot indicators
  const userAgent = navigator.userAgent.toLowerCase()
  const botIndicators = ['bot', 'crawler', 'spider', 'scraper']
  
  return botIndicators.some(indicator => userAgent.includes(indicator))
}

/**
 * Generate a secure random string for CSRF protection
 */
export const generateSecureToken = (length: number = 32): string => {
  const array = new Uint8Array(length)
  crypto.getRandomValues(array)
  return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('')
}

/**
 * Rate limiting for login attempts
 */
export class LoginRateLimiter {
  private attempts: Map<string, { count: number; lastAttempt: number }> = new Map()
  
  canAttemptLogin(identifier: string): boolean {
    const now = Date.now()
    const attempt = this.attempts.get(identifier)
    
    if (!attempt) {
      this.attempts.set(identifier, { count: 1, lastAttempt: now })
      return true
    }
    
    // Reset if lockout period has passed
    if (now - attempt.lastAttempt > SECURITY_CONSTANTS.LOCKOUT_DURATION) {
      this.attempts.set(identifier, { count: 1, lastAttempt: now })
      return true
    }
    
    // Check if max attempts reached
    if (attempt.count >= SECURITY_CONSTANTS.MAX_LOGIN_ATTEMPTS) {
      return false
    }
    
    // Increment attempt count
    this.attempts.set(identifier, { count: attempt.count + 1, lastAttempt: now })
    return true
  }
  
  resetAttempts(identifier: string): void {
    this.attempts.delete(identifier)
  }
  
  getRemainingTime(identifier: string): number {
    const attempt = this.attempts.get(identifier)
    if (!attempt) return 0
    
    const elapsed = Date.now() - attempt.lastAttempt
    return Math.max(0, SECURITY_CONSTANTS.LOCKOUT_DURATION - elapsed)
  }
}

export const loginRateLimiter = new LoginRateLimiter()
