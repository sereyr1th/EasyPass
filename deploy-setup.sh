#!/bin/bash

echo "🚀 EasyPass-EMS Deployment Setup"
echo "================================="

# Check if we're in the right directory
if [ ! -f "composer.json" ] && [ ! -d "backend" ]; then
    echo "❌ Please run this script from the project root directory"
    exit 1
fi

echo "📋 Setting up deployment files..."

# Create backend environment file if it doesn't exist
if [ ! -f "backend/.env" ]; then
    echo "📝 Creating backend .env file..."
    if [ -f "backend/.env.example" ]; then
        cp backend/.env.example backend/.env
    else
        echo "⚠️  No .env.example found. Please create backend/.env manually"
    fi
fi

# Generate Laravel app key
echo "🔑 Generating Laravel application key..."
cd backend
php artisan key:generate --force
cd ..

# Install backend dependencies
echo "📦 Installing backend dependencies..."
cd backend
composer install --no-dev --optimize-autoloader
cd ..

# Install frontend dependencies
echo "🌐 Installing frontend dependencies..."
cd frontend
npm ci
cd ..

# Build frontend for production
echo "🏗️  Building frontend for production..."
cd frontend
npm run build
cd ..

echo ""
echo "✅ Setup complete!"
echo ""
echo "📋 Next Steps:"
echo "1. Choose a deployment platform from DEPLOYMENT_GUIDE.md"
echo "2. Configure environment variables for your chosen platform"
echo "3. Deploy using the platform-specific instructions"
echo ""
echo "🔗 Recommended platforms:"
echo "   • Railway (easiest): https://railway.app"
echo "   • Render (reliable): https://render.com"
echo "   • Vercel + Supabase (frontend-focused): https://vercel.com"
echo ""
echo "📚 Full guide: ./DEPLOYMENT_GUIDE.md"
