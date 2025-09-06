import React from 'react';
import { View, StyleSheet, ScrollView, Text } from 'react-native';
import { useRoute, RouteProp } from '@react-navigation/native';
import { useTranslation } from 'react-i18next';
import { useTheme } from '@/shared/theme/useTheme';
import { LoadingSpinner } from '@/shared/components/LoadingSpinner';
import { EmptyState } from '@/shared/components/EmptyState';
import { useTaskQuery } from '../hooks/useTasksQuery';
import { MainNavigator } from '@/navigation/MainNavigator';

type TaskDetailRouteProp = RouteProp<MainNavigator['TasksStackParamList'], 'TaskDetail'>;

export function TaskDetailScreen(): React.JSX.Element {
  const { t } = useTranslation();
  const { colors } = useTheme();
  const route = useRoute<TaskDetailRouteProp>();
  const { taskId } = route.params;

  const { data: task, isLoading, isError, refetch } = useTaskQuery(taskId);

  if (isLoading) {
    return <LoadingSpinner message={t('common.loading')} />;
  }

  if (isError || !task) {
    return (
      <EmptyState
        title="Error"
        message="Failed to load task details. Please try again."
        actionLabel={t('common.retry')}
        onAction={() => refetch()}
      />
    );
  }

  return (
    <ScrollView style={[styles.container, { backgroundColor: colors.background }]}>
      <View style={styles.content}>
        <Text style={[styles.title, { color: colors.text }]}>
          {task.title}
        </Text>
        
        {task.description && (
          <Text style={[styles.description, { color: colors.textSecondary }]}>
            {task.description}
          </Text>
        )}
        
        <View style={styles.details}>
          <View style={styles.detailRow}>
            <Text style={[styles.detailLabel, { color: colors.textSecondary }]}>
              Status:
            </Text>
            <Text style={[styles.detailValue, { color: colors.text }]}>
              {task.status}
            </Text>
          </View>
          
          <View style={styles.detailRow}>
            <Text style={[styles.detailLabel, { color: colors.textSecondary }]}>
              Priority:
            </Text>
            <Text style={[styles.detailValue, { color: colors.text }]}>
              {task.priority}
            </Text>
          </View>
          
          {task.dueDate && (
            <View style={styles.detailRow}>
              <Text style={[styles.detailLabel, { color: colors.textSecondary }]}>
                Due Date:
              </Text>
              <Text style={[styles.detailValue, { color: colors.text }]}>
                {new Date(task.dueDate).toLocaleDateString()}
              </Text>
            </View>
          )}
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
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 8,
  },
  description: {
    fontSize: 16,
    lineHeight: 24,
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
