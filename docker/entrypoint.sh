#!/bin/sh
set -e

# Run storage link
php artisan storage:link || true

# Run database migrations
echo "=== Running Database Migrations ==="
php artisan migrate --force

echo "=== Seeding Base Roles & B2B Marketplace Catalog ==="
php artisan db:seed --class=RoleSeeder --force || true
php artisan db:seed --class=SampleDataSeeder --force || true

# Cache routes and views for production speed
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Dynamically configure Nginx port for Render ($PORT or default 80)
PORT="${PORT:-80}"
echo "=== Configuring Nginx to listen on port ${PORT} ==="
sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/http.d/default.conf
sed -i "s/listen \[::\]:80;/listen \[::\]:${PORT};/g" /etc/nginx/http.d/default.conf

# Start PHP-FPM daemon
php-fpm -D

# Start Nginx
nginx -g "daemon off;"
