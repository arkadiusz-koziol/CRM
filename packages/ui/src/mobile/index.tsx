// Mobile-specific UI components and adapters for React Native
import { View, Text, TouchableOpacity, TextInput, StyleSheet } from 'react-native';
import { colors, spacing, typography, borderRadius } from '@skytech/theme/mobile';

// Button component for React Native
export interface MobileButtonProps {
  children: React.ReactNode;
  variant?: 'primary' | 'secondary' | 'ghost' | 'danger';
  size?: 'sm' | 'md' | 'lg';
  disabled?: boolean;
  loading?: boolean;
  onPress?: () => void;
  style?: any;
  testID?: string;
}

export const Button = ({
  children,
  variant = 'primary',
  size = 'md',
  disabled = false,
  loading = false,
  onPress,
  style,
  testID,
}: MobileButtonProps) => {
  const buttonStyles = [
    styles.button,
    styles[`button_${variant}`],
    styles[`button_${size}`],
    disabled && styles.button_disabled,
    style,
  ];

  const textStyles = [
    styles.buttonText,
    styles[`buttonText_${variant}`],
    styles[`buttonText_${size}`],
  ];

  return (
    <TouchableOpacity
      style={buttonStyles}
      onPress={onPress}
      disabled={disabled || loading}
      testID={testID}
      activeOpacity={0.7}
    >
      <Text style={textStyles}>
        {loading ? 'Loading...' : children}
      </Text>
    </TouchableOpacity>
  );
};

// Input component for React Native
export interface MobileInputProps {
  label?: string;
  placeholder?: string;
  value?: string;
  defaultValue?: string;
  disabled?: boolean;
  error?: string;
  helperText?: string;
  style?: any;
  inputStyle?: any;
  testID?: string;
  onChangeText?: (text: string) => void;
  onBlur?: () => void;
  onFocus?: () => void;
}

export const Input = ({
  label,
  placeholder,
  value,
  defaultValue,
  disabled = false,
  error,
  helperText,
  style,
  inputStyle,
  testID,
  onChangeText,
  onBlur,
  onFocus,
}: MobileInputProps) => {
  const inputStyles = [
    styles.input,
    error && styles.input_error,
    disabled && styles.input_disabled,
    inputStyle,
  ];

  return (
    <View style={[styles.inputContainer, style]}>
      {label && (
        <Text style={styles.label}>
          {label}
        </Text>
      )}
      
      <TextInput
        placeholder={placeholder}
        value={value}
        defaultValue={defaultValue}
        editable={!disabled}
        style={inputStyles}
        testID={testID}
        onChangeText={onChangeText}
        onBlur={onBlur}
        onFocus={onFocus}
        placeholderTextColor={colors.neutral[400]}
      />
      
      {error && (
        <Text style={styles.errorText}>
          {error}
        </Text>
      )}
      
      {helperText && !error && (
        <Text style={styles.helperText}>
          {helperText}
        </Text>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  // Button styles
  button: {
    borderRadius: borderRadius.md,
    alignItems: 'center',
    justifyContent: 'center',
  },
  button_primary: {
    backgroundColor: colors.primary[500],
  },
  button_secondary: {
    backgroundColor: colors.secondary[200],
  },
  button_ghost: {
    backgroundColor: 'transparent',
  },
  button_danger: {
    backgroundColor: colors.error[500],
  },
  button_sm: {
    paddingHorizontal: spacing[3],
    paddingVertical: spacing[2],
  },
  button_md: {
    paddingHorizontal: spacing[4],
    paddingVertical: spacing[3],
  },
  button_lg: {
    paddingHorizontal: spacing[6],
    paddingVertical: spacing[4],
  },
  button_disabled: {
    opacity: 0.5,
  },
  buttonText: {
    fontFamily: typography.fontFamily.sans,
    fontWeight: typography.fontWeight.medium,
  },
  buttonText_primary: {
    color: colors.neutral[0],
  },
  buttonText_secondary: {
    color: colors.neutral[900],
  },
  buttonText_ghost: {
    color: colors.neutral[700],
  },
  buttonText_danger: {
    color: colors.neutral[0],
  },
  buttonText_sm: {
    fontSize: typography.fontSize.sm,
  },
  buttonText_md: {
    fontSize: typography.fontSize.base,
  },
  buttonText_lg: {
    fontSize: typography.fontSize.lg,
  },
  
  // Input styles
  inputContainer: {
    marginBottom: spacing[4],
  },
  label: {
    fontSize: typography.fontSize.sm,
    fontWeight: typography.fontWeight.medium,
    color: colors.neutral[700],
    marginBottom: spacing[1],
  },
  input: {
    borderWidth: 1,
    borderColor: colors.neutral[300],
    borderRadius: borderRadius.md,
    paddingHorizontal: spacing[3],
    paddingVertical: spacing[2],
    fontSize: typography.fontSize.base,
    fontFamily: typography.fontFamily.sans,
    color: colors.neutral[900],
    backgroundColor: colors.neutral[0],
  },
  input_error: {
    borderColor: colors.error[500],
  },
  input_disabled: {
    opacity: 0.5,
    backgroundColor: colors.neutral[100],
  },
  errorText: {
    fontSize: typography.fontSize.sm,
    color: colors.error[500],
    marginTop: spacing[1],
  },
  helperText: {
    fontSize: typography.fontSize.sm,
    color: colors.neutral[500],
    marginTop: spacing[1],
  },
});
