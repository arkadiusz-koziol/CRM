/**
 * Security utilities for authentication and token management
 */

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
 * Check if user data is valid
 */
export const isValidUserData = (user: any): boolean => {
  if (!user || typeof user !== 'object') return false
  
  return !!(
    user.id &&
    user.email &&
    user.name &&
    typeof user.id === 'number' &&
    typeof user.email === 'string' &&
    typeof user.name === 'string'
  )
}

/**
 * Sanitize user data before storing
 */
export const sanitizeUserData = (user: any) => {
  if (!user || typeof user !== 'object') return null
  
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
