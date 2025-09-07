import { tokens } from './tokens';

// CSS variables for web
export const cssVariables = {
  // Colors
  '--color-primary-50': tokens.colors.primary[50],
  '--color-primary-100': tokens.colors.primary[100],
  '--color-primary-200': tokens.colors.primary[200],
  '--color-primary-300': tokens.colors.primary[300],
  '--color-primary-400': tokens.colors.primary[400],
  '--color-primary-500': tokens.colors.primary[500],
  '--color-primary-600': tokens.colors.primary[600],
  '--color-primary-700': tokens.colors.primary[700],
  '--color-primary-800': tokens.colors.primary[800],
  '--color-primary-900': tokens.colors.primary[900],
  '--color-primary-950': tokens.colors.primary[950],
  
  '--color-secondary-50': tokens.colors.secondary[50],
  '--color-secondary-100': tokens.colors.secondary[100],
  '--color-secondary-200': tokens.colors.secondary[200],
  '--color-secondary-300': tokens.colors.secondary[300],
  '--color-secondary-400': tokens.colors.secondary[400],
  '--color-secondary-500': tokens.colors.secondary[500],
  '--color-secondary-600': tokens.colors.secondary[600],
  '--color-secondary-700': tokens.colors.secondary[700],
  '--color-secondary-800': tokens.colors.secondary[800],
  '--color-secondary-900': tokens.colors.secondary[900],
  '--color-secondary-950': tokens.colors.secondary[950],
  
  '--color-success-50': tokens.colors.success[50],
  '--color-success-500': tokens.colors.success[500],
  '--color-success-600': tokens.colors.success[600],
  '--color-success-700': tokens.colors.success[700],
  
  '--color-warning-50': tokens.colors.warning[50],
  '--color-warning-500': tokens.colors.warning[500],
  '--color-warning-600': tokens.colors.warning[600],
  '--color-warning-700': tokens.colors.warning[700],
  
  '--color-error-50': tokens.colors.error[50],
  '--color-error-500': tokens.colors.error[500],
  '--color-error-600': tokens.colors.error[600],
  '--color-error-700': tokens.colors.error[700],
  
  '--color-neutral-0': tokens.colors.neutral[0],
  '--color-neutral-50': tokens.colors.neutral[50],
  '--color-neutral-100': tokens.colors.neutral[100],
  '--color-neutral-200': tokens.colors.neutral[200],
  '--color-neutral-300': tokens.colors.neutral[300],
  '--color-neutral-400': tokens.colors.neutral[400],
  '--color-neutral-500': tokens.colors.neutral[500],
  '--color-neutral-600': tokens.colors.neutral[600],
  '--color-neutral-700': tokens.colors.neutral[700],
  '--color-neutral-800': tokens.colors.neutral[800],
  '--color-neutral-900': tokens.colors.neutral[900],
  '--color-neutral-950': tokens.colors.neutral[950],
  
  // Spacing
  '--spacing-0': tokens.spacing[0],
  '--spacing-1': tokens.spacing[1],
  '--spacing-2': tokens.spacing[2],
  '--spacing-3': tokens.spacing[3],
  '--spacing-4': tokens.spacing[4],
  '--spacing-5': tokens.spacing[5],
  '--spacing-6': tokens.spacing[6],
  '--spacing-8': tokens.spacing[8],
  '--spacing-10': tokens.spacing[10],
  '--spacing-12': tokens.spacing[12],
  '--spacing-16': tokens.spacing[16],
  '--spacing-20': tokens.spacing[20],
  '--spacing-24': tokens.spacing[24],
  '--spacing-32': tokens.spacing[32],
  '--spacing-40': tokens.spacing[40],
  '--spacing-48': tokens.spacing[48],
  '--spacing-56': tokens.spacing[56],
  '--spacing-64': tokens.spacing[64],
  
  // Typography
  '--font-family-sans': tokens.typography.fontFamily.sans.join(', '),
  '--font-family-mono': tokens.typography.fontFamily.mono.join(', '),
  
  // Border radius
  '--radius-none': tokens.borderRadius.none,
  '--radius-sm': tokens.borderRadius.sm,
  '--radius-base': tokens.borderRadius.base,
  '--radius-md': tokens.borderRadius.md,
  '--radius-lg': tokens.borderRadius.lg,
  '--radius-xl': tokens.borderRadius.xl,
  '--radius-2xl': tokens.borderRadius['2xl'],
  '--radius-3xl': tokens.borderRadius['3xl'],
  '--radius-full': tokens.borderRadius.full,
  
  // Shadows
  '--shadow-sm': tokens.boxShadow.sm,
  '--shadow-base': tokens.boxShadow.base,
  '--shadow-md': tokens.boxShadow.md,
  '--shadow-lg': tokens.boxShadow.lg,
  '--shadow-xl': tokens.boxShadow.xl,
  '--shadow-2xl': tokens.boxShadow['2xl'],
  '--shadow-inner': tokens.boxShadow.inner,
  '--shadow-none': tokens.boxShadow.none,
  
  // Z-index
  '--z-hide': tokens.zIndex.hide,
  '--z-auto': tokens.zIndex.auto,
  '--z-base': tokens.zIndex.base,
  '--z-docked': tokens.zIndex.docked,
  '--z-dropdown': tokens.zIndex.dropdown,
  '--z-sticky': tokens.zIndex.sticky,
  '--z-banner': tokens.zIndex.banner,
  '--z-overlay': tokens.zIndex.overlay,
  '--z-modal': tokens.zIndex.modal,
  '--z-popover': tokens.zIndex.popover,
  '--z-skip-link': tokens.zIndex.skipLink,
  '--z-toast': tokens.zIndex.toast,
  '--z-tooltip': tokens.zIndex.tooltip,
} as const;

export function generateCSSVariables(): string {
  return Object.entries(cssVariables)
    .map(([key, value]) => `${key}: ${value};`)
    .join('\n');
}

export function applyCSSVariables(element: HTMLElement = document.documentElement): void {
  Object.entries(cssVariables).forEach(([key, value]) => {
    element.style.setProperty(key, String(value));
  });
}
