import { ExpoConfig, ConfigPlugin } from 'expo/config';
import { EnvSchema } from '@skytech/config';

const config: ExpoConfig = {
  name: 'Skytech',
  slug: 'skytech-mobile',
  version: '1.0.0',
  orientation: 'portrait',
  icon: './assets/icon.png',
  userInterfaceStyle: 'light',
  splash: {
    image: './assets/splash.png',
    resizeMode: 'contain',
    backgroundColor: '#ffffff',
  },
  assetBundlePatterns: ['**/*'],
  ios: {
    supportsTablet: true,
    bundleIdentifier: 'com.skytech.mobile',
  },
  android: {
    adaptiveIcon: {
      foregroundImage: './assets/adaptive-icon.png',
      backgroundColor: '#ffffff',
    },
    package: 'com.skytech.mobile',
  },
  web: {
    favicon: './assets/favicon.png',
  },
  scheme: 'skytech',
  extra: {
    // Environment variables will be injected here
    EXPO_PUBLIC_API_URL: process.env.EXPO_PUBLIC_API_URL,
    EXPO_PUBLIC_SENTRY_DSN: process.env.EXPO_PUBLIC_SENTRY_DSN,
    EXPO_PUBLIC_AUTH_CLIENT_ID: process.env.EXPO_PUBLIC_AUTH_CLIENT_ID,
    EXPO_PUBLIC_AUTH_DISCOVERY_URL: process.env.EXPO_PUBLIC_AUTH_DISCOVERY_URL,
    EXPO_PUBLIC_SENTRY_ENVIRONMENT: process.env.EXPO_PUBLIC_SENTRY_ENVIRONMENT,
    EXPO_PUBLIC_FEATURE_OFFLINE_MODE: process.env.EXPO_PUBLIC_FEATURE_OFFLINE_MODE,
    EXPO_PUBLIC_FEATURE_DEBUG_LOGS: process.env.EXPO_PUBLIC_FEATURE_DEBUG_LOGS,
    EXPO_PUBLIC_APP_NAME: process.env.EXPO_PUBLIC_APP_NAME,
    EXPO_PUBLIC_APP_VERSION: process.env.EXPO_PUBLIC_APP_VERSION,
    eas: {
      projectId: "102a15d8-a8fc-4d8f-8158-1665b0388ef8"
    }
  },
  plugins: [
    'expo-router',
    'expo-secure-store',
    'expo-updates',
    [
      'expo-build-properties',
      {
        ios: {
          deploymentTarget: '13.4',
        },
        android: {
          compileSdkVersion: 34,
          targetSdkVersion: 34,
          minSdkVersion: 23,
        },
      },
    ],
  ],
};

export default config;
