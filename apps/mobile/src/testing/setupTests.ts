import '@testing-library/jest-native/extend-expect';

// Mock AsyncStorage
jest.mock('@react-native-async-storage/async-storage', () =>
  require('@react-native-async-storage/async-storage/jest/async-storage-mock')
);

// Mock expo-secure-store
jest.mock('expo-secure-store', () => ({
  getItemAsync: jest.fn(),
  setItemAsync: jest.fn(),
  deleteItemAsync: jest.fn(),
}));

// Mock expo-auth-session
jest.mock('expo-auth-session', () => ({
  AuthRequest: {
    createRandomCodeChallenge: jest.fn(() => 'mock-code-verifier'),
  },
  ResponseType: {
    Code: 'code',
  },
  CodeChallengeMethod: {
    S256: 'S256',
  },
  makeRedirectUri: jest.fn(() => 'skytech://auth/callback'),
  promptAsync: jest.fn(),
}));

// Mock expo-crypto
jest.mock('expo-crypto', () => ({
  digestStringAsync: jest.fn(() => Promise.resolve('mock-code-challenge')),
  CryptoDigestAlgorithm: {
    SHA256: 'SHA256',
  },
  CryptoEncoding: {
    BASE64URL: 'base64url',
  },
}));
