module.exports = {
  extends: [
    './react.js',
    'plugin:boundaries/recommended',
  ],
  plugins: ['boundaries'],
  rules: {
    'boundaries/element-types': [
      'error',
      {
        default: 'disallow',
        rules: [
          {
            from: 'features',
            allow: ['shared', 'entities', 'lib'],
          },
          {
            from: 'shared',
            allow: ['lib'],
          },
          {
            from: 'entities',
            allow: ['lib'],
          },
        ],
      },
    ],
    'boundaries/no-unknown-files': [
      'error',
      {
        mode: 'strict',
        allow: ['**/*.d.ts', '**/*.config.*', '**/*.test.*', '**/*.spec.*'],
      },
    ],
  },
};
