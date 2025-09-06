import React from 'react';
import { View, StyleSheet } from 'react-native';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useTranslation } from 'react-i18next';
import { Button } from '@/shared/ui/Button';
import { Input } from '@/shared/ui/Input';
import { useTheme } from '@/shared/theme/useTheme';

const resetPasswordSchema = z.object({
  password: z.string().min(8, 'Password must be at least 8 characters'),
  confirmPassword: z.string(),
}).refine((data) => data.password === data.confirmPassword, {
  message: "Passwords don't match",
  path: ["confirmPassword"],
});

type ResetPasswordFormData = z.infer<typeof resetPasswordSchema>;

export function ResetPasswordScreen(): React.JSX.Element {
  const { t } = useTranslation();
  const { colors } = useTheme();

  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm<ResetPasswordFormData>({
    resolver: zodResolver(resetPasswordSchema),
  });

  const onSubmit = (data: ResetPasswordFormData): void => {
    // Implement reset password logic
    console.log('Reset password with:', data.password);
  };

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <View style={styles.form}>
        <Input
          control={control}
          name="password"
          label="New Password"
          placeholder="Enter new password"
          secureTextEntry
          error={errors.password?.message}
        />
        
        <Input
          control={control}
          name="confirmPassword"
          label="Confirm Password"
          placeholder="Confirm new password"
          secureTextEntry
          error={errors.confirmPassword?.message}
        />
        
        <Button
          variant="primary"
          onPress={handleSubmit(onSubmit)}
          style={styles.submitButton}
        >
          Reset Password
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
  submitButton: {
    marginTop: 20,
  },
});
