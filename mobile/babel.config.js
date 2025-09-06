module.exports = {
  presets: ['module:metro-react-native-babel-preset'],
  plugins: [
    'react-native-reanimated/plugin',
    [
      'module-resolver',
      {
        root: ['./src'],
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
    ],
  ],
};
