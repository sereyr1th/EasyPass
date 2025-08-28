#!/bin/bash

echo "🚀 Starting F4 EasyPass Services..."

# Kill any existing processes
echo "🛑 Stopping existing services..."
pkill -f "php artisan serve" 2>/dev/null
pkill -f "npm run dev" 2>/dev/null
sleep 2

# Start backend
echo "🔧 Starting Laravel Backend on port 8001..."
cd /home/rith/f4/backend
php artisan config:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
nohup php artisan serve --port=8001 > /tmp/laravel.log 2>&1 &
BACKEND_PID=$!

# Wait for backend to start
sleep 3

# Start frontend
echo "🌐 Starting Vue.js Frontend on port 3001..."
cd /home/rith/f4/frontend
nohup npm run dev -- --port 3001 --host 0.0.0.0 > /tmp/vue.log 2>&1 &
FRONTEND_PID=$!

# Wait for services to start
echo "⏳ Waiting for services to initialize..."
sleep 5

# Test services
echo "📊 Testing Services..."
echo "Backend (8001): $(curl -s -o /dev/null -w '%{http_code}' http://localhost:8001 || echo 'Failed')"
echo "Frontend (3001): $(curl -s -o /dev/null -w '%{http_code}' http://localhost:3001 || echo 'Failed')"

echo ""
echo "✅ Services Started!"
echo "🌐 Frontend: http://localhost:3001"
echo "🔧 Backend: http://localhost:8001"
echo ""
echo "📝 Process IDs:"
echo "   Backend PID: $BACKEND_PID"
echo "   Frontend PID: $FRONTEND_PID"
echo ""
echo "📋 To stop services:"
echo "   pkill -f 'php artisan serve'"
echo "   pkill -f 'npm run dev'"
echo ""
echo "📊 To check logs:"
echo "   tail -f /tmp/laravel.log"
echo "   tail -f /tmp/vue.log"
