#!/bin/bash

# Wait for database to be ready
echo "Waiting for database..."
sleep 10

# Generate app key if not exists
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
apache2-foreground
