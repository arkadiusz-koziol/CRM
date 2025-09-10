import { View, Text, FlatList } from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { httpClient } from '@/shared/lib/httpClient';
import { UsersListResponse } from '@skytech/api-sdk/schemas';

export default function UsersScreen() {
  const { data, isLoading, isError } = useQuery({
    queryKey: ['users', 'list'],
    queryFn: async () => {
      const response = await httpClient.get<UsersListResponse>('/api/users');
      return response;
    },
  });

  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>Loading users...</Text>
      </View>
    );
  }

  if (isError) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>Failed to load users</Text>
      </View>
    );
  }

  if (!data?.data?.length) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>No users found</Text>
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
              {item.attributes.firstName} {item.attributes.lastName}
            </Text>
            <Text style={{ fontSize: 14, color: '#666', marginTop: 4 }}>
              {item.attributes.email}
            </Text>
            <Text style={{ fontSize: 12, color: '#999', marginTop: 8 }}>
              Role: {item.attributes.role} | Status: {item.attributes.status}
            </Text>
          </View>
        )}
      />
    </View>
  );
}
