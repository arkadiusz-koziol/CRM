import React, { forwardRef } from 'react';
import {
  TextInput,
  View,
  Text,
  StyleSheet,
  TextInputProps,
  ViewStyle,
  TextStyle,
} from 'react-native';
import { useTheme } from '@/shared/theme/useTheme';

export interface InputProps extends TextInputProps {
  label?: string;
  error?: string;
  helperText?: string;
  required?: boolean;
  containerStyle?: ViewStyle;
  inputStyle?: TextStyle;
  labelStyle?: TextStyle;
}

export const Input = forwardRef<TextInput, InputProps>(
  (
    {
      label,
      error,
      helperText,
      required = false,
      containerStyle,
      inputStyle,
      labelStyle,
      style,
      ...props
    },
    ref,
  ): React.JSX.Element => {
    const { colors } = useTheme();
    
    const hasError = Boolean(error);
    const inputId = `input-${Math.random().toString(36).substr(2, 9)}`;
    const errorId = `${inputId}-error`;
    const helperId = `${inputId}-helper`;

    return (
      <View style={[styles.container, containerStyle]}>
        {label && (
          <Text
            style={[
              styles.label,
              { color: colors.text },
              hasError && { color: colors.error },
              labelStyle,
            ]}
          >
            {label}
            {required && <Text style={{ color: colors.error }}> *</Text>}
          </Text>
        )}
        
        <TextInput
          ref={ref}
          id={inputId}
          style={[
            styles.input,
            {
              color: colors.text,
              backgroundColor: colors.surface,
              borderColor: hasError ? colors.error : colors.border,
            },
            inputStyle,
            style,
          ]}
          placeholderTextColor={colors.placeholder}
          accessibilityLabel={label}
          accessibilityInvalid={hasError}
          accessibilityDescribedBy={hasError ? errorId : helperText ? helperId : undefined}
          {...props}
        />
        
        {error && (
          <Text
            id={errorId}
            style={[styles.errorText, { color: colors.error }]}
            role="alert"
          >
            {error}
          </Text>
        )}
        
        {helperText && !error && (
          <Text
            id={helperId}
            style={[styles.helperText, { color: colors.textSecondary }]}
          >
            {helperText}
          </Text>
        )}
      </View>
    );
  },
);

Input.displayName = 'Input';

const styles = StyleSheet.create({
  container: {
    marginBottom: 16,
  },
  label: {
    fontSize: 16,
    fontWeight: '500',
    marginBottom: 8,
  },
  input: {
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 12,
    fontSize: 16,
    minHeight: 44,
  },
  errorText: {
    fontSize: 14,
    marginTop: 4,
  },
  helperText: {
    fontSize: 14,
    marginTop: 4,
  },
});
