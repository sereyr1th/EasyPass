@echo off
REM Clear Laravel caches and reload configuration
REM Run this script on Railway after updating environment variables

echo 🚀 Clearing Laravel caches and reloading configuration...

REM Navigate to backend directory
cd backend

echo 📝 Clearing configuration cache...
php artisan config:clear

echo 🔄 Caching new configuration...
php artisan config:cache

echo 🧹 Clearing route cache...
php artisan route:clear

echo 🔄 Caching routes...
php artisan route:cache

echo 🧽 Clearing view cache...
php artisan view:clear

echo 🔄 Caching views...
php artisan view:cache

echo 🧹 Clearing application cache...
php artisan cache:clear

echo ✅ All caches cleared and configuration reloaded!
echo 🔍 You can now test your CORS configuration at:
echo    https://easypass-ems-production.up.railway.app/api/health

echo.
echo 🔧 To check current CORS configuration, run:
echo    php artisan tinker --execute="echo 'Allowed Origins: ' . json_encode(config('cors.allowed_origins')); echo PHP_EOL . 'Sanctum Domains: ' . json_encode(config('sanctum.stateful'));"
