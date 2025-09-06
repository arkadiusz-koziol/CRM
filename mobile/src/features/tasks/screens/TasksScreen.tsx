import React, { useState } from 'react';
import { View, StyleSheet, FlatList, RefreshControl } from 'react-native';
import { useTranslation } from 'react-i18next';
import { useTheme } from '@/shared/theme/useTheme';
import { LoadingSpinner } from '@/shared/components/LoadingSpinner';
import { EmptyState } from '@/shared/components/EmptyState';
import { TaskCard } from '../components/TaskCard';
import { useTasksQuery } from '../hooks/useTasksQuery';

export function TasksScreen(): React.JSX.Element {
  const { t } = useTranslation();
  const { colors } = useTheme();
  const [refreshing, setRefreshing] = useState(false);

  const { data, isLoading, isError, refetch } = useTasksQuery();

  const handleRefresh = async (): Promise<void> => {
    setRefreshing(true);
    await refetch();
    setRefreshing(false);
  };

  if (isLoading) {
    return <LoadingSpinner message={t('common.loading')} />;
  }

  if (isError) {
    return (
      <EmptyState
        title="Error"
        message="Failed to load tasks. Please try again."
        actionLabel={t('common.retry')}
        onAction={() => refetch()}
      />
    );
  }

  if (!data?.tasks || data.tasks.length === 0) {
    return (
      <EmptyState
        title="No Tasks"
        message="You don't have any tasks yet."
        actionLabel="Create Task"
        onAction={() => {
          // Navigate to create task screen
        }}
      />
    );
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <FlatList
        data={data.tasks}
        keyExtractor={(item) => item.id}
        renderItem={({ item }) => <TaskCard task={item} />}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={handleRefresh}
            colors={[colors.primary]}
            tintColor={colors.primary}
          />
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  listContent: {
    padding: 16,
  },
});
