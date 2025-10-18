# Docker Development Environment

This document describes how to set up and use the Docker development environment for the CRM project.

## Prerequisites

- Docker and Docker Compose installed
- Git (for cloning the repository)

## Quick Start

1. **Start the development environment:**
   ```bash
   ./scripts/docker-dev.sh start
   ```

2. **Access the application:**
   - Main application: http://localhost:8199
   - phpMyAdmin: http://localhost:8080

## Services

The development environment includes the following services:

- **app**: PHP 8.4 with Laravel application
- **nginx**: Web server on port 8199
- **pgsql**: PostgreSQL database on port 5433
- **redis**: Redis cache/queue on port 6380
- **phpmyadmin**: Database administration on port 8080

## Available Commands

Use the `./scripts/docker-dev.sh` script for common development tasks:

### Basic Operations
```bash
# Start the environment
./scripts/docker-dev.sh start

# Stop the environment
./scripts/docker-dev.sh stop

# Restart the environment
./scripts/docker-dev.sh restart

# Build Docker images
./scripts/docker-dev.sh build
```

### Development Tasks
```bash
# Open shell in the app container
./scripts/docker-dev.sh shell

# Run Laravel artisan commands
./scripts/docker-dev.sh artisan migrate
./scripts/docker-dev.sh artisan make:controller UserController

# Run composer commands
./scripts/docker-dev.sh composer install
./scripts/docker-dev.sh composer require package/name

# Run tests
./scripts/docker-dev.sh test

# Database operations
./scripts/docker-dev.sh migrate
./scripts/docker-dev.sh seed
./scripts/docker-dev.sh fresh
```

### Monitoring and Debugging
```bash
# View logs
./scripts/docker-dev.sh logs

# Check service status
./scripts/docker-dev.sh status

# Clean up Docker resources
./scripts/docker-dev.sh clean
```

## Environment Configuration

The Docker environment uses the following configuration:

- **Database**: PostgreSQL with database `crm_dev`
- **Cache/Queue**: Redis
- **PHP Version**: 8.4
- **Laravel**: Latest version with all required extensions

## Port Mappings

- **8199**: Main application (nginx)
- **8080**: phpMyAdmin
- **5433**: PostgreSQL (external access)
- **6380**: Redis (external access)

## File Structure

```
docker/
├── nginx/
│   └── dev.conf          # Nginx configuration
├── supervisord.conf       # Process management
├── start.sh              # Container startup script
└── env.docker            # Environment variables

docker-compose.dev.yml     # Docker Compose configuration
scripts/
└── docker-dev.sh         # Development helper script
```

## Development Workflow

1. **Start the environment:**
   ```bash
   ./scripts/docker-dev.sh start
   ```

2. **Run initial setup:**
   ```bash
   ./scripts/docker-dev.sh artisan migrate
   ./scripts/docker-dev.sh artisan db:seed
   ```

3. **Start developing:**
   - Edit files in the `backend/` directory
   - Changes are automatically reflected in the container
   - Use `./scripts/docker-dev.sh shell` to access the container

4. **Run tests:**
   ```bash
   ./scripts/docker-dev.sh test
   ```

## Troubleshooting

### Common Issues

1. **Port conflicts:**
   - Make sure ports 8199, 8080, 5433, and 6380 are not in use
   - Change ports in `docker-compose.dev.yml` if needed

2. **Permission issues:**
   - Ensure Docker has proper permissions
   - On Linux, you might need to add your user to the docker group

3. **Database connection issues:**
   - Wait for the database to be ready (startup script handles this)
   - Check if the database container is running: `./scripts/docker-dev.sh status`

4. **Application key not set:**
   - The startup script automatically generates an application key
   - If issues persist, run: `./scripts/docker-dev.sh artisan key:generate`

### Logs and Debugging

```bash
# View all logs
./scripts/docker-dev.sh logs

# View specific service logs
docker-compose -f docker-compose.dev.yml logs app
docker-compose -f docker-compose.dev.yml logs nginx
docker-compose -f docker-compose.dev.yml logs pgsql
```

### Clean Start

If you encounter persistent issues:

```bash
# Stop and clean everything
./scripts/docker-dev.sh clean

# Start fresh
./scripts/docker-dev.sh start
./scripts/docker-dev.sh artisan migrate:fresh --seed
```

## Production Considerations

This Docker setup is designed for development only. For production:

- Use production-optimized images
- Implement proper security measures
- Use environment-specific configurations
- Set up proper logging and monitoring
- Use external database and Redis services

## Support

For issues related to the Docker setup:

1. Check the logs: `./scripts/docker-dev.sh logs`
2. Verify service status: `./scripts/docker-dev.sh status`
3. Try a clean restart: `./scripts/docker-dev.sh clean && ./scripts/docker-dev.sh start`
4. Check the Laravel logs in `backend/storage/logs/`
