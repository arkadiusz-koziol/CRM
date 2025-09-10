# Skytech Development Environment

This document describes how to set up and run the complete Skytech development environment using Docker Compose.

## 🚀 Quick Start

### Prerequisites

- **Docker Desktop** (latest version)
- **pnpm** (version 9.0.0 or higher)
- **Node.js** (version 20 or higher)

### Installation

1. **Install pnpm** (if not already installed):
   ```bash
   npm install -g pnpm@9.0.0
   ```

2. **Clone the repository** (if not already done):
   ```bash
   git clone <repository-url>
   cd skytech
   ```

3. **Start the development environment**:
   ```bash
   ./scripts/dev.sh start
   ```

That's it! The script will automatically:
- Install all dependencies
- Start all Docker containers
- Run database migrations
- Create a test user
- Set up all services

## 🌐 Services

Once started, the following services will be available:

| Service | URL | Description |
|---------|-----|-------------|
| **Web App** | http://localhost:3000 | Next.js web application |
| **Mobile App** | http://localhost:8081 | Expo development server |
| **Backend API** | http://localhost:8199/api/v1 | Laravel API backend |
| **Mailhog** | http://localhost:8025 | Email testing interface |
| **PgAdmin** | http://localhost:5050 | Database administration |

## 🔑 Test Credentials

- **Email**: `test@test.com`
- **Password**: `test`

## 📱 Mobile Development

### Web Browser
- Open http://localhost:8081 in your browser
- The Expo development tools will load

### Physical Device
1. Install **Expo Go** app on your device
2. Scan the QR code displayed at http://localhost:8081
3. The app will load on your device

### iOS Simulator
```bash
# Start iOS simulator (requires Xcode)
./scripts/dev.sh logs mobile
# Then press 'i' in the terminal
```

### Android Emulator
```bash
# Start Android emulator (requires Android Studio)
./scripts/dev.sh logs mobile
# Then press 'a' in the terminal
```

## 🛠️ Development Commands

### Script Commands

```bash
# Start all services
./scripts/dev.sh start

# Stop all services
./scripts/dev.sh stop

# Restart all services
./scripts/dev.sh restart

# View logs from all services
./scripts/dev.sh logs

# View logs from specific service
docker-compose -f docker-compose.dev.yml logs -f web
docker-compose -f docker-compose.dev.yml logs -f mobile
docker-compose -f docker-compose.dev.yml logs -f backend

# Show status of all services
./scripts/dev.sh status

# Clean up everything (removes all containers and volumes)
./scripts/dev.sh clean
```

### Manual Commands

```bash
# Install dependencies
pnpm install

# Run web development server
pnpm dev:web

# Run mobile development server
pnpm dev:mobile

# Run both web and mobile
pnpm dev

# Build all packages
pnpm build

# Run tests
pnpm test

# Run linting
pnpm lint

# Run type checking
pnpm typecheck
```

## 🐳 Docker Services

### Backend (Laravel)
- **Container**: `skytech-backend-dev`
- **Port**: 8000 (internal)
- **Database**: PostgreSQL
- **Cache**: Redis
- **Features**: API, Authentication, Database migrations

### Web (Next.js)
- **Container**: `skytech-web-dev`
- **Port**: 3000
- **Features**: React, TypeScript, Tailwind CSS, Hot reload

### Mobile (Expo)
- **Container**: `skytech-mobile-dev`
- **Port**: 8081
- **Features**: React Native, Expo Router, Hot reload

### Database (PostgreSQL)
- **Container**: `skytech-postgres-dev`
- **Port**: 5432
- **Database**: `skytech-db`
- **User**: `user`
- **Password**: `password`

### Cache (Redis)
- **Container**: `skytech-redis-dev`
- **Port**: 6379
- **Features**: Session storage, Cache, Queue

### Proxy (Nginx)
- **Container**: `skytech-nginx-dev`
- **Ports**: 8199 (API), 8081 (Mobile)
- **Features**: Load balancing, CORS, Static files

### Mail (Mailhog)
- **Container**: `skytech-mailhog-dev`
- **Ports**: 1025 (SMTP), 8025 (Web UI)
- **Features**: Email testing, Web interface

### Database Admin (PgAdmin)
- **Container**: `skytech-pgadmin-dev`
- **Port**: 5050
- **Email**: `admin@skytech.com`
- **Password**: `admin`

## 🔧 Configuration

### Environment Variables

The development environment uses `.env.dev` file with the following key variables:

```bash
# API Configuration
API_URL=http://localhost:8199/api/v1
NEXT_PUBLIC_API_URL=http://localhost:8199/api/v1
EXPO_PUBLIC_API_URL=http://localhost:8199/api/v1

# Database
DB_HOST=postgres
DB_DATABASE=skytech-db
DB_USERNAME=user
DB_PASSWORD=password

# Redis
REDIS_HOST=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### CORS Configuration

The development environment is configured to allow CORS from:
- http://localhost:3000 (Web app)
- http://localhost:8081 (Mobile app)
- http://127.0.0.1:3000
- http://127.0.0.1:8081

## 🐛 Troubleshooting

### Common Issues

1. **Port already in use**
   ```bash
   # Check what's using the port
   lsof -i :3000
   lsof -i :8081
   lsof -i :8199
   
   # Kill the process
   kill -9 <PID>
   ```

2. **Docker containers not starting**
   ```bash
   # Check Docker status
   docker ps -a
   
   # View container logs
   docker logs skytech-backend-dev
   docker logs skytech-web-dev
   docker logs skytech-mobile-dev
   ```

3. **Database connection issues**
   ```bash
   # Check if PostgreSQL is running
   docker-compose -f docker-compose.dev.yml exec postgres pg_isready -U user -d skytech-db
   
   # Reset database
   ./scripts/dev.sh clean
   ./scripts/dev.sh start
   ```

4. **Dependencies not installing**
   ```bash
   # Clear node_modules and reinstall
   rm -rf node_modules apps/*/node_modules packages/*/node_modules
   pnpm install
   ```

5. **Mobile app not loading**
   ```bash
   # Check Expo logs
   docker-compose -f docker-compose.dev.yml logs -f mobile
   
   # Restart mobile service
   docker-compose -f docker-compose.dev.yml restart mobile
   ```

### Reset Everything

If you encounter persistent issues:

```bash
# Stop and remove everything
./scripts/dev.sh clean

# Remove all Docker images and volumes
docker system prune -a --volumes

# Restart Docker Desktop
# Then run:
./scripts/dev.sh start
```

## 📁 Project Structure

```
skytech/
├── apps/
│   ├── web/                 # Next.js web application
│   └── mobile/              # Expo mobile application
├── packages/
│   ├── api-sdk/             # Shared API client
│   ├── config/              # Shared configuration
│   ├── i18n/                # Internationalization
│   ├── theme/               # Design tokens
│   └── ui/                  # Shared UI components
├── backend/                 # Laravel backend
├── .docker/                 # Docker configurations
├── scripts/
│   └── dev.sh              # Development script
├── docker-compose.dev.yml  # Development Docker Compose
└── DEVELOPMENT.md          # This file
```

## 🤝 Contributing

1. Make sure the development environment is running
2. Make your changes
3. Test both web and mobile applications
4. Run tests: `pnpm test`
5. Run linting: `pnpm lint`
6. Commit your changes

## 📞 Support

If you encounter issues not covered in this document:

1. Check the logs: `./scripts/dev.sh logs`
2. Check the troubleshooting section above
3. Create an issue with:
   - Your operating system
   - Docker version
   - Node.js version
   - pnpm version
   - Error messages from logs
