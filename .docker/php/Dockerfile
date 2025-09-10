FROM php:8.4-fpm

# Install nginx, supervisor and required libraries
RUN apt-get update && \
    apt-get install -y nginx supervisor libpq-dev libonig-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev libxpm-dev libzip-dev libmagickwand-dev && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip
RUN pecl install imagick redis && docker-php-ext-enable imagick redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/app

# Copy supervisord config
COPY .docker/php/supervisord.conf /etc/supervisor/supervisord.conf
COPY .docker/php/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]