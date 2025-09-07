const { getDefaultConfig } = require('expo/metro-config');

const config = getDefaultConfig(__dirname);

// Add support for workspace packages
config.watchFolders = [
  // Watch the monorepo root
  require('path').resolve(__dirname, '../..'),
];

// Resolve workspace packages
config.resolver.nodeModulesPaths = [
  require('path').resolve(__dirname, 'node_modules'),
  require('path').resolve(__dirname, '../../node_modules'),
];

module.exports = config;
