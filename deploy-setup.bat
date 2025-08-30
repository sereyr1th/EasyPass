@echo off
REM EasyPass EMS Deployment Setup Script for Windows
REM This script helps set up the project for deployment

echo 🚀 EasyPass EMS Deployment Setup
echo ================================

REM Check if we're in the right directory
if not exist "DEPLOYMENT_GUIDE.md" (
    echo ❌ Please run this script from the project root directory
    pause
    exit /b 1
)

echo 📁 Setting up backend environment...
cd backend

REM Copy environment template for Railway
if not exist ".env" (
    if exist "env.railway.example" (
        copy env.railway.example .env
        echo ✅ Created .env from Railway template
        echo ⚠️  Please update the .env file with your actual values
    ) else (
        echo ❌ env.railway.example not found
    )
)

REM Generate Laravel application key
where php >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo 🔑 Generating Laravel application key...
    php artisan key:generate
    echo ✅ Laravel key generated
) else (
    echo ⚠️  PHP not found. Please generate the Laravel key manually:
    echo    php artisan key:generate
)

cd ..

echo 📁 Setting up frontend environment...
cd frontend

REM Copy environment template for Vercel
if not exist ".env" (
    if exist "env.production.example" (
        copy env.production.example .env.production
        copy env.development.example .env.local
        echo ✅ Created environment files
        echo ⚠️  Please update the .env files with your actual values
    ) else (
        echo ❌ Environment templates not found
    )
)

REM Install dependencies if package.json exists
if exist "package.json" (
    where npm >nul 2>nul
    if %ERRORLEVEL% EQU 0 (
        echo 📦 Installing frontend dependencies...
        npm install
        echo ✅ Frontend dependencies installed
    ) else (
        echo ⚠️  Please install frontend dependencies manually:
        echo    cd frontend ^&^& npm install
    )
)

cd ..

echo.
echo ✅ Deployment setup complete!
echo.
echo 📋 Next steps:
echo 1. Update environment variables in backend\.env and frontend\.env files
echo 2. Set up Railway project and add MySQL database
echo 3. Set up Vercel project
echo 4. Configure Stripe webhooks
echo 5. Follow the DEPLOYMENT_GUIDE.md for detailed instructions
echo.
echo 🔗 Helpful links:
echo    Railway: https://railway.app
echo    Vercel: https://vercel.com
echo    Deployment Guide: .\DEPLOYMENT_GUIDE.md

pause
