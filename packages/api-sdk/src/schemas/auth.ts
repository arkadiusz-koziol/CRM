import { z } from 'zod';

// Login request
export const LoginRequest = z.object({
  email: z.string().email(),
  password: z.string().min(1),
});

// Login response
export const LoginResponse = z.object({
  access_token: z.string(),
  token_type: z.literal('Bearer'),
  expires_in: z.number(),
  refresh_token: z.string().optional(),
  user: z.object({
    id: z.string().uuid(),
    firstName: z.string(),
    lastName: z.string(),
    email: z.string().email(),
    role: z.enum(['admin', 'manager', 'technician', 'user']),
  }),
});

// Refresh token request
export const RefreshTokenRequest = z.object({
  refresh_token: z.string(),
});

// Refresh token response
export const RefreshTokenResponse = z.object({
  access_token: z.string(),
  token_type: z.literal('Bearer'),
  expires_in: z.number(),
  refresh_token: z.string().optional(),
});

// Forgot password request
export const ForgotPasswordRequest = z.object({
  email: z.string().email(),
});

// Reset password request
export const ResetPasswordRequest = z.object({
  token: z.string(),
  password: z.string().min(8),
  password_confirmation: z.string().min(8),
});

// Change password request
export const ChangePasswordRequest = z.object({
  current_password: z.string().min(1),
  password: z.string().min(8),
  password_confirmation: z.string().min(8),
});

// OIDC PKCE authorization request
export const OidcAuthRequest = z.object({
  client_id: z.string(),
  redirect_uri: z.string().url(),
  response_type: z.literal('code'),
  scope: z.string(),
  code_challenge: z.string(),
  code_challenge_method: z.literal('S256'),
  state: z.string().optional(),
});

// OIDC PKCE token request
export const OidcTokenRequest = z.object({
  grant_type: z.literal('authorization_code'),
  client_id: z.string(),
  code: z.string(),
  redirect_uri: z.string().url(),
  code_verifier: z.string(),
});

// OIDC token response
export const OidcTokenResponse = z.object({
  access_token: z.string(),
  token_type: z.literal('Bearer'),
  expires_in: z.number(),
  refresh_token: z.string().optional(),
  scope: z.string().optional(),
  id_token: z.string().optional(),
});

// Logout request
export const LogoutRequest = z.object({
  refresh_token: z.string().optional(),
});

// Export types
export type LoginRequest = z.infer<typeof LoginRequest>;
export type LoginResponse = z.infer<typeof LoginResponse>;
export type RefreshTokenRequest = z.infer<typeof RefreshTokenRequest>;
export type RefreshTokenResponse = z.infer<typeof RefreshTokenResponse>;
export type ForgotPasswordRequest = z.infer<typeof ForgotPasswordRequest>;
export type ResetPasswordRequest = z.infer<typeof ResetPasswordRequest>;
export type ChangePasswordRequest = z.infer<typeof ChangePasswordRequest>;
export type OidcAuthRequest = z.infer<typeof OidcAuthRequest>;
export type OidcTokenRequest = z.infer<typeof OidcTokenRequest>;
export type OidcTokenResponse = z.infer<typeof OidcTokenResponse>;
export type LogoutRequest = z.infer<typeof LogoutRequest>;
