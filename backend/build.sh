#!/bin/bash

# Install dependencies
composer install --no-dev --optimize-autoloader

# Generate application key if not set
php artisan key:generate --force

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build completed successfully!"
