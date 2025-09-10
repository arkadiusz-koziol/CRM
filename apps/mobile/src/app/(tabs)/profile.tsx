import { View, Text } from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { getUser } from '@/shared/lib/auth';
import { AuthUser } from '@/shared/lib/auth';

export default function ProfileScreen() {
  const { data: user, isLoading, isError } = useQuery({
    queryKey: ['user', 'profile'],
    queryFn: async (): Promise<AuthUser | null> => {
      return await getUser();
    },
  });

  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>Loading profile...</Text>
      </View>
    );
  }

  if (isError || !user) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>Failed to load profile</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1, backgroundColor: '#fff', padding: 20 }}>
      <Text style={{ fontSize: 24, fontWeight: 'bold', marginBottom: 20 }}>
        Profile
      </Text>
      
      <View style={{ marginBottom: 16 }}>
        <Text style={{ fontSize: 16, fontWeight: 'bold', marginBottom: 4 }}>
          Name
        </Text>
        <Text style={{ fontSize: 14, color: '#666' }}>
          {user.firstName} {user.lastName}
        </Text>
      </View>
      
      <View style={{ marginBottom: 16 }}>
        <Text style={{ fontSize: 16, fontWeight: 'bold', marginBottom: 4 }}>
          Email
        </Text>
        <Text style={{ fontSize: 14, color: '#666' }}>
          {user.email}
        </Text>
      </View>
      
      <View style={{ marginBottom: 16 }}>
        <Text style={{ fontSize: 16, fontWeight: 'bold', marginBottom: 4 }}>
          Role
        </Text>
        <Text style={{ fontSize: 14, color: '#666' }}>
          {user.role}
        </Text>
      </View>
    </View>
  );
}
