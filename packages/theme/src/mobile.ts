import { tokens } from './tokens';

// React Native StyleSheet helpers
export const createStyleSheet = (styles: Record<string, any>) => styles;

// Color helpers for React Native
export const colors = {
  primary: {
    50: tokens.colors.primary[50],
    100: tokens.colors.primary[100],
    200: tokens.colors.primary[200],
    300: tokens.colors.primary[300],
    400: tokens.colors.primary[400],
    500: tokens.colors.primary[500],
    600: tokens.colors.primary[600],
    700: tokens.colors.primary[700],
    800: tokens.colors.primary[800],
    900: tokens.colors.primary[900],
    950: tokens.colors.primary[950],
  },
  secondary: {
    50: tokens.colors.secondary[50],
    100: tokens.colors.secondary[100],
    200: tokens.colors.secondary[200],
    300: tokens.colors.secondary[300],
    400: tokens.colors.secondary[400],
    500: tokens.colors.secondary[500],
    600: tokens.colors.secondary[600],
    700: tokens.colors.secondary[700],
    800: tokens.colors.secondary[800],
    900: tokens.colors.secondary[900],
    950: tokens.colors.secondary[950],
  },
  success: {
    50: tokens.colors.success[50],
    500: tokens.colors.success[500],
    600: tokens.colors.success[600],
    700: tokens.colors.success[700],
  },
  warning: {
    50: tokens.colors.warning[50],
    500: tokens.colors.warning[500],
    600: tokens.colors.warning[600],
    700: tokens.colors.warning[700],
  },
  error: {
    50: tokens.colors.error[50],
    500: tokens.colors.error[500],
    600: tokens.colors.error[600],
    700: tokens.colors.error[700],
  },
  neutral: {
    0: tokens.colors.neutral[0],
    50: tokens.colors.neutral[50],
    100: tokens.colors.neutral[100],
    200: tokens.colors.neutral[200],
    300: tokens.colors.neutral[300],
    400: tokens.colors.neutral[400],
    500: tokens.colors.neutral[500],
    600: tokens.colors.neutral[600],
    700: tokens.colors.neutral[700],
    800: tokens.colors.neutral[800],
    900: tokens.colors.neutral[900],
    950: tokens.colors.neutral[950],
  },
} as const;

// Spacing helpers
export const spacing = {
  0: 0,
  1: 4,    // 4px
  2: 8,    // 8px
  3: 12,   // 12px
  4: 16,   // 16px
  5: 20,   // 20px
  6: 24,   // 24px
  8: 32,   // 32px
  10: 40,  // 40px
  12: 48,  // 48px
  16: 64,  // 64px
  20: 80,  // 80px
  24: 96,  // 96px
  32: 128, // 128px
  40: 160, // 160px
  48: 192, // 192px
  56: 224, // 224px
  64: 256, // 256px
} as const;

// Typography helpers
export const typography = {
  fontFamily: {
    sans: tokens.typography.fontFamily.sans[0], // Use first font as primary
    mono: tokens.typography.fontFamily.mono[0],
  },
  fontSize: {
    xs: 12,
    sm: 14,
    base: 16,
    lg: 18,
    xl: 20,
    '2xl': 24,
    '3xl': 30,
    '4xl': 36,
    '5xl': 48,
    '6xl': 60,
    '7xl': 72,
    '8xl': 96,
    '9xl': 128,
  },
  lineHeight: {
    xs: 16,
    sm: 20,
    base: 24,
    lg: 28,
    xl: 28,
    '2xl': 32,
    '3xl': 36,
    '4xl': 40,
    '5xl': 48,
    '6xl': 60,
    '7xl': 72,
    '8xl': 96,
    '9xl': 128,
  },
  fontWeight: {
    thin: '100' as const,
    extralight: '200' as const,
    light: '300' as const,
    normal: '400' as const,
    medium: '500' as const,
    semibold: '600' as const,
    bold: '700' as const,
    extrabold: '800' as const,
    black: '900' as const,
  },
} as const;

// Border radius helpers
export const borderRadius = {
  none: 0,
  sm: 2,
  base: 4,
  md: 6,
  lg: 8,
  xl: 12,
  '2xl': 16,
  '3xl': 24,
  full: 9999,
} as const;

// Shadow helpers (React Native shadow properties)
export const shadows = {
  sm: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  base: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 2,
  },
  md: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 6,
    elevation: 4,
  },
  lg: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.1,
    shadowRadius: 15,
    elevation: 8,
  },
  xl: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 20 },
    shadowOpacity: 0.1,
    shadowRadius: 25,
    elevation: 12,
  },
  '2xl': {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 25 },
    shadowOpacity: 0.25,
    shadowRadius: 50,
    elevation: 16,
  },
} as const;

// Z-index helpers
export const zIndex = {
  hide: -1,
  auto: 'auto' as const,
  base: 0,
  docked: 10,
  dropdown: 1000,
  sticky: 1100,
  banner: 1200,
  overlay: 1300,
  modal: 1400,
  popover: 1500,
  skipLink: 1600,
  toast: 1700,
  tooltip: 1800,
} as const;

// Common style presets
export const presets = {
  container: {
    flex: 1,
    backgroundColor: colors.neutral[0],
  },
  center: {
    flex: 1,
    justifyContent: 'center' as const,
    alignItems: 'center' as const,
  },
  row: {
    flexDirection: 'row' as const,
    alignItems: 'center' as const,
  },
  column: {
    flexDirection: 'column' as const,
  },
  text: {
    fontFamily: typography.fontFamily.sans,
    fontSize: typography.fontSize.base,
    lineHeight: typography.lineHeight.base,
    color: colors.neutral[900],
  },
  heading: {
    fontFamily: typography.fontFamily.sans,
    fontWeight: typography.fontWeight.bold,
    color: colors.neutral[900],
  },
  button: {
    paddingHorizontal: spacing[4],
    paddingVertical: spacing[3],
    borderRadius: borderRadius.md,
    alignItems: 'center' as const,
    justifyContent: 'center' as const,
  },
  input: {
    paddingHorizontal: spacing[4],
    paddingVertical: spacing[3],
    borderRadius: borderRadius.md,
    borderWidth: 1,
    borderColor: colors.neutral[300],
    backgroundColor: colors.neutral[0],
    fontFamily: typography.fontFamily.sans,
    fontSize: typography.fontSize.base,
    color: colors.neutral[900],
  },
} as const;
