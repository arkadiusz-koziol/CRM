import React from 'react';
import { View, StyleSheet, Alert } from 'react-native';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useTranslation } from 'react-i18next';
import { Button } from '@/shared/ui/Button';
import { Input } from '@/shared/ui/Input';
import { useTheme } from '@/shared/theme/useTheme';
import { useAuth } from '../hooks/useAuth';

const loginSchema = z.object({
  email: z.string().email('Invalid email address'),
  password: z.string().min(6, 'Password must be at least 6 characters'),
});

type LoginFormData = z.infer<typeof loginSchema>;

export function LoginScreen(): React.JSX.Element {
  const { t } = useTranslation();
  const { colors } = useTheme();
  const { login, isLoggingIn } = useAuth();

  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm<LoginFormData>({
    resolver: zodResolver(loginSchema),
  });

  const onSubmit = (data: LoginFormData): void => {
    login(data, {
      onError: (error) => {
        Alert.alert('Login Failed', error.message || 'Please try again');
      },
    });
  };

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <View style={styles.form}>
        <Input
          control={control}
          name="email"
          label={t('auth.email')}
          placeholder="Enter your email"
          keyboardType="email-address"
          autoCapitalize="none"
          error={errors.email?.message}
        />
        
        <Input
          control={control}
          name="password"
          label={t('auth.password')}
          placeholder="Enter your password"
          secureTextEntry
          error={errors.password?.message}
        />
        
        <Button
          variant="primary"
          onPress={handleSubmit(onSubmit)}
          loading={isLoggingIn}
          style={styles.loginButton}
        >
          {t('auth.login')}
        </Button>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    padding: 20,
  },
  form: {
    maxWidth: 400,
    width: '100%',
    alignSelf: 'center',
  },
  loginButton: {
    marginTop: 20,
  },
});
