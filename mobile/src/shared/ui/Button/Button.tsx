import React from 'react';
import {
  TouchableOpacity,
  Text,
  StyleSheet,
  ActivityIndicator,
  TouchableOpacityProps,
  ViewStyle,
  TextStyle,
} from 'react-native';
import { useTheme } from '@/shared/theme/useTheme';

export type ButtonVariant = 'primary' | 'secondary' | 'ghost' | 'danger';
export type ButtonSize = 'sm' | 'md' | 'lg';

export interface ButtonProps extends TouchableOpacityProps {
  variant?: ButtonVariant;
  size?: ButtonSize;
  loading?: boolean;
  disabled?: boolean;
  children: React.ReactNode;
}

export function Button({
  variant = 'primary',
  size = 'md',
  loading = false,
  disabled = false,
  children,
  style,
  ...props
}: ButtonProps): React.JSX.Element {
  const { colors } = useTheme();
  
  const isDisabled = disabled || loading;
  
  const buttonStyle = [
    styles.base,
    styles[variant],
    styles[size],
    isDisabled && styles.disabled,
    style,
  ];

  const textStyle = [
    styles.text,
    styles[`${variant}Text`],
    styles[`${size}Text`],
    isDisabled && styles.disabledText,
  ];

  return (
    <TouchableOpacity
      style={[buttonStyle, { backgroundColor: getBackgroundColor(variant, colors, isDisabled) }]}
      disabled={isDisabled}
      {...props}
    >
      {loading ? (
        <ActivityIndicator
          size="small"
          color={getTextColor(variant, colors, isDisabled)}
        />
      ) : (
        <Text style={[textStyle, { color: getTextColor(variant, colors, isDisabled) }]}>
          {children}
        </Text>
      )}
    </TouchableOpacity>
  );
}

function getBackgroundColor(variant: ButtonVariant, colors: any, isDisabled: boolean): string {
  if (isDisabled) return colors.disabled;
  
  switch (variant) {
    case 'primary':
      return colors.primary;
    case 'secondary':
      return colors.surface;
    case 'ghost':
      return 'transparent';
    case 'danger':
      return colors.error;
    default:
      return colors.primary;
  }
}

function getTextColor(variant: ButtonVariant, colors: any, isDisabled: boolean): string {
  if (isDisabled) return colors.textTertiary;
  
  switch (variant) {
    case 'primary':
      return colors.background;
    case 'secondary':
      return colors.text;
    case 'ghost':
      return colors.primary;
    case 'danger':
      return colors.background;
    default:
      return colors.background;
  }
}

const styles = StyleSheet.create({
  base: {
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    flexDirection: 'row',
  },
  primary: {
    // backgroundColor handled dynamically
  },
  secondary: {
    borderWidth: 1,
    borderColor: 'currentColor',
  },
  ghost: {
    // backgroundColor: transparent
  },
  danger: {
    // backgroundColor handled dynamically
  },
  sm: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    minHeight: 32,
  },
  md: {
    paddingHorizontal: 16,
    paddingVertical: 12,
    minHeight: 44,
  },
  lg: {
    paddingHorizontal: 24,
    paddingVertical: 16,
    minHeight: 52,
  },
  disabled: {
    opacity: 0.6,
  },
  text: {
    fontWeight: '600',
    textAlign: 'center',
  },
  primaryText: {
    // color handled dynamically
  },
  secondaryText: {
    // color handled dynamically
  },
  ghostText: {
    // color handled dynamically
  },
  dangerText: {
    // color handled dynamically
  },
  smText: {
    fontSize: 14,
  },
  mdText: {
    fontSize: 16,
  },
  lgText: {
    fontSize: 18,
  },
  disabledText: {
    // color handled dynamically
  },
});
