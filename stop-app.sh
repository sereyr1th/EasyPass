#!/bin/bash

echo "🛑 Stopping F4 EasyPass Event Management System"
echo "=============================================="

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to stop process by PID file
stop_by_pid() {
    local service=$1
    local pid_file=$2
    
    if [ -f "$pid_file" ]; then
        local pid=$(cat "$pid_file")
        if kill -0 "$pid" 2>/dev/null; then
            kill "$pid"
            echo -e "${GREEN}✅ Stopped $service (PID: $pid)${NC}"
        else
            echo -e "${YELLOW}⚠️ $service process not running${NC}"
        fi
        rm -f "$pid_file"
    else
        echo -e "${YELLOW}⚠️ No PID file found for $service${NC}"
    fi
}

# Stop Docker containers (if running)
echo -e "${YELLOW}🐳 Stopping Docker containers...${NC}"
docker compose down 2>/dev/null || true

# Stop backend
echo -e "${YELLOW}🔧 Stopping Backend...${NC}"
stop_by_pid "Backend" "/tmp/f4-backend.pid"
pkill -f "php artisan serve" 2>/dev/null || true

# Stop frontend
echo -e "${YELLOW}🌐 Stopping Frontend...${NC}"
stop_by_pid "Frontend" "/tmp/f4-frontend.pid"
pkill -f "npm run dev" 2>/dev/null || true
pkill -f "vite" 2>/dev/null || true

# Clean up log files
echo -e "${YELLOW}🧹 Cleaning up log files...${NC}"
rm -f /tmp/laravel-f4.log
rm -f /tmp/vue-f4.log

echo ""
echo -e "${GREEN}✅ F4 EasyPass Application stopped successfully!${NC}"
echo -e "${YELLOW}💡 To start again, run: ./start-app.sh${NC}"
