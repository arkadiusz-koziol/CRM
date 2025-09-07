// HTTP client utilities for API SDK
export interface ApiClientConfig {
  baseURL: string;
  timeout?: number;
  headers?: Record<string, string>;
}

export interface ApiRequestConfig {
  method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';
  headers?: Record<string, string>;
  body?: unknown;
  params?: Record<string, string | number | boolean>;
}

export interface ApiResponse<T = unknown> {
  data: T;
  status: number;
  statusText: string;
  headers: Record<string, string>;
}

export interface ApiError {
  message: string;
  status?: number;
  details?: unknown;
}

export class ApiClient {
  private config: ApiClientConfig;

  constructor(config: ApiClientConfig) {
    this.config = config;
  }

  async request<T = unknown>(
    endpoint: string,
    config: ApiRequestConfig = {}
  ): Promise<ApiResponse<T>> {
    const url = new URL(endpoint, this.config.baseURL);
    
    // Add query parameters
    if (config.params) {
      Object.entries(config.params).forEach(([key, value]) => {
        url.searchParams.append(key, String(value));
      });
    }

    const requestConfig: RequestInit = {
      method: config.method || 'GET',
      headers: {
        'Content-Type': 'application/json',
        ...this.config.headers,
        ...config.headers,
      },
      body: config.body ? JSON.stringify(config.body) : undefined,
    };

    try {
      const response = await fetch(url.toString(), requestConfig);
      
      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.message || response.statusText);
      }

      const data = await response.json();
      
      return {
        data,
        status: response.status,
        statusText: response.statusText,
        headers: Object.fromEntries(response.headers.entries()),
      };
    } catch (error) {
      if (error instanceof Error) {
        throw error;
      }
      throw new Error('Network error occurred');
    }
  }

  // Convenience methods
  async get<T = unknown>(endpoint: string, config?: Omit<ApiRequestConfig, 'method' | 'body'>) {
    return this.request<T>(endpoint, { ...config, method: 'GET' });
  }

  async post<T = unknown>(endpoint: string, body?: unknown, config?: Omit<ApiRequestConfig, 'method'>) {
    return this.request<T>(endpoint, { ...config, method: 'POST', body });
  }

  async put<T = unknown>(endpoint: string, body?: unknown, config?: Omit<ApiRequestConfig, 'method'>) {
    return this.request<T>(endpoint, { ...config, method: 'PUT', body });
  }

  async patch<T = unknown>(endpoint: string, body?: unknown, config?: Omit<ApiRequestConfig, 'method'>) {
    return this.request<T>(endpoint, { ...config, method: 'PATCH', body });
  }

  async delete<T = unknown>(endpoint: string, config?: Omit<ApiRequestConfig, 'method' | 'body'>) {
    return this.request<T>(endpoint, { ...config, method: 'DELETE' });
  }
}

// Factory function to create API client
export function createApiClient(config: ApiClientConfig): ApiClient {
  return new ApiClient(config);
}
