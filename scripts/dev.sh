#!/bin/bash

# Skytech Development Environment Script
# This script manages the development environment with Docker Compose

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if Docker is running
check_docker() {
    if ! docker info > /dev/null 2>&1; then
        print_error "Docker is not running. Please start Docker Desktop and try again."
        exit 1
    fi
}

# Function to check if pnpm is installed
check_pnpm() {
    if ! command -v pnpm &> /dev/null; then
        print_error "pnpm is not installed. Please install pnpm first:"
        echo "npm install -g pnpm@9.0.0"
        exit 1
    fi
}

# Function to start development environment
start_dev() {
    print_status "Starting Skytech development environment..."
    
    check_docker
    check_pnpm
    
    # Create .env.dev if it doesn't exist
    if [ ! -f .env.dev ]; then
        print_status "Creating .env.dev file..."
        cat > .env.dev << EOF
# Development Environment Configuration

# Database
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=skytech-db
DB_USERNAME=user
DB_PASSWORD=password

# Redis
REDIS_HOST=redis
REDIS_PORT=6379
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Application
APP_NAME=Skytech
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8199
APP_KEY=

# API Configuration
API_URL=http://localhost:8199/api/v1
API_TIMEOUT=15000

# Authentication
AUTH_CLIENT_ID=skytech-dev-client
AUTH_DISCOVERY_URL=http://localhost:8199/api/v1/auth/oauth/authorize

# CORS
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:8081,http://127.0.0.1:3000,http://127.0.0.1:8081

# Mail (Development)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@skytech.com
MAIL_FROM_NAME="\${APP_NAME}"

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug

# Features
FEATURE_OFFLINE_MODE=false
FEATURE_DEBUG_LOGS=true

# Sentry (Development)
SENTRY_DSN=
SENTRY_ENVIRONMENT=development

# Next.js
NEXT_PUBLIC_API_URL=http://localhost:8199/api/v1
NEXT_PUBLIC_APP_NAME=Skytech
NEXT_PUBLIC_APP_VERSION=1.0.0

# Expo
EXPO_PUBLIC_API_URL=http://localhost:8199/api/v1
EXPO_PUBLIC_APP_NAME=Skytech
EXPO_PUBLIC_APP_VERSION=1.0.0
EOF
        print_success "Created .env.dev file"
    fi
    
    # Install dependencies
    print_status "Installing dependencies..."
    pnpm install
    
    # Start Docker Compose
    print_status "Starting Docker containers..."
    docker-compose -f docker-compose.dev.yml up -d
    
    # Wait for services to be ready
    print_status "Waiting for services to be ready..."
    sleep 10
    
    # Check service health
    print_status "Checking service health..."
    
    # Check PostgreSQL
    if docker-compose -f docker-compose.dev.yml exec -T postgres pg_isready -U user -d skytech-db > /dev/null 2>&1; then
        print_success "PostgreSQL is ready"
    else
        print_warning "PostgreSQL is not ready yet"
    fi
    
    # Check Redis
    if docker-compose -f docker-compose.dev.yml exec -T redis redis-cli ping > /dev/null 2>&1; then
        print_success "Redis is ready"
    else
        print_warning "Redis is not ready yet"
    fi
    
    # Run Laravel migrations
    print_status "Running Laravel migrations..."
    docker-compose -f docker-compose.dev.yml exec -T backend php artisan migrate --force || print_warning "Migrations failed or already run"
    
    # Create test user
    print_status "Creating test user..."
    docker-compose -f docker-compose.dev.yml exec -T backend php artisan tinker --execute="
        \$user = \App\Models\User::firstOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('test'),
                'email_verified_at' => now()
            ]
        );
        echo 'Test user created: ' . \$user->email;
    " || print_warning "Test user creation failed or user already exists"
    
    print_success "Development environment started successfully!"
    echo ""
    echo "🌐 Services available at:"
    echo "  • Web App:     http://localhost:3000"
    echo "  • Mobile App:  http://localhost:8081"
    echo "  • Backend API: http://localhost:8199/api/v1"
    echo "  • Mailhog:     http://localhost:8025"
    echo "  • PgAdmin:     http://localhost:5050"
    echo ""
    echo "🔑 Test credentials:"
    echo "  • Email: test@test.com"
    echo "  • Password: test"
    echo ""
    echo "📱 Mobile development:"
    echo "  • Scan QR code at http://localhost:8081"
    echo "  • Or open http://localhost:8081 in browser"
    echo ""
    echo "🛠️  Useful commands:"
    echo "  • View logs:    ./scripts/dev.sh logs"
    echo "  • Stop:         ./scripts/dev.sh stop"
    echo "  • Restart:      ./scripts/dev.sh restart"
    echo "  • Clean:        ./scripts/dev.sh clean"
}

# Function to stop development environment
stop_dev() {
    print_status "Stopping development environment..."
    docker-compose -f docker-compose.dev.yml down
    print_success "Development environment stopped"
}

# Function to restart development environment
restart_dev() {
    print_status "Restarting development environment..."
    stop_dev
    sleep 2
    start_dev
}

# Function to view logs
view_logs() {
    print_status "Viewing logs..."
    docker-compose -f docker-compose.dev.yml logs -f
}

# Function to clean up
clean_dev() {
    print_status "Cleaning up development environment..."
    docker-compose -f docker-compose.dev.yml down -v
    docker system prune -f
    print_success "Development environment cleaned"
}

# Function to show status
show_status() {
    print_status "Development environment status:"
    docker-compose -f docker-compose.dev.yml ps
}

# Main script logic
case "${1:-start}" in
    start)
        start_dev
        ;;
    stop)
        stop_dev
        ;;
    restart)
        restart_dev
        ;;
    logs)
        view_logs
        ;;
    clean)
        clean_dev
        ;;
    status)
        show_status
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|logs|clean|status}"
        echo ""
        echo "Commands:"
        echo "  start   - Start the development environment (default)"
        echo "  stop    - Stop the development environment"
        echo "  restart - Restart the development environment"
        echo "  logs    - View logs from all services"
        echo "  clean   - Stop and remove all containers and volumes"
        echo "  status  - Show status of all services"
        exit 1
        ;;
esac
