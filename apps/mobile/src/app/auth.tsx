import { useState } from 'react';
import { 
  View, 
  Text, 
  TextInput, 
  Alert, 
  TouchableOpacity, 
  ActivityIndicator, 
  StyleSheet,
  KeyboardAvoidingView,
  Platform,
  ScrollView
} from 'react-native';
import { signInWithPKCE, signInWithCredentials } from '@/shared/lib/auth';
import { useRouter } from 'expo-router';

export default function AuthScreen() {
  const [isLoading, setIsLoading] = useState(false);
  const [isOAuthLoading, setIsOAuthLoading] = useState(false);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const router = useRouter();

  const handleEmailSignIn = async () => {
    if (!email.trim() || !password.trim()) {
      Alert.alert('Error', 'Please enter both email and password');
      return;
    }

    try {
      setIsLoading(true);
      await signInWithCredentials(email.trim(), password);
      router.replace('/(tabs)');
    } catch (error) {
      console.error('Email sign in failed:', error);
      Alert.alert('Error', 'Invalid email or password. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  const handleOAuthSignIn = async () => {
    try {
      setIsOAuthLoading(true);
      await signInWithPKCE();
      router.replace('/(tabs)');
    } catch (error) {
      console.error('OAuth sign in failed:', error);
      Alert.alert('Error', 'Failed to sign in with OAuth. Please try again.');
    } finally {
      setIsOAuthLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView 
      style={styles.container} 
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <ScrollView contentContainerStyle={styles.scrollContainer}>
        <View style={styles.content}>
          <Text style={styles.title}>Welcome to Skytech</Text>
          <Text style={styles.subtitle}>Sign in to your account</Text>

          {/* Email/Password Form */}
          <View style={styles.form}>
            <View style={styles.inputContainer}>
              <Text style={styles.label}>Email</Text>
              <TextInput
                style={[styles.input, styles.globalText]}
                value={email}
                onChangeText={setEmail}
                placeholder="Enter your email"
                placeholderTextColor="#999"
                keyboardType="email-address"
                autoCapitalize="none"
                autoCorrect={false}
              />
            </View>

            <View style={styles.inputContainer}>
              <Text style={styles.label}>Password</Text>
              <View style={styles.passwordContainer}>
                <TextInput
                  style={[styles.passwordInput, styles.globalText]}
                  value={password}
                  onChangeText={setPassword}
                  placeholder="Enter your password"
                  placeholderTextColor="#999"
                  secureTextEntry={!showPassword}
                  autoCapitalize="none"
                  autoCorrect={false}
                />
                <TouchableOpacity
                  style={styles.eyeButton}
                  onPress={() => setShowPassword(!showPassword)}
                >
                  <Text style={styles.eyeText}>{showPassword ? '👁️' : '👁️‍🗨️'}</Text>
                </TouchableOpacity>
              </View>
            </View>

            <TouchableOpacity
              onPress={handleEmailSignIn}
              disabled={isLoading}
              style={[styles.primaryButton, isLoading && styles.disabledButton]}
            >
              {isLoading ? (
                <ActivityIndicator color="white" />
              ) : (
                <Text style={styles.primaryButtonText}>Sign In</Text>
              )}
            </TouchableOpacity>
          </View>

          {/* Divider */}
          <View style={styles.divider}>
            <View style={styles.dividerLine} />
            <Text style={styles.dividerText}>OR</Text>
            <View style={styles.dividerLine} />
          </View>

          {/* OAuth Sign In */}
          <TouchableOpacity
            onPress={handleOAuthSignIn}
            disabled={isOAuthLoading}
            style={[styles.oauthButton, isOAuthLoading && styles.disabledButton]}
          >
            {isOAuthLoading ? (
              <ActivityIndicator color="#007AFF" />
            ) : (
              <Text style={styles.oauthButtonText}>Sign in with OAuth</Text>
            )}
          </TouchableOpacity>

          {/* Forgot Password */}
          <TouchableOpacity style={styles.forgotPassword}>
            <Text style={styles.forgotPasswordText}>Forgot Password?</Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f9fafb', // gray-50
  },
  // Global text color override for web environment
  globalText: {
    color: '#111827', // text-gray-900
  },
  scrollContainer: {
    flexGrow: 1,
    justifyContent: 'center',
    paddingVertical: 48, // py-12
    paddingHorizontal: 24, // sm:px-6
  },
  content: {
    paddingHorizontal: 20,
    alignItems: 'center',
  },
  title: {
    fontSize: 30, // text-3xl
    fontWeight: 'bold',
    color: '#111827', // text-gray-900
    marginBottom: 8,
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 14, // text-sm
    color: '#6b7280', // text-gray-600
    marginBottom: 32, // mt-2
    textAlign: 'center',
  },
  form: {
    width: '100%',
    maxWidth: 384, // sm:max-w-md
    backgroundColor: 'white',
    paddingVertical: 32, // py-8
    paddingHorizontal: 16, // px-4
    borderRadius: 8, // sm:rounded-lg
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 1,
    },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  inputContainer: {
    marginBottom: 24, // space-y-6
  },
  label: {
    fontSize: 14, // text-sm
    fontWeight: '500', // font-medium
    color: '#374151', // text-gray-700
    marginBottom: 8, // mt-1
  },
  input: {
    backgroundColor: 'white',
    borderWidth: 1,
    borderColor: '#d1d5db', // border-gray-300
    borderRadius: 6, // rounded-md
    paddingHorizontal: 12, // px-3
    paddingVertical: 8, // py-2
    fontSize: 14, // sm:text-sm
    color: '#111827', // text-gray-900
    textAlign: 'left', // Ensure text alignment
  },
  passwordContainer: {
    position: 'relative',
  },
  passwordInput: {
    backgroundColor: 'white',
    borderWidth: 1,
    borderColor: '#d1d5db', // border-gray-300
    borderRadius: 6, // rounded-md
    paddingHorizontal: 12, // px-3
    paddingVertical: 8, // py-2
    paddingRight: 40, // pr-10
    fontSize: 14, // sm:text-sm
    color: '#111827', // text-gray-900
    textAlign: 'left', // Ensure text alignment
  },
  eyeButton: {
    position: 'absolute',
    right: 12, // right-0 pr-3
    top: 8, // inset-y-0
    padding: 4,
  },
  eyeText: {
    fontSize: 16,
    color: '#9ca3af', // text-gray-400
  },
  primaryButton: {
    backgroundColor: '#2563eb', // bg-blue-600
    paddingVertical: 8, // py-2
    paddingHorizontal: 16, // px-4
    borderRadius: 6, // rounded-md
    alignItems: 'center',
    marginTop: 24, // space-y-6
  },
  primaryButtonText: {
    color: 'white',
    fontSize: 14, // text-sm
    fontWeight: '500', // font-medium
  },
  oauthButton: {
    backgroundColor: 'white',
    borderWidth: 1,
    borderColor: '#2563eb', // border-blue-600
    paddingVertical: 8, // py-2
    paddingHorizontal: 16, // px-4
    borderRadius: 6, // rounded-md
    alignItems: 'center',
    marginTop: 24, // mt-6
  },
  oauthButtonText: {
    color: '#2563eb', // text-blue-600
    fontSize: 14, // text-sm
    fontWeight: '500', // font-medium
  },
  disabledButton: {
    opacity: 0.5, // disabled:opacity-50
  },
  divider: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: 24, // mt-6
    width: '100%',
  },
  dividerLine: {
    flex: 1,
    height: 1,
    backgroundColor: '#d1d5db', // border-gray-300
  },
  dividerText: {
    marginHorizontal: 8, // px-2
    color: '#6b7280', // text-gray-500
    fontSize: 14, // text-sm
    backgroundColor: 'white',
    paddingHorizontal: 8,
  },
  forgotPassword: {
    marginTop: 24, // mt-6
    alignItems: 'center',
  },
  forgotPasswordText: {
    color: '#2563eb', // text-blue-600
    fontSize: 14, // text-sm
  },
});
