#!/usr/bin/env bash
set -e

echo "Preparing Apache port..."
envsubst '${PORT}' < /etc/apache2/sites-available/000-default.conf.template > /etc/apache2/sites-available/000-default.conf

echo "Preparing Laravel folders..."
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

echo "Clearing old Laravel cache..."
php artisan optimize:clear || true

echo "Running database migrations..."
php artisan migrate --force

echo "Creating production admin if env variables exist..."
php artisan db:seed --class=ProductionAdminSeeder --force || true

echo "Caching Laravel for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
apache2-foreground
