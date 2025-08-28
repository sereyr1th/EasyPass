# 🐳 Docker Setup for F4 Application

## Why Docker is the Solution

Your login/registration was failing because:
1. **Database Connection Issues**: Missing `.env` configuration causing MySQL connection failures
2. **Port Mismatches**: Frontend trying to connect to port 3000, but backend running on 8000
3. **Environment Inconsistencies**: Manual setup prone to configuration drift

Docker solves all these issues by providing:
- ✅ **Consistent Environment**: Same setup every time
- ✅ **Automatic Database Setup**: MySQL container with proper configuration
- ✅ **Service Orchestration**: All services start together with correct dependencies
- ✅ **Port Management**: Proper port mapping and service discovery
- ✅ **Zero Configuration**: No manual database setup required

## 🚀 Quick Start

### Option 1: Use the Startup Script (Recommended)
```bash
./start-docker.sh
```

### Option 2: Manual Docker Commands
```bash
# Start all services
docker-compose up --build -d

# View logs
docker-compose logs -f

# Stop all services
docker-compose down
```

## 📋 What Gets Started

| Service | URL | Description |
|---------|-----|-------------|
| **Frontend** | http://localhost:3000 | Vue.js application |
| **Backend** | http://localhost:8000 | Laravel API |
| **Database** | localhost:3306 | MySQL 8.0 |

## 🔧 Service Details

### Frontend (Vue.js)
- **Port**: 3000
- **Hot Reload**: Enabled
- **API Connection**: Automatically configured to backend

### Backend (Laravel)
- **Port**: 8000
- **Database**: Auto-migrated with seed data
- **API Key**: Auto-generated
- **Authentication**: Laravel Sanctum

### Database (MySQL)
- **Port**: 3306
- **Database**: f4_db
- **User**: f4_user
- **Password**: f4_password
- **Root Password**: root_password

## 🛠️ Development Workflow

### First Time Setup
```bash
# Clone and navigate to project
cd /home/rith/f4

# Start everything
./start-docker.sh
```

### Daily Development
```bash
# Start services
docker-compose up -d

# View logs (optional)
docker-compose logs -f

# Stop when done
docker-compose down
```

### Useful Commands
```bash
# View running containers
docker-compose ps

# Access backend container
docker-compose exec backend bash

# Access database
docker-compose exec mysql mysql -u f4_user -pf4_password f4_db

# Rebuild after code changes
docker-compose up --build -d

# Clean restart
docker-compose down -v && docker-compose up --build -d
```

## 🐛 Troubleshooting

### Services Won't Start
```bash
# Check Docker is running
docker info

# Check port conflicts
netstat -tulpn | grep -E ':(3000|8000|3306)'

# Clean restart
docker-compose down -v
docker system prune -f
docker-compose up --build -d
```

### Database Connection Issues
```bash
# Check MySQL is ready
docker-compose logs mysql

# Reset database
docker-compose down -v
docker-compose up --build -d
```

### Frontend Can't Connect to Backend
- Verify backend is running: `curl http://localhost:8000/api/health`
- Check axios configuration in `frontend/src/plugins/axios.ts`

### Permission Issues
```bash
# Fix Laravel permissions
docker-compose exec backend chown -R www-data:www-data /var/www/html
docker-compose exec backend chmod -R 755 /var/www/html/storage
```

## 🔄 Migration from Manual Setup

If you were running services manually before:

1. **Stop all manual processes**:
   - Stop `php artisan serve`
   - Stop `npm run dev`
   - Stop any MySQL services

2. **Use Docker setup**:
   ```bash
   ./start-docker.sh
   ```

3. **Verify everything works**:
   - Frontend: http://localhost:3000
   - Backend health: http://localhost:8000/api/health
   - Test login/registration

## 📊 Performance Benefits

- **Faster startup**: All services start in parallel
- **Consistent database**: No more "it works on my machine"
- **Easy reset**: `docker-compose down -v` for clean slate
- **Isolated environment**: No conflicts with system packages

## 🔒 Security Notes

- Database credentials are for development only
- Change passwords in production
- `.env` files contain sensitive data (already in .gitignore)

## 📝 Next Steps

1. Run `./start-docker.sh`
2. Visit http://localhost:3000
3. Test registration and login
4. Start developing! 🎉

---

**Need help?** Check the logs with `docker-compose logs -f` or create an issue.
