# SkyTech Frontend

A modern React frontend for the SkyTech field service management system.

## Features

- **Modern UI**: Built with React 19, TypeScript, and Tailwind CSS
- **Responsive Design**: Mobile-first approach with responsive layouts
- **Component Architecture**: Atomic design with shared UI components
- **State Management**: React Query for server state management
- **Routing**: React Router for client-side navigation
- **API Integration**: Axios-based API client with interceptors

## Tech Stack

- **React 19** - UI library
- **TypeScript** - Type safety
- **Vite** - Build tool and dev server
- **Tailwind CSS** - Utility-first CSS framework
- **React Router** - Client-side routing
- **React Query** - Server state management
- **Axios** - HTTP client
- **Lucide React** - Icon library

## Project Structure

```
src/
├── components/          # Page-level components
├── pages/              # Route components
├── shared/             # Shared components and UI
│   ├── components/     # Reusable components
│   └── ui/            # Base UI components
├── hooks/              # Custom React hooks
├── services/           # API services
├── types/              # TypeScript type definitions
└── utils/              # Utility functions
```

## Getting Started

### Prerequisites

- Node.js 18+ 
- npm or yarn

### Installation

1. Install dependencies:
```bash
npm install
```

2. Start the development server:
```bash
npm run dev
```

3. Open [http://localhost:3000](http://localhost:3000) in your browser

### Building for Production

```bash
npm run build
```

The built files will be in the `dist` directory.

## API Integration

The frontend is configured to proxy API requests to the Laravel backend running on `http://localhost:8000`. The API client is set up with:

- Automatic token handling
- Request/response interceptors
- Error handling
- TypeScript support

## Development Guidelines

- Follow the established component architecture
- Use TypeScript for all new code
- Follow the naming conventions (PascalCase for components, camelCase for functions)
- Use Tailwind CSS for styling
- Write reusable components in the `shared` directory
- Keep business logic in services and hooks

## Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build
- `npm run lint` - Run ESLint