import React from 'react';
import { View, StyleSheet, ScrollView, Text, TouchableOpacity } from 'react-native';
import { useTranslation } from 'react-i18next';
import { useTheme } from '@/shared/theme/useTheme';
import { Button } from '@/shared/ui/Button';
import { useAuth } from '@/features/auth/hooks/useAuth';

export function ProfileScreen(): React.JSX.Element {
  const { t } = useTranslation();
  const { colors } = useTheme();
  const { logout, isLoggingOut } = useAuth();

  const handleLogout = (): void => {
    logout();
  };

  return (
    <ScrollView style={[styles.container, { backgroundColor: colors.background }]}>
      <View style={styles.content}>
        <View style={styles.profileSection}>
          <View style={[styles.avatar, { backgroundColor: colors.primary }]}>
            <Text style={[styles.avatarText, { color: colors.background }]}>
              JD
            </Text>
          </View>
          
          <Text style={[styles.name, { color: colors.text }]}>
            John Doe
          </Text>
          
          <Text style={[styles.email, { color: colors.textSecondary }]}>
            john.doe@example.com
          </Text>
          
          <Text style={[styles.role, { color: colors.textSecondary }]}>
            Technician
          </Text>
        </View>
        
        <View style={styles.settingsSection}>
          <TouchableOpacity style={[styles.settingItem, { borderBottomColor: colors.border }]}>
            <Text style={[styles.settingLabel, { color: colors.text }]}>
              Edit Profile
            </Text>
            <Text style={[styles.settingArrow, { color: colors.textSecondary }]}>
              ›
            </Text>
          </TouchableOpacity>
          
          <TouchableOpacity style={[styles.settingItem, { borderBottomColor: colors.border }]}>
            <Text style={[styles.settingLabel, { color: colors.text }]}>
              Notifications
            </Text>
            <Text style={[styles.settingArrow, { color: colors.textSecondary }]}>
              ›
            </Text>
          </TouchableOpacity>
          
          <TouchableOpacity style={[styles.settingItem, { borderBottomColor: colors.border }]}>
            <Text style={[styles.settingLabel, { color: colors.text }]}>
              Privacy
            </Text>
            <Text style={[styles.settingArrow, { color: colors.textSecondary }]}>
              ›
            </Text>
          </TouchableOpacity>
        </View>
        
        <View style={styles.logoutSection}>
          <Button
            variant="danger"
            onPress={handleLogout}
            loading={isLoggingOut}
            style={styles.logoutButton}
          >
            {t('auth.logout')}
          </Button>
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
  profileSection: {
    alignItems: 'center',
    marginBottom: 32,
  },
  avatar: {
    width: 80,
    height: 80,
    borderRadius: 40,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  avatarText: {
    fontSize: 32,
    fontWeight: 'bold',
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
  role: {
    fontSize: 14,
  },
  settingsSection: {
    marginBottom: 32,
  },
  settingItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 16,
    borderBottomWidth: 1,
  },
  settingLabel: {
    fontSize: 16,
  },
  settingArrow: {
    fontSize: 20,
  },
  logoutSection: {
    marginTop: 16,
  },
  logoutButton: {
    marginTop: 8,
  },
});
