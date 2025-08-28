#!/bin/bash

echo "🚀 Starting F4 EasyPass Event Management System"
echo "================================================"

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to check if port is in use
check_port() {
    if lsof -Pi :$1 -sTCP:LISTEN -t >/dev/null ; then
        return 0
    else
        return 1
    fi
}

# Function to stop existing processes
stop_existing() {
    echo -e "${YELLOW}🛑 Stopping any existing processes...${NC}"
    
    # Stop Docker containers
    if command -v docker-compose &> /dev/null || command -v docker &> /dev/null; then
        docker compose down 2>/dev/null || true
    fi
    
    # Kill existing Laravel and Node processes
    pkill -f "php artisan serve" 2>/dev/null || true
    pkill -f "npm run dev" 2>/dev/null || true
    pkill -f "vite" 2>/dev/null || true
    
    sleep 2
}

# Function to check MySQL connection
check_mysql() {
    echo -e "${BLUE}🗄️ Checking database connection...${NC}"
    cd backend
    if php artisan migrate:status >/dev/null 2>&1; then
        echo -e "${GREEN}✅ Database connected successfully${NC}"
        return 0
    else
        echo -e "${RED}❌ Database connection failed${NC}"
        echo -e "${YELLOW}Please ensure MySQL is running and credentials are correct${NC}"
        return 1
    fi
    cd ..
}

# Function to start backend
start_backend() {
    echo -e "${BLUE}🔧 Starting Laravel Backend...${NC}"
    cd backend
    
    # Clear caches
    php artisan config:clear >/dev/null 2>&1
    php artisan cache:clear >/dev/null 2>&1
    
    # Start Laravel server
    nohup php artisan serve --port=8002 > /tmp/laravel-f4.log 2>&1 &
    BACKEND_PID=$!
    
    # Wait for backend to start
    sleep 3
    
    if check_port 8002; then
        echo -e "${GREEN}✅ Backend started on http://localhost:8002${NC}"
        echo $BACKEND_PID > /tmp/f4-backend.pid
        cd ..
        return 0
    else
        echo -e "${RED}❌ Failed to start backend${NC}"
        cd ..
        return 1
    fi
}

# Function to start frontend
start_frontend() {
    echo -e "${BLUE}🌐 Starting Vue.js Frontend...${NC}"
    cd frontend
    
    # Start Vite dev server
    nohup npm run dev -- --port 3005 --host 0.0.0.0 > /tmp/vue-f4.log 2>&1 &
    FRONTEND_PID=$!
    
    # Wait for frontend to start
    sleep 5
    
    if check_port 3005; then
        echo -e "${GREEN}✅ Frontend started on http://localhost:3005${NC}"
        echo $FRONTEND_PID > /tmp/f4-frontend.pid
        cd ..
        return 0
    else
        echo -e "${RED}❌ Failed to start frontend${NC}"
        cd ..
        return 1
    fi
}

# Function to show status
show_status() {
    echo ""
    echo -e "${GREEN}🎉 F4 EasyPass Application Started Successfully!${NC}"
    echo "================================================"
    echo -e "${BLUE}📱 Frontend:${NC} http://localhost:3005"
    echo -e "${BLUE}🔧 Backend API:${NC} http://localhost:8002"
    echo -e "${BLUE}🗄️ Database:${NC} Local MySQL (easypass_db)"
    echo ""
    echo -e "${YELLOW}📋 Available API Endpoints:${NC}"
    echo "   • POST /api/auth/register - User Registration"
    echo "   • POST /api/auth/login - User Login"
    echo "   • GET /api/health - Health Check"
    echo ""
    echo -e "${YELLOW}📝 Log Files:${NC}"
    echo "   • Backend: /tmp/laravel-f4.log"
    echo "   • Frontend: /tmp/vue-f4.log"
    echo ""
    echo -e "${GREEN}🌟 Open http://localhost:3005 in your browser to start using the app!${NC}"
    echo ""
    echo -e "${YELLOW}To stop the application, run: ./stop-app.sh${NC}"
}

# Main execution
main() {
    # Stop any existing processes
    stop_existing
    
    # Check database connection
    if ! check_mysql; then
        echo -e "${RED}❌ Cannot start application - database issues${NC}"
        exit 1
    fi
    
    # Start backend
    if ! start_backend; then
        echo -e "${RED}❌ Failed to start backend${NC}"
        exit 1
    fi
    
    # Start frontend
    if ! start_frontend; then
        echo -e "${RED}❌ Failed to start frontend${NC}"
        exit 1
    fi
    
    # Show success status
    show_status
}

# Run main function
main
