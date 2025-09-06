import React from 'react';
import { StatusBar } from 'react-native';
import { QueryProvider } from '@/shared/lib/queryClient';
import { ThemeProvider } from '@/shared/theme/ThemeProvider';
import { I18nProvider } from '@/shared/lib/i18n';
import { AppNavigator } from '@/navigation/AppNavigator';
import { ErrorBoundary } from '@/shared/components/ErrorBoundary';

export default function App(): React.JSX.Element {
  return (
    <ErrorBoundary>
      <QueryProvider>
        <ThemeProvider>
          <I18nProvider>
            <StatusBar barStyle="dark-content" backgroundColor="#ffffff" />
            <AppNavigator />
          </I18nProvider>
        </ThemeProvider>
      </QueryProvider>
    </ErrorBoundary>
  );
}
