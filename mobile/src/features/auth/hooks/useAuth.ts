import { useState, useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';

const AUTH_STORAGE_KEY = 'auth_tokens';

interface AuthTokens {
  accessToken: string;
  refreshToken: string;
  expiresAt: number;
}

interface User {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  role: string;
}

export function useAuth() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const queryClient = useQueryClient();

  // Check if user is authenticated on app start
  useEffect(() => {
    const checkAuthStatus = async (): Promise<void> => {
      try {
        const tokens = await AsyncStorage.getItem(AUTH_STORAGE_KEY);
        if (tokens) {
          const parsedTokens: AuthTokens = JSON.parse(tokens);
          const now = Date.now();
          
          if (parsedTokens.expiresAt > now) {
            setIsAuthenticated(true);
          } else {
            // Token expired, try to refresh
            await refreshTokens(parsedTokens.refreshToken);
          }
        }
      } catch (error) {
        console.warn('Failed to check auth status:', error);
      } finally {
        setIsLoading(false);
      }
    };

    checkAuthStatus();
  }, []);

  const refreshTokens = async (refreshToken: string): Promise<boolean> => {
    try {
      // This would call your refresh token endpoint
      // const response = await httpClient.post('/auth/refresh', { refreshToken });
      // For now, just return false
      return false;
    } catch (error) {
      console.warn('Failed to refresh tokens:', error);
      return false;
    }
  };

  const loginMutation = useMutation({
    mutationFn: async (credentials: { email: string; password: string }) => {
      // This would call your login endpoint
      // const response = await httpClient.post('/auth/login', credentials);
      // For now, just simulate a successful login
      return {
        accessToken: 'fake-access-token',
        refreshToken: 'fake-refresh-token',
        expiresAt: Date.now() + 3600000, // 1 hour
        user: {
          id: '1',
          email: credentials.email,
          firstName: 'John',
          lastName: 'Doe',
          role: 'technician',
        },
      };
    },
    onSuccess: async (data) => {
      const tokens: AuthTokens = {
        accessToken: data.accessToken,
        refreshToken: data.refreshToken,
        expiresAt: data.expiresAt,
      };
      
      await AsyncStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(tokens));
      setIsAuthenticated(true);
    },
  });

  const logoutMutation = useMutation({
    mutationFn: async () => {
      await AsyncStorage.removeItem(AUTH_STORAGE_KEY);
      queryClient.clear();
      setIsAuthenticated(false);
    },
  });

  return {
    isAuthenticated,
    isLoading,
    login: loginMutation.mutate,
    logout: logoutMutation.mutate,
    isLoggingIn: loginMutation.isPending,
    isLoggingOut: logoutMutation.isPending,
  };
}
