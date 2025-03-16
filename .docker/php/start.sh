#!/bin/sh

composer install &&
if [ -f /etc/ImageMagick-6/policy.xml ]; then
  echo "Updating /etc/ImageMagick-6/policy.xml..."
  sed -i \
    -e 's|<policy domain="coder" rights="none" pattern="PDF" />|<!-- <policy domain="coder" rights="none" pattern="PDF" /> -->|g' \
    /etc/ImageMagick-6/policy.xml
  else
      echo "No policy.xml found for ImageMagick 6 or 7. Skipping..."
  fi &&
php artisan key:generate &&
php artisan migrate &&
php artisan acl:update &&
php artisan cache:clear &&
php artisan config:clear &&
php artisan route:clear &&
php artisan view:clear &&
php artisan storage:link &&
php artisan l5-swagger:generate &&
mkdir -p /var/www/app/storage/logs &&
touch /var/www/app/storage/logs/laravel.log &&
chmod 755 -R storage &&
chown -R www-data:www-data storage &&

if ! pgrep php-fpm > /dev/null 2>&1; then
  php-fpm
fi