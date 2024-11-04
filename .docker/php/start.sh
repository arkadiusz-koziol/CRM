#!/bin/sh

composer install &&
php artisan key:generate &&
php artisan migrate &&
php artisan acl:update &&
php artisan cache:clear &&
php artisan config:clear &&
php artisan route:clear &&
php artisan view:clear &&
php artisan storage:link &&
php artisan l5-swagger:generate &&
chmod 755 -R storage &&

if ! pgrep php-fpm > /dev/null 2>&1; then
  php-fpm
fi