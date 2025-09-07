import { View, Text, FlatList } from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { httpClient } from '@/shared/lib/httpClient';
import { TasksListResponse } from '@skytech/api-sdk/schemas';

export default function TasksScreen() {
  const { data, isLoading, isError } = useQuery({
    queryKey: ['tasks', 'list'],
    queryFn: async () => {
      const response = await httpClient.get<TasksListResponse>('/api/tasks');
      return response;
    },
  });

  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>Loading tasks...</Text>
      </View>
    );
  }

  if (isError) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>Failed to load tasks</Text>
      </View>
    );
  }

  if (!data?.data?.length) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>No tasks found</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1, backgroundColor: '#fff' }}>
      <FlatList
        data={data.data}
        keyExtractor={(item) => item.id}
        renderItem={({ item }) => (
          <View style={{ padding: 16, borderBottomWidth: 1, borderBottomColor: '#e5e5e5' }}>
            <Text style={{ fontSize: 16, fontWeight: 'bold' }}>
              {item.attributes.title}
            </Text>
            <Text style={{ fontSize: 14, color: '#666', marginTop: 4 }}>
              {item.attributes.description}
            </Text>
            <Text style={{ fontSize: 12, color: '#999', marginTop: 8 }}>
              Status: {item.attributes.status} | Priority: {item.attributes.priority}
            </Text>
          </View>
        )}
      />
    </View>
  );
}
