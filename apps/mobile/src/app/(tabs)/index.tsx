import { View, Text, ScrollView, TouchableOpacity, StyleSheet } from 'react-native';
import { signOut, getUser } from '@/shared/lib/auth';
import { useRouter } from 'expo-router';
import { useQuery } from '@tanstack/react-query';
import { AuthUser } from '@/shared/lib/auth';

export default function HomeScreen() {
  const router = useRouter();

  const { data: user, isLoading } = useQuery({
    queryKey: ['user', 'profile'],
    queryFn: async (): Promise<AuthUser | null> => {
      return await getUser();
    },
  });

  const handleSignOut = async () => {
    try {
      await signOut();
      router.replace('/auth');
    } catch (error) {
      console.error('Sign out failed:', error);
    }
  };

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <View style={styles.spinner} />
        <Text style={styles.loadingText}>Loading...</Text>
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <View style={styles.headerContent}>
          <View style={styles.headerLeft}>
            <Text style={styles.headerTitle}>Skytech</Text>
          </View>
          <View style={styles.headerRight}>
            <Text style={styles.welcomeText}>
              Welcome, {user?.firstName} {user?.lastName}
            </Text>
            <TouchableOpacity onPress={handleSignOut} style={styles.signOutButton}>
              <Text style={styles.signOutText}>Sign Out</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>

      {/* Main Content */}
      <View style={styles.mainContent}>
        <View style={styles.contentContainer}>
          <View style={styles.cardsGrid}>
            {/* Tasks Card */}
            <View style={styles.card}>
              <View style={styles.cardContent}>
                <View style={styles.cardIconContainer}>
                  <View style={[styles.cardIcon, { backgroundColor: '#3b82f6' }]}>
                    <Text style={styles.cardIconText}>T</Text>
                  </View>
                </View>
                <View style={styles.cardTextContainer}>
                  <Text style={styles.cardTitle}>Tasks</Text>
                  <Text style={styles.cardDescription}>Manage your tasks</Text>
                </View>
              </View>
            </View>

            {/* Users Card */}
            <View style={styles.card}>
              <View style={styles.cardContent}>
                <View style={styles.cardIconContainer}>
                  <View style={[styles.cardIcon, { backgroundColor: '#10b981' }]}>
                    <Text style={styles.cardIconText}>U</Text>
                  </View>
                </View>
                <View style={styles.cardTextContainer}>
                  <Text style={styles.cardTitle}>Users</Text>
                  <Text style={styles.cardDescription}>Team members</Text>
                </View>
              </View>
            </View>

            {/* Analytics Card */}
            <View style={styles.card}>
              <View style={styles.cardContent}>
                <View style={styles.cardIconContainer}>
                  <View style={[styles.cardIcon, { backgroundColor: '#f59e0b' }]}>
                    <Text style={styles.cardIconText}>A</Text>
                  </View>
                </View>
                <View style={styles.cardTextContainer}>
                  <Text style={styles.cardTitle}>Analytics</Text>
                  <Text style={styles.cardDescription}>Performance metrics</Text>
                </View>
              </View>
            </View>

            {/* Profile Card */}
            <View style={styles.card}>
              <View style={styles.cardContent}>
                <View style={styles.cardIconContainer}>
                  <View style={[styles.cardIcon, { backgroundColor: '#8b5cf6' }]}>
                    <Text style={styles.cardIconText}>P</Text>
                  </View>
                </View>
                <View style={styles.cardTextContainer}>
                  <Text style={styles.cardTitle}>Profile</Text>
                  <Text style={styles.cardDescription}>Your account</Text>
                </View>
              </View>
            </View>
          </View>

          {/* Welcome Message */}
          <View style={styles.welcomeCard}>
            <View style={styles.welcomeCardContent}>
              <Text style={styles.welcomeCardTitle}>
                Welcome to Skytech Dashboard
              </Text>
              <Text style={styles.welcomeCardDescription}>
                This is your task management dashboard. You can manage tasks, 
                view team members, check analytics, and update your profile.
              </Text>
              <View style={styles.infoBox}>
                <View style={styles.infoBoxContent}>
                  <Text style={styles.infoBoxIcon}>ℹ️</Text>
                  <View style={styles.infoBoxText}>
                    <Text style={styles.infoBoxTitle}>
                      Mobile App Available
                    </Text>
                    <Text style={styles.infoBoxDescription}>
                      Download our mobile app for iOS and Android to access 
                      your tasks on the go.
                    </Text>
                  </View>
                </View>
              </View>
            </View>
          </View>
        </View>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f9fafb', // gray-50
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f9fafb',
  },
  spinner: {
    width: 32,
    height: 32,
    borderWidth: 2,
    borderColor: '#2563eb',
    borderTopColor: 'transparent',
    borderRadius: 16,
  },
  loadingText: {
    marginTop: 16,
    color: '#6b7280',
  },
  header: {
    backgroundColor: 'white',
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 1,
    },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  headerContent: {
    paddingHorizontal: 16,
    paddingVertical: 24,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  headerLeft: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#111827', // text-gray-900
  },
  headerRight: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 16,
  },
  welcomeText: {
    fontSize: 14,
    color: '#374151', // text-gray-700
  },
  signOutButton: {
    backgroundColor: '#dc2626', // bg-red-600
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 6,
  },
  signOutText: {
    color: 'white',
    fontSize: 14,
    fontWeight: '500',
  },
  mainContent: {
    flex: 1,
    paddingHorizontal: 16,
    paddingVertical: 24,
  },
  contentContainer: {
    flex: 1,
  },
  cardsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 24,
    marginBottom: 32,
  },
  card: {
    backgroundColor: 'white',
    borderRadius: 8,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 1,
    },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
    flex: 1,
    minWidth: '45%',
  },
  cardContent: {
    padding: 20,
    flexDirection: 'row',
    alignItems: 'center',
  },
  cardIconContainer: {
    marginRight: 20,
  },
  cardIcon: {
    width: 32,
    height: 32,
    borderRadius: 6,
    justifyContent: 'center',
    alignItems: 'center',
  },
  cardIconText: {
    color: 'white',
    fontSize: 14,
    fontWeight: '500',
  },
  cardTextContainer: {
    flex: 1,
  },
  cardTitle: {
    fontSize: 14,
    fontWeight: '500',
    color: '#6b7280', // text-gray-500
    marginBottom: 4,
  },
  cardDescription: {
    fontSize: 18,
    fontWeight: '500',
    color: '#111827', // text-gray-900
  },
  welcomeCard: {
    backgroundColor: 'white',
    borderRadius: 8,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 1,
    },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  welcomeCardContent: {
    padding: 24,
  },
  welcomeCardTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#111827', // text-gray-900
    marginBottom: 8,
  },
  welcomeCardDescription: {
    fontSize: 14,
    color: '#6b7280', // text-gray-500
    marginBottom: 20,
  },
  infoBox: {
    backgroundColor: '#dbeafe', // bg-blue-50
    borderRadius: 6,
    padding: 16,
  },
  infoBoxContent: {
    flexDirection: 'row',
  },
  infoBoxIcon: {
    fontSize: 20,
    marginRight: 12,
  },
  infoBoxText: {
    flex: 1,
  },
  infoBoxTitle: {
    fontSize: 14,
    fontWeight: '500',
    color: '#1e40af', // text-blue-800
    marginBottom: 4,
  },
  infoBoxDescription: {
    fontSize: 14,
    color: '#1d4ed8', // text-blue-700
  },
});
