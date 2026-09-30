#!/bin/sh
set -e

# Run storage link
php artisan storage:link || true

# Run database migrations
php artisan migrate --force || true

# Cache routes and views for production speed
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start PHP-FPM daemon
php-fpm -D

# Start Nginx
nginx -g "daemon off;"
