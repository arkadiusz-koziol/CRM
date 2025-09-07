import * as AuthSession from 'expo-auth-session';
import * as Crypto from 'expo-crypto';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';
import { getMobileEnv } from '@skytech/config';

// Type declaration for window in React Native web
declare const window: any;

const env = getMobileEnv();

export interface AuthTokens {
  accessToken: string;
  refreshToken?: string;
  expiresIn: number;
  tokenType: string;
  scope?: string;
  idToken?: string;
}

export interface AuthUser {
  id: string;
  firstName: string;
  lastName: string;
  email: string;
  role: string;
}

const TOKENS_KEY = 'auth_tokens';
const USER_KEY = 'auth_user';

// Generate PKCE code challenge
export async function generateCodeChallenge(): Promise<{ codeChallenge: string; codeVerifier: string }> {
  // Generate a random code verifier (43-128 characters, URL-safe)
  // Since AuthSession.AuthRequest.createRandomCodeChallenge() doesn't exist, we'll generate manually
  const array = new Uint8Array(32);
  const randomBytes = await Crypto.getRandomBytesAsync(32);
  const codeVerifier = randomBytes.reduce((acc, byte) => {
    const char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~'[byte % 66];
    return acc + char;
  }, '').substring(0, 128);
  
  const codeChallengeBase64 = await Crypto.digestStringAsync(
    Crypto.CryptoDigestAlgorithm.SHA256,
    codeVerifier,
    { encoding: Crypto.CryptoEncoding.BASE64 }
  );
  
  // Convert BASE64 to BASE64URL (replace + with -, / with _, remove padding =)
  const codeChallenge = codeChallengeBase64
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=/g, '');
  
  return { codeChallenge, codeVerifier };
}

// Sign in with OAuth PKCE
export async function signInWithPKCE(): Promise<void> {
  const { codeChallenge, codeVerifier } = await generateCodeChallenge();
  
  const redirectUri = AuthSession.makeRedirectUri({
    scheme: 'skytech',
    path: 'auth/callback',
  });

  const request = new AuthSession.AuthRequest({
    clientId: env.AUTH_CLIENT_ID,
    scopes: ['openid', 'profile', 'email'],
    redirectUri,
    responseType: AuthSession.ResponseType.Code,
    codeChallenge,
    codeChallengeMethod: AuthSession.CodeChallengeMethod.S256,
  });

  const result = await request.promptAsync({
    authorizationEndpoint: `${env.API_URL}/api/v1/auth/oauth/authorize`,
  });

  if (result.type === 'success' && result.params?.code) {
    const tokens = await exchangeCodeForTokens(result.params.code, codeVerifier);
    await storeTokens(tokens);
    
    // Get user info from the backend
    const userResponse = await fetch(`${env.API_URL}/api/v1/auth/me`, {
      headers: {
        'Authorization': `Bearer ${tokens.accessToken}`,
      },
    });
    
    if (userResponse.ok) {
      const userData = await userResponse.json();
      const user: AuthUser = {
        id: userData.id,
        firstName: userData.first_name || userData.firstName,
        lastName: userData.last_name || userData.lastName,
        email: userData.email,
        role: userData.role || 'user',
      };
      await storeUser(user);
    }
  } else {
    throw new Error('OAuth authentication failed');
  }
}

// Exchange authorization code for tokens
async function exchangeCodeForTokens(code: string, codeVerifier: string): Promise<AuthTokens> {
  const tokenEndpoint = `${env.API_URL}/api/v1/auth/oauth/token`;
  
  const response = await fetch(tokenEndpoint, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      grant_type: 'authorization_code',
      client_id: env.AUTH_CLIENT_ID,
      code,
      redirect_uri: AuthSession.makeRedirectUri({
        scheme: 'skytech',
        path: 'auth/callback',
      }),
      code_verifier: codeVerifier,
    }).toString(),
  });

  if (!response.ok) {
    throw new Error('Failed to exchange code for tokens');
  }

  const data = await response.json();
  return {
    accessToken: data.access_token,
    refreshToken: data.refresh_token,
    expiresIn: data.expires_in,
    tokenType: data.token_type,
    scope: data.scope,
    idToken: data.id_token,
  };
}

// Store tokens securely
async function storeTokens(tokens: AuthTokens): Promise<void> {
  if (Platform.OS === 'web') {
    if (typeof window !== 'undefined' && window.localStorage) {
      window.localStorage.setItem(TOKENS_KEY, JSON.stringify(tokens));
    }
  } else {
    await SecureStore.setItemAsync(TOKENS_KEY, JSON.stringify(tokens));
  }
}

// Get stored tokens
export async function getTokens(): Promise<AuthTokens | null> {
  try {
    let tokensJson: string | null;
    if (Platform.OS === 'web') {
      if (typeof window !== 'undefined' && window.localStorage) {
        tokensJson = window.localStorage.getItem(TOKENS_KEY);
      } else {
        tokensJson = null;
      }
    } else {
      tokensJson = await SecureStore.getItemAsync(TOKENS_KEY);
    }
    return tokensJson ? JSON.parse(tokensJson) : null;
  } catch (error) {
    console.error('Failed to get tokens:', error);
    return null;
  }
}

// Store user data
export async function storeUser(user: AuthUser): Promise<void> {
  if (Platform.OS === 'web') {
    if (typeof window !== 'undefined' && window.localStorage) {
      window.localStorage.setItem(USER_KEY, JSON.stringify(user));
    }
  } else {
    await SecureStore.setItemAsync(USER_KEY, JSON.stringify(user));
  }
}

// Get stored user
export async function getUser(): Promise<AuthUser | null> {
  try {
    let userJson: string | null;
    if (Platform.OS === 'web') {
      if (typeof window !== 'undefined' && window.localStorage) {
        userJson = window.localStorage.getItem(USER_KEY);
      } else {
        userJson = null;
      }
    } else {
      userJson = await SecureStore.getItemAsync(USER_KEY);
    }
    
    if (userJson) {
      return JSON.parse(userJson);
    }
    
    // For local development, return mock user if tokens exist
    const tokens = await getTokens();
    if (tokens) {
      const mockUser: AuthUser = {
        id: 'skytech_user_123',
        firstName: 'Skytech',
        lastName: 'User',
        email: 'user@skytech.com',
        role: 'user',
      };
      await storeUser(mockUser);
      return mockUser;
    }
    
    return null;
  } catch (error) {
    console.error('Failed to get user:', error);
    return null;
  }
}

// Refresh access token
export async function refreshAccessToken(): Promise<AuthTokens | null> {
  const tokens = await getTokens();
  if (!tokens?.refreshToken) {
    return null;
  }

  try {
    const tokenEndpoint = `${env.API_URL}/api/v1/auth/oauth/token`;
    
    const response = await fetch(tokenEndpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        grant_type: 'refresh_token',
        client_id: env.AUTH_CLIENT_ID,
        refresh_token: tokens.refreshToken,
      }).toString(),
    });

    if (!response.ok) {
      throw new Error('Failed to refresh token');
    }

    const data = await response.json();
    const newTokens = {
      accessToken: data.access_token,
      refreshToken: data.refresh_token || tokens.refreshToken,
      expiresIn: data.expires_in,
      tokenType: data.token_type,
      scope: data.scope,
      idToken: data.id_token,
    };

    await storeTokens(newTokens);
    return newTokens;
  } catch (error) {
    console.error('Failed to refresh token:', error);
    return null;
  }
}

// Sign out
export async function signOut(): Promise<void> {
  try {
    if (Platform.OS === 'web') {
      if (typeof window !== 'undefined' && window.localStorage) {
        window.localStorage.removeItem(TOKENS_KEY);
        window.localStorage.removeItem(USER_KEY);
      }
    } else {
      await SecureStore.deleteItemAsync(TOKENS_KEY);
      await SecureStore.deleteItemAsync(USER_KEY);
    }
  } catch (error) {
    console.error('Failed to sign out:', error);
  }
}

// Sign in with email and password
export async function signInWithCredentials(email: string, password: string): Promise<void> {
  const response = await fetch(`${env.API_URL}/api/v1/auth/login`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email,
      password,
    }),
  });

  if (!response.ok) {
    const errorData = await response.json().catch(() => ({}));
    throw new Error(errorData.message || 'Login failed');
  }

  const data = await response.json();
  
  // Store tokens
  const tokens: AuthTokens = {
    accessToken: data.access_token,
    refreshToken: data.refresh_token,
    expiresIn: data.expires_in,
    tokenType: data.token_type || 'Bearer',
    scope: data.scope,
    idToken: data.id_token,
  };
  
  await storeTokens(tokens);

  // Store user data
  const user: AuthUser = {
    id: data.user.id,
    firstName: data.user.first_name || data.user.firstName,
    lastName: data.user.last_name || data.user.lastName,
    email: data.user.email,
    role: data.user.role || 'user',
  };
  
  await storeUser(user);
}

// Check if user is authenticated
export async function isAuthenticated(): Promise<boolean> {
  const tokens = await getTokens();
  return !!tokens?.accessToken;
}
