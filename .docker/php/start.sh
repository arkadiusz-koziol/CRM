#!/bin/sh

php-fpm &&
composer install &&
php artisan key:generate &&
php artisan cache:clear &&
php artisan config:clear &&
php artisan route:clear &&
php artisan view:clear &&
php artisan migrate &&
php artisan db:seed &&
php artisan storage:link