import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { useTheme } from '@/shared/theme/useTheme';
import { User } from '@/api/schemas/user';

interface UserCardProps {
  user: User;
  onPress?: () => void;
}

export function UserCard({ user, onPress }: UserCardProps): React.JSX.Element {
  const { colors } = useTheme();

  const getStatusColor = (status: string): string => {
    switch (status) {
      case 'active':
        return colors.success;
      case 'inactive':
        return colors.warning;
      case 'suspended':
        return colors.error;
      default:
        return colors.textSecondary;
    }
  };

  const getRoleColor = (role: string): string => {
    switch (role) {
      case 'admin':
        return colors.error;
      case 'manager':
        return colors.warning;
      case 'technician':
        return colors.info;
      case 'viewer':
        return colors.textSecondary;
      default:
        return colors.textSecondary;
    }
  };

  return (
    <TouchableOpacity
      style={[styles.container, { backgroundColor: colors.card, borderColor: colors.border }]}
      onPress={onPress}
      activeOpacity={0.7}
    >
      <View style={styles.header}>
        <View style={styles.nameContainer}>
          <Text style={[styles.name, { color: colors.text }]}>
            {user.firstName} {user.lastName}
          </Text>
          <Text style={[styles.email, { color: colors.textSecondary }]}>
            {user.email}
          </Text>
        </View>
        
        <View
          style={[
            styles.statusBadge,
            { backgroundColor: getStatusColor(user.status) },
          ]}
        >
          <Text style={[styles.statusText, { color: colors.background }]}>
            {user.status.toUpperCase()}
          </Text>
        </View>
      </View>
      
      <View style={styles.footer}>
        <View
          style={[
            styles.roleBadge,
            { backgroundColor: getRoleColor(user.role) },
          ]}
        >
          <Text style={[styles.roleText, { color: colors.background }]}>
            {user.role.toUpperCase()}
          </Text>
        </View>
        
        {user.phone && (
          <Text style={[styles.phone, { color: colors.textSecondary }]}>
            {user.phone}
          </Text>
        )}
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  container: {
    padding: 16,
    marginBottom: 12,
    borderRadius: 8,
    borderWidth: 1,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  nameContainer: {
    flex: 1,
    marginRight: 8,
  },
  name: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 4,
  },
  email: {
    fontSize: 14,
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 4,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '600',
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  roleBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 4,
  },
  roleText: {
    fontSize: 12,
    fontWeight: '600',
  },
  phone: {
    fontSize: 12,
  },
});
