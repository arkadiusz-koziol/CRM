// Environment configuration
const env = {
  API_URL: process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8199',
  AUTH_CLIENT_ID: process.env.NEXT_PUBLIC_AUTH_CLIENT_ID || 'skytech-web',
};

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

// Store tokens securely
async function storeTokens(tokens: AuthTokens): Promise<void> {
  localStorage.setItem(TOKENS_KEY, JSON.stringify(tokens));
}

// Get stored tokens
export async function getTokens(): Promise<AuthTokens | null> {
  try {
    const tokensJson = localStorage.getItem(TOKENS_KEY);
    return tokensJson ? JSON.parse(tokensJson) : null;
  } catch (error) {
    console.error('Failed to get tokens:', error);
    return null;
  }
}

// Store user data
export async function storeUser(user: AuthUser): Promise<void> {
  localStorage.setItem(USER_KEY, JSON.stringify(user));
}

// Get stored user
export async function getUser(): Promise<AuthUser | null> {
  try {
    const userJson = localStorage.getItem(USER_KEY);
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

// Sign in with email and password
export async function signInWithCredentials(email: string, password: string): Promise<void> {
  const response = await fetch(`${env.API_URL}/auth/login`, {
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
    throw new Error(errorData.error || errorData.message || 'Login failed');
  }

  const data = await response.json();
  
  // Backend returns data in JSON:API format: { data: { user: {...}, token: "..." } }
  const { user: userData, token } = data.data;
  
  // Store tokens
  const tokens: AuthTokens = {
    accessToken: token,
    refreshToken: undefined, // Backend doesn't provide refresh token
    expiresIn: 3600, // Default 1 hour
    tokenType: 'Bearer',
    scope: undefined,
    idToken: undefined,
  };
  
  await storeTokens(tokens);

  // Store user data
  const user: AuthUser = {
    id: userData.id.toString(),
    firstName: userData.name || userData.first_name || userData.firstName || '',
    lastName: userData.surname || userData.last_name || userData.lastName || '',
    email: userData.email,
    role: 'admin', // Default role, you can get this from userData if available
  };
  
  await storeUser(user);
}

// Sign in with OAuth PKCE
export async function signInWithPKCE(): Promise<void> {
  // Redirect to OAuth provider
  const authUrl = `${env.API_URL}/auth/oauth/authorize?client_id=${env.AUTH_CLIENT_ID}&response_type=code&redirect_uri=${encodeURIComponent(window.location.origin + '/auth/callback')}&scope=openid profile email`;
  window.location.href = authUrl;
}

// Sign out
export async function signOut(): Promise<void> {
  try {
    localStorage.removeItem(TOKENS_KEY);
    localStorage.removeItem(USER_KEY);
  } catch (error) {
    console.error('Failed to sign out:', error);
  }
}

// Check if user is authenticated
export async function isAuthenticated(): Promise<boolean> {
  // Check if we're on the client side
  if (typeof window === 'undefined') {
    return false;
  }
  
  try {
    const tokens = await getTokens();
    return !!tokens?.accessToken;
  } catch (error) {
    console.error('Error checking authentication:', error);
    return false;
  }
}
