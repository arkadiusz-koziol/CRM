import { createApiClient, ApiClient, ApiRequestConfig } from '@skytech/api-sdk/client';
import { getMobileEnv } from '@skytech/config';

// Create API client instance
const env = getMobileEnv();
export const apiClient = createApiClient({
  baseURL: env.API_URL,
  timeout: env.API_TIMEOUT,
});

// HTTP client wrapper with error handling
export class HttpClient {
  private client: ApiClient;

  constructor() {
    this.client = apiClient;
  }

  async request<T = unknown>(
    endpoint: string,
    config: ApiRequestConfig = {}
  ): Promise<T> {
    try {
      const response = await this.client.request<T>(endpoint, config);
      return response.data;
    } catch (error) {
      // Map HTTP errors to application errors
      if (error instanceof Error) {
        throw new Error(error.message);
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

// Export singleton instance
export const httpClient = new HttpClient();
