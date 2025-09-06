import { useTheme as useThemeContext } from '@/app/providers/ThemeProvider';
import { colors } from './tokens';

export function useTheme() {
  const { isDark, colorScheme } = useThemeContext();
  
  return {
    isDark,
    colorScheme,
    colors: isDark ? colors.dark : colors.light,
  };
}
