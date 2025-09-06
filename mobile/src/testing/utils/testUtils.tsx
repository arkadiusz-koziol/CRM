import React from 'react';
import { render, RenderOptions } from '@testing-library/react-native';
import { TestQueryProvider } from '../mocks/reactQuery';

// Custom render function that includes providers
function customRender(
  ui: React.ReactElement,
  options?: Omit<RenderOptions, 'wrapper'>,
): ReturnType<typeof render> {
  function Wrapper({ children }: { children: React.ReactNode }): React.JSX.Element {
    return <TestQueryProvider>{children}</TestQueryProvider>;
  }

  return render(ui, { wrapper: Wrapper, ...options });
}

// Re-export everything
export * from '@testing-library/react-native';
export { customRender as render };
