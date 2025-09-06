import React, { useEffect } from 'react';
import { initReactI18next } from 'react-i18next';
import i18n from 'i18next';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { NativeModules, Platform } from 'react-native';

// Import translation files
import en from '@/shared/lib/i18n/locales/en.json';
import pl from '@/shared/lib/i18n/locales/pl.json';
import es from '@/shared/lib/i18n/locales/es.json';

const LANGUAGE_DETECTOR = {
  type: 'languageDetector' as const,
  async: true,
  detect: async (callback: (lng: string) => void): Promise<void> => {
    try {
      const savedLanguage = await AsyncStorage.getItem('user-language');
      if (savedLanguage) {
        callback(savedLanguage);
        return;
      }

      // Fallback to device language
      const deviceLanguage =
        Platform.OS === 'ios'
          ? NativeModules.SettingsManager?.settings?.AppleLocale ||
            NativeModules.SettingsManager?.settings?.AppleLanguages?.[0]
          : NativeModules.I18nManager?.localeIdentifier;

      const supportedLanguages = ['en', 'pl', 'es'];
      const language = supportedLanguages.find(lng => deviceLanguage?.startsWith(lng)) || 'en';
      callback(language);
    } catch (error) {
      console.warn('Failed to detect language:', error);
      callback('en');
    }
  },
  init: (): void => {},
  cacheUserLanguage: async (lng: string): Promise<void> => {
    try {
      await AsyncStorage.setItem('user-language', lng);
    } catch (error) {
      console.warn('Failed to cache language:', error);
    }
  },
};

i18n
  .use(LANGUAGE_DETECTOR)
  .use(initReactI18next)
  .init({
    compatibilityJSON: 'v3',
    fallbackLng: 'en',
    debug: __DEV__,
    resources: {
      en: { translation: en },
      pl: { translation: pl },
      es: { translation: es },
    },
    interpolation: {
      escapeValue: false,
    },
  });

export function I18nProvider({ children }: { children: React.ReactNode }): React.JSX.Element {
  useEffect(() => {
    // i18n is already initialized
  }, []);

  return <>{children}</>;
}
