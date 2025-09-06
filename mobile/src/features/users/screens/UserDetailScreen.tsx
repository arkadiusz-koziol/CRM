import React from 'react';
import { View, StyleSheet, ScrollView, Text } from 'react-native';
import { useRoute, RouteProp } from '@react-navigation/native';
import { useTranslation } from 'react-i18next';
import { useTheme } from '@/shared/theme/useTheme';
import { LoadingSpinner } from '@/shared/components/LoadingSpinner';
import { EmptyState } from '@/shared/components/EmptyState';
import { useUserQuery } from '../hooks/useUsersQuery';
import { MainNavigator } from '@/navigation/MainNavigator';

type UserDetailRouteProp = RouteProp<MainNavigator['UsersStackParamList'], 'UserDetail'>;

export function UserDetailScreen(): React.JSX.Element {
  const { t } = useTranslation();
  const { colors } = useTheme();
  const route = useRoute<UserDetailRouteProp>();
  const { userId } = route.params;

  const { data: user, isLoading, isError, refetch } = useUserQuery(userId);

  if (isLoading) {
    return <LoadingSpinner message={t('common.loading')} />;
  }

  if (isError || !user) {
    return (
      <EmptyState
        title="Error"
        message="Failed to load user details. Please try again."
        actionLabel={t('common.retry')}
        onAction={() => refetch()}
      />
    );
  }

  return (
    <ScrollView style={[styles.container, { backgroundColor: colors.background }]}>
      <View style={styles.content}>
        <Text style={[styles.name, { color: colors.text }]}>
          {user.firstName} {user.lastName}
        </Text>
        
        <Text style={[styles.email, { color: colors.textSecondary }]}>
          {user.email}
        </Text>
        
        {user.phone && (
          <Text style={[styles.phone, { color: colors.textSecondary }]}>
            {user.phone}
          </Text>
        )}
        
        <View style={styles.details}>
          <View style={styles.detailRow}>
            <Text style={[styles.detailLabel, { color: colors.textSecondary }]}>
              Role:
            </Text>
            <Text style={[styles.detailValue, { color: colors.text }]}>
              {user.role}
            </Text>
          </View>
          
          <View style={styles.detailRow}>
            <Text style={[styles.detailLabel, { color: colors.textSecondary }]}>
              Status:
            </Text>
            <Text style={[styles.detailValue, { color: colors.text }]}>
              {user.status}
            </Text>
          </View>
          
          <View style={styles.detailRow}>
            <Text style={[styles.detailLabel, { color: colors.textSecondary }]}>
              Created:
            </Text>
            <Text style={[styles.detailValue, { color: colors.text }]}>
              {new Date(user.createdAt).toLocaleDateString()}
            </Text>
          </View>
        </View>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  content: {
    padding: 16,
  },
  name: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  email: {
    fontSize: 16,
    marginBottom: 4,
  },
  phone: {
    fontSize: 16,
    marginBottom: 24,
  },
  details: {
    gap: 12,
  },
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  detailLabel: {
    fontSize: 16,
    fontWeight: '500',
  },
  detailValue: {
    fontSize: 16,
  },
});
