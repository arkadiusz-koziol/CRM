import { httpClient } from '@/shared/lib/httpClient';

// Mock the httpClient for testing
export const mockHttpClient = {
  get: jest.fn(),
  post: jest.fn(),
  patch: jest.fn(),
  delete: jest.fn(),
  request: jest.fn(),
  getRequestId: jest.fn(() => 'test-request-id'),
};

// Replace the actual httpClient with our mock
jest.mock('@/shared/lib/httpClient', () => ({
  httpClient: mockHttpClient,
  http: jest.fn(),
  parseWithZod: jest.fn((schema, data) => schema(data)),
}));

export { mockHttpClient as httpClient };
