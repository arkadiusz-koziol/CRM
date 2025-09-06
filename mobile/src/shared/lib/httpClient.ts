import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios';
import NetInfo from '@react-native-community/netinfo';
import { z } from 'zod';

export type HttpError = Error & {
  status?: number;
  code?: string;
  details?: unknown;
};

export type NetworkError = HttpError & { type: 'network' };
export type AuthError = HttpError & { type: 'auth' };
export type ValidationError = HttpError & { type: 'validation' };
export type ConflictError = HttpError & { type: 'conflict' };
export type ServerError = HttpError & { type: 'server' };

export type ApiError = NetworkError | AuthError | ValidationError | ConflictError | ServerError;

const API_BASE_URL = process.env.API_URL || 'https://api.telemain.com';

class HttpClient {
  private client: AxiosInstance;
  private requestId: string | null = null;

  constructor() {
    this.client = axios.create({
      baseURL: API_BASE_URL,
      timeout: 15000,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/vnd.api+json',
      },
    });

    this.setupInterceptors();
  }

  private setupInterceptors(): void {
    // Request interceptor
    this.client.interceptors.request.use(
      (config) => {
        // Add request ID for tracing
        this.requestId = this.generateRequestId();
        config.headers['X-Request-Id'] = this.requestId;

        // Add auth token if available
        // This will be implemented when auth is added
        return config;
      },
      (error) => Promise.reject(error),
    );

    // Response interceptor
    this.client.interceptors.response.use(
      (response) => response,
      async (error) => {
        const networkState = await NetInfo.fetch();
        
        if (!networkState.isConnected) {
          throw this.createError('Network error', 'network', {
            message: 'No internet connection',
            status: 0,
          });
        }

        return Promise.reject(this.mapHttpError(error));
      },
    );
  }

  private generateRequestId(): string {
    return `req_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
  }

  private createError(
    message: string,
    type: ApiError['type'],
    details: { status?: number; code?: string; details?: unknown },
  ): ApiError {
    const error = new Error(message) as ApiError;
    error.type = type;
    error.status = details.status;
    error.code = details.code;
    error.details = details.details;
    return error;
  }

  private mapHttpError(error: any): ApiError {
    const response = error.response;
    const status = response?.status || 0;
    const message = response?.data?.message || error.message || 'Unknown error';
    const details = response?.data;

    switch (true) {
      case status === 0:
        return this.createError('Network error', 'network', { status, details });
      case status === 401:
      case status === 403:
        return this.createError(message, 'auth', { status, details });
      case status === 422:
        return this.createError(message, 'validation', { status, details });
      case status === 409:
        return this.createError(message, 'conflict', { status, details });
      case status >= 500:
        return this.createError(message, 'server', { status, details });
      default:
        return this.createError(message, 'network', { status, details });
    }
  }

  async request<T>(
    config: AxiosRequestConfig & { schema?: (data: unknown) => T },
  ): Promise<T> {
    try {
      const response: AxiosResponse = await this.client.request(config);
      const data = response.data;

      if (config.schema) {
        return config.schema(data);
      }

      return data as T;
    } catch (error) {
      throw error;
    }
  }

  async get<T>(
    url: string,
    config?: AxiosRequestConfig & { schema?: (data: unknown) => T },
  ): Promise<T> {
    return this.request<T>({ ...config, method: 'GET', url });
  }

  async post<T>(
    url: string,
    data?: unknown,
    config?: AxiosRequestConfig & { schema?: (data: unknown) => T },
  ): Promise<T> {
    return this.request<T>({ ...config, method: 'POST', url, data });
  }

  async patch<T>(
    url: string,
    data?: unknown,
    config?: AxiosRequestConfig & { schema?: (data: unknown) => T },
  ): Promise<T> {
    return this.request<T>({ ...config, method: 'PATCH', url, data });
  }

  async delete<T>(
    url: string,
    config?: AxiosRequestConfig & { schema?: (data: unknown) => T },
  ): Promise<T> {
    return this.request<T>({ ...config, method: 'DELETE', url });
  }

  getRequestId(): string | null {
    return this.requestId;
  }
}

export const httpClient = new HttpClient();

// Convenience function for backward compatibility
export async function http<T>(
  path: string,
  init?: {
    method?: 'GET' | 'POST' | 'PATCH' | 'DELETE';
    data?: unknown;
    schema?: (d: unknown) => T;
    headers?: Record<string, string>;
  },
): Promise<T> {
  const { method = 'GET', data, schema, headers } = init || {};
  
  return httpClient.request({
    method,
    url: path,
    data,
    schema,
    headers,
  });
}

// Zod helper for runtime validation
export function parseWithZod<T>(schema: (data: unknown) => T, data: unknown): T {
  try {
    return schema(data);
  } catch (error) {
    if (error instanceof z.ZodError) {
      throw new Error(`Validation error: ${error.errors.map(e => e.message).join(', ')}`);
    }
    throw error;
  }
}
