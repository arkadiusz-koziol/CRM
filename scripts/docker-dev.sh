#!/bin/bash

# Docker Development Script for CRM
# This script provides common development tasks for the Docker environment

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

# Function to show help
show_help() {
    echo "Docker Development Script for CRM"
    echo ""
    echo "Usage: $0 [COMMAND]"
    echo ""
    echo "Commands:"
    echo "  start       Start the development environment"
    echo "  stop        Stop the development environment"
    echo "  restart     Restart the development environment"
    echo "  build       Build the Docker images"
    echo "  logs        Show logs from all services"
    echo "  shell       Open shell in the app container"
    echo "  artisan     Run Laravel artisan commands"
    echo "  composer    Run composer commands"
    echo "  test        Run PHPUnit tests"
    echo "  migrate     Run database migrations"
    echo "  seed        Run database seeders"
    echo "  fresh       Fresh database with seeders"
    echo "  clean       Clean up Docker resources"
    echo "  status      Show status of all services"
    echo "  help        Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 start"
    echo "  $0 artisan migrate"
    echo "  $0 composer install"
    echo "  $0 test"
}

# Function to start the development environment
start_dev() {
    print_status "Starting development environment..."
    docker-compose -f docker-compose.dev.yml up -d
    print_success "Development environment started!"
    print_status "Application available at: http://localhost:8199"
    print_status "phpMyAdmin available at: http://localhost:8080"
}

# Function to stop the development environment
stop_dev() {
    print_status "Stopping development environment..."
    docker-compose -f docker-compose.dev.yml down
    print_success "Development environment stopped!"
}

# Function to restart the development environment
restart_dev() {
    print_status "Restarting development environment..."
    docker-compose -f docker-compose.dev.yml restart
    print_success "Development environment restarted!"
}

# Function to build Docker images
build_images() {
    print_status "Building Docker images..."
    docker-compose -f docker-compose.dev.yml build --no-cache
    print_success "Docker images built!"
}

# Function to show logs
show_logs() {
    docker-compose -f docker-compose.dev.yml logs -f
}

# Function to open shell in app container
open_shell() {
    print_status "Opening shell in app container..."
    docker-compose -f docker-compose.dev.yml exec app bash
}

# Function to run artisan commands
run_artisan() {
    shift # Remove 'artisan' from arguments
    print_status "Running artisan command: $@"
    docker-compose -f docker-compose.dev.yml exec app php artisan "$@"
}

# Function to run composer commands
run_composer() {
    shift # Remove 'composer' from arguments
    print_status "Running composer command: $@"
    docker-compose -f docker-compose.dev.yml exec app composer "$@"
}

# Function to run tests
run_tests() {
    print_status "Running PHPUnit tests..."
    docker-compose -f docker-compose.dev.yml exec app php artisan test
}

# Function to run migrations
run_migrate() {
    print_status "Running database migrations..."
    docker-compose -f docker-compose.dev.yml exec app php artisan migrate
}

# Function to run seeders
run_seed() {
    print_status "Running database seeders..."
    docker-compose -f docker-compose.dev.yml exec app php artisan db:seed
}

# Function to fresh database with seeders
run_fresh() {
    print_status "Fresh database with seeders..."
    docker-compose -f docker-compose.dev.yml exec app php artisan migrate:fresh --seed
}

# Function to clean up Docker resources
clean_docker() {
    print_warning "This will remove all containers, networks, and volumes. Are you sure? (y/N)"
    read -r response
    if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
        print_status "Cleaning up Docker resources..."
        docker-compose -f docker-compose.dev.yml down -v --remove-orphans
        docker system prune -f
        print_success "Docker resources cleaned!"
    else
        print_status "Cleanup cancelled."
    fi
}

# Function to show status
show_status() {
    print_status "Docker services status:"
    docker-compose -f docker-compose.dev.yml ps
}

# Main script logic
case "${1:-help}" in
    start)
        start_dev
        ;;
    stop)
        stop_dev
        ;;
    restart)
        restart_dev
        ;;
    build)
        build_images
        ;;
    logs)
        show_logs
        ;;
    shell)
        open_shell
        ;;
    artisan)
        run_artisan "$@"
        ;;
    composer)
        run_composer "$@"
        ;;
    test)
        run_tests
        ;;
    migrate)
        run_migrate
        ;;
    seed)
        run_seed
        ;;
    fresh)
        run_fresh
        ;;
    clean)
        clean_docker
        ;;
    status)
        show_status
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        print_error "Unknown command: $1"
        show_help
        exit 1
        ;;
esac
