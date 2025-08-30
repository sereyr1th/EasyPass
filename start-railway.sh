#!/bin/bash

echo "🚀 Starting EasyPass-EMS on Railway..."
echo "Environment: $APP_ENV"
echo "Port: $PORT"
echo "Database URL: $DATABASE_URL"

# Ensure we're in the right directory
cd /app || exit 1

# Check if .env exists, if not create it
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    echo "APP_NAME=EasyPass-EMS" > .env
    echo "APP_ENV=production" >> .env
    echo "APP_DEBUG=false" >> .env
    echo "APP_KEY=" >> .env
    echo "DB_CONNECTION=pgsql" >> .env
    
    # Add DATABASE_URL if it exists
    if [ ! -z "$DATABASE_URL" ]; then
        echo "DATABASE_URL=$DATABASE_URL" >> .env
    fi
fi

# Generate app key if it doesn't exist
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Clear all caches first
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Cache configuration for production
echo "⚡ Caching configuration..."
php artisan config:cache

# Start the server
echo "🌐 Starting server on 0.0.0.0:${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
