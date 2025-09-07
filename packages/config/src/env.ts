import { z } from 'zod';

export const EnvSchema = z.object({
  // API Configuration
  API_URL: z.string().url(),
  API_TIMEOUT: z.coerce.number().default(15000),
  
  // Authentication
  AUTH_CLIENT_ID: z.string().min(1),
  AUTH_DISCOVERY_URL: z.string().url().optional(),
  
  // Sentry
  SENTRY_DSN: z.string().url().optional(),
  SENTRY_ENVIRONMENT: z.enum(['development', 'staging', 'production']).default('development'),
  
  // Feature Flags
  FEATURE_OFFLINE_MODE: z.coerce.boolean().default(false),
  FEATURE_DEBUG_LOGS: z.coerce.boolean().default(false),
  
  // App Configuration
  APP_NAME: z.string().default('Skytech'),
  APP_VERSION: z.string().default('1.0.0'),
  
  // Mobile specific (Expo)
  EXPO_PUBLIC_API_URL: z.string().url().optional(),
  EXPO_PUBLIC_SENTRY_DSN: z.string().url().optional(),
  EXPO_PUBLIC_AUTH_CLIENT_ID: z.string().min(1).optional(),
});

export type Env = z.infer<typeof EnvSchema>;

export function validateEnv(env: Record<string, unknown>): Env {
  return EnvSchema.parse(env);
}

export function getEnv(): Env {
  if (typeof window !== 'undefined') {
    // Browser environment
    return validateEnv({
      API_URL: process.env.NEXT_PUBLIC_API_URL || process.env.REACT_APP_API_URL || 'http://localhost:8199',
      API_TIMEOUT: process.env.NEXT_PUBLIC_API_TIMEOUT || process.env.REACT_APP_API_TIMEOUT || '15000',
      AUTH_CLIENT_ID: process.env.NEXT_PUBLIC_AUTH_CLIENT_ID || process.env.REACT_APP_AUTH_CLIENT_ID || 'skytech-web',
      AUTH_DISCOVERY_URL: process.env.NEXT_PUBLIC_AUTH_DISCOVERY_URL || process.env.REACT_APP_AUTH_DISCOVERY_URL || 'https://demo.identityserver.io',
      SENTRY_DSN: process.env.NEXT_PUBLIC_SENTRY_DSN || process.env.REACT_APP_SENTRY_DSN || undefined,
      SENTRY_ENVIRONMENT: process.env.NEXT_PUBLIC_SENTRY_ENVIRONMENT || process.env.REACT_APP_SENTRY_ENVIRONMENT || 'development',
      FEATURE_OFFLINE_MODE: process.env.NEXT_PUBLIC_FEATURE_OFFLINE_MODE || process.env.REACT_APP_FEATURE_OFFLINE_MODE || 'false',
      FEATURE_DEBUG_LOGS: process.env.NEXT_PUBLIC_FEATURE_DEBUG_LOGS || process.env.REACT_APP_FEATURE_DEBUG_LOGS || 'true',
      APP_NAME: process.env.NEXT_PUBLIC_APP_NAME || process.env.REACT_APP_APP_NAME || 'Skytech',
      APP_VERSION: process.env.NEXT_PUBLIC_APP_VERSION || process.env.REACT_APP_APP_VERSION || '1.0.0',
    });
  } else {
    // Node.js environment (Next.js server-side)
    return validateEnv({
      API_URL: process.env.API_URL || 'http://localhost:8199',
      API_TIMEOUT: process.env.API_TIMEOUT || '15000',
      AUTH_CLIENT_ID: process.env.AUTH_CLIENT_ID || 'skytech-web',
      AUTH_DISCOVERY_URL: process.env.AUTH_DISCOVERY_URL || 'https://demo.identityserver.io',
      SENTRY_DSN: process.env.SENTRY_DSN || undefined,
      SENTRY_ENVIRONMENT: process.env.SENTRY_ENVIRONMENT || 'development',
      FEATURE_OFFLINE_MODE: process.env.FEATURE_OFFLINE_MODE || 'false',
      FEATURE_DEBUG_LOGS: process.env.FEATURE_DEBUG_LOGS || 'true',
      APP_NAME: process.env.APP_NAME || 'Skytech',
      APP_VERSION: process.env.APP_VERSION || '1.0.0',
    });
  }
}

// Mobile-specific environment getter
export function getMobileEnv(): Env {
  // This will be used in the mobile app
  return validateEnv({
    API_URL: process.env.EXPO_PUBLIC_API_URL || 'http://localhost:8199',
    API_TIMEOUT: process.env.EXPO_PUBLIC_API_TIMEOUT || '15000',
    AUTH_CLIENT_ID: process.env.EXPO_PUBLIC_AUTH_CLIENT_ID || 'skytech-mobile',
    AUTH_DISCOVERY_URL: process.env.EXPO_PUBLIC_AUTH_DISCOVERY_URL || 'https://demo.identityserver.io',
    SENTRY_DSN: process.env.EXPO_PUBLIC_SENTRY_DSN || undefined,
    SENTRY_ENVIRONMENT: process.env.EXPO_PUBLIC_SENTRY_ENVIRONMENT || 'development',
    FEATURE_OFFLINE_MODE: process.env.EXPO_PUBLIC_FEATURE_OFFLINE_MODE || 'false',
    FEATURE_DEBUG_LOGS: process.env.EXPO_PUBLIC_FEATURE_DEBUG_LOGS || 'false',
    APP_NAME: process.env.EXPO_PUBLIC_APP_NAME || 'Skytech',
    APP_VERSION: process.env.EXPO_PUBLIC_APP_VERSION || '1.0.0',
  });
}
