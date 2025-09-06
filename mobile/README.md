# Telemain Mobile

React Native mobile application for Telemain project management system.

## Architecture

This mobile app follows Clean Architecture principles with clear separation of concerns:

- **App Layer**: Bootstrap, providers, and navigation
- **Features**: Domain-specific features with their own components, hooks, and screens
- **Shared**: Reusable UI components, utilities, and services
- **API**: HTTP client, services, and data schemas
- **Domain**: Business logic and entities

## Tech Stack

- **React Native** 0.74+ with Hermes
- **TypeScript** with strict mode
- **React Navigation** for navigation
- **React Query** for server state management
- **React Hook Form** + **Zod** for forms and validation
- **React i18next** for internationalization
- **Jest** + **React Native Testing Library** for testing

## Getting Started

### Prerequisites

- Node.js 18+
- React Native CLI
- Android Studio (for Android development)
- Xcode (for iOS development)

### Installation

1. Install dependencies:
```bash
npm install
```

2. Install iOS dependencies (iOS only):
```bash
cd ios && pod install && cd ..
```

3. Copy environment variables:
```bash
cp env.example .env
```

4. Start Metro bundler:
```bash
npm start
```

5. Run on device/simulator:
```bash
# Android
npm run android

# iOS
npm run ios
```

## Development

### Code Style

- Follow ESLint and Prettier configurations
- Use TypeScript strict mode
- Follow React Native best practices
- Write tests for components and hooks

### Project Structure

```
src/
├── app/                    # App bootstrap and providers
├── features/              # Feature modules
│   ├── auth/             # Authentication
│   ├── tasks/            # Task management
│   ├── users/            # User management
│   └── profile/          # User profile
├── shared/               # Shared components and utilities
│   ├── ui/               # Reusable UI components
│   ├── hooks/            # Custom hooks
│   ├── lib/              # Services and utilities
│   └── theme/            # Theme and styling
├── api/                  # API layer
│   ├── schemas/          # Data schemas (Zod)
│   └── services/         # API services
├── navigation/           # Navigation configuration
└── testing/             # Test utilities and mocks
```

### Testing

Run tests:
```bash
npm test
```

Run tests with coverage:
```bash
npm run test:coverage
```

### Building

Build for production:
```bash
# Android
npm run build:android

# iOS
npm run build:ios
```

## Features

- **Authentication**: OIDC PKCE flow with secure token storage
- **Offline Support**: React Query persistence with AsyncStorage
- **Internationalization**: Multi-language support (EN, PL, ES)
- **Theme Support**: Light/dark mode with system preference
- **Error Handling**: Global error boundary and error states
- **Loading States**: Consistent loading and empty states
- **Accessibility**: Screen reader support and proper focus management

## Security

- Secure token storage using Keychain/Keystore
- SSL pinning for API requests
- No sensitive data in bundle
- Proper error handling without exposing internals

## Contributing

1. Follow the established code style
2. Write tests for new features
3. Update documentation as needed
4. Ensure all tests pass before submitting PR

## License

See LICENSE file for details.
