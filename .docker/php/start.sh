#!/bin/sh

git config --global --add safe.directory /var/www/app
composer install --no-interaction --prefer-dist --optimize-autoloader
exec supervisord -c /etc/supervisor/supervisord.conf