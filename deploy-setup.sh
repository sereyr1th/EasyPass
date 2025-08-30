#!/bin/bash

# EasyPass EMS Deployment Setup Script
# This script helps set up the project for deployment

echo "🚀 EasyPass EMS Deployment Setup"
echo "================================"

# Check if we're in the right directory
if [ ! -f "DEPLOYMENT_GUIDE.md" ]; then
    echo "❌ Please run this script from the project root directory"
    exit 1
fi

echo "📁 Setting up backend environment..."
cd backend

# Copy environment template for Railway
if [ ! -f ".env" ]; then
    if [ -f "env.railway.example" ]; then
        cp env.railway.example .env
        echo "✅ Created .env from Railway template"
        echo "⚠️  Please update the .env file with your actual values"
    else
        echo "❌ env.railway.example not found"
    fi
fi

# Generate Laravel application key
if command -v php &> /dev/null; then
    echo "🔑 Generating Laravel application key..."
    php artisan key:generate
    echo "✅ Laravel key generated"
else
    echo "⚠️  PHP not found. Please generate the Laravel key manually:"
    echo "   php artisan key:generate"
fi

cd ..

echo "📁 Setting up frontend environment..."
cd frontend

# Copy environment template for Vercel
if [ ! -f ".env" ]; then
    if [ -f "env.production.example" ]; then
        cp env.production.example .env.production
        cp env.development.example .env.local
        echo "✅ Created environment files"
        echo "⚠️  Please update the .env files with your actual values"
    else
        echo "❌ Environment templates not found"
    fi
fi

# Install dependencies if package.json exists
if [ -f "package.json" ] && command -v npm &> /dev/null; then
    echo "📦 Installing frontend dependencies..."
    npm install
    echo "✅ Frontend dependencies installed"
else
    echo "⚠️  Please install frontend dependencies manually:"
    echo "   cd frontend && npm install"
fi

cd ..

echo ""
echo "✅ Deployment setup complete!"
echo ""
echo "📋 Next steps:"
echo "1. Update environment variables in backend/.env and frontend/.env files"
echo "2. Set up Railway project and add MySQL database"
echo "3. Set up Vercel project"
echo "4. Configure Stripe webhooks"
echo "5. Follow the DEPLOYMENT_GUIDE.md for detailed instructions"
echo ""
echo "🔗 Helpful links:"
echo "   Railway: https://railway.app"
echo "   Vercel: https://vercel.com"
echo "   Deployment Guide: ./DEPLOYMENT_GUIDE.md"
