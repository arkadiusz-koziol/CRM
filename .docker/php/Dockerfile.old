FROM php:8.4-fpm AS skytech-php

# Copy composer.lock and composer.json into the working directory
COPY ./backend/composer.lock ./backend/composer.json /var/www/app/

# Set working directory
WORKDIR /var/www/app/

# Install dependencies for the operating system software
RUN apt-get update && \
    apt-get -y upgrade && \
    apt-get install -y aptitude && \
    apt-get install -y --no-install-recommends supervisor && \
    apt-get install -y build-essential byobu curl ca-certificates git htop man unzip vim wget libcurl4 libcurl4-gnutls-dev libpcre2-8-0 libpcre2-dev libxml2 libxml2-dev liblua5.3-0 liblua5.3-0-dbg liblua5.3-dev \
    libxml2-dev libxslt-dev libgd-dev libiconv-hook-dev libicu-dev libonig-dev zlib1g-dev libzip-dev libpq-dev \
    libbz2-dev openssl libssl-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev libmemcached-dev libmemcached11 \
    libmagickwand-dev imagemagick && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN update-ca-certificates && \
    docker-php-ext-install bcmath ctype curl dom fileinfo && \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-xpm --enable-gd-jis-conv && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install iconv intl mbstring xml simplexml xsl soap zip sockets exif && \
    docker-php-ext-enable exif && \
    docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install pdo pdo_pgsql pgsql

# Install Redis support
RUN pecl install redis && docker-php-ext-enable redis

# Install Imagick PHP extension
# Add the install-php-extensions script
RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    -o /usr/local/bin/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions

# Install Imagick (latest version compatible with PHP 8.4)
RUN install-php-extensions imagick

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents to the working directory
COPY ./backend /var/www/app

# Assign permissions of the working directory to the www-data user
RUN chown -R www-data:www-data /var/www -R

# Supervisord configuration
ADD .docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
ADD .docker/php/supervisord.conf /etc/

# Expose PHP-FPM port
EXPOSE 9000

# Start script
COPY .docker/php/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh
CMD ["/usr/local/bin/start.sh"]
