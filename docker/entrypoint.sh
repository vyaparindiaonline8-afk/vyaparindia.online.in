#!/bin/sh
set -e

# Ensure all essential storage directories exist and have proper permissions
mkdir -p /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/framework/cache/data \
         /var/www/storage/logs \
         /var/www/storage/app/public \
         /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true
chmod -R 777 /var/www/storage /var/www/bootstrap/cache || true

# Run storage link
php artisan storage:link || true

# Run database migrations
echo "=== Running Database Migrations ==="
php artisan migrate --force || true

echo "=== Seeding Base Roles & B2B Marketplace Catalog ==="
php artisan db:seed --class=RoleSeeder --force || true
php artisan db:seed --class=SampleDataSeeder --force || true
php artisan db:seed --class=BrandMasterSeeder --force || true

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
