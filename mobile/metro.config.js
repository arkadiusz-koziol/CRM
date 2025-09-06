const { getDefaultConfig, mergeConfig } = require('@react-native/metro-config');

/**
 * Metro configuration
 * https://facebook.github.io/metro/docs/configuration
 *
 * @type {import('metro-config').MetroConfig}
 */
const config = {
  resolver: {
    alias: {
      '@': './src',
      '@/app': './src/app',
      '@/config': './src/config',
      '@/navigation': './src/navigation',
      '@/api': './src/api',
      '@/domain': './src/domain',
      '@/features': './src/features',
      '@/shared': './src/shared',
      '@/entities': './src/entities',
      '@/testing': './src/testing',
    },
  },
};

module.exports = mergeConfig(getDefaultConfig(__dirname), config);
