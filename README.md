# 🎉 F4 EasyPass Event Management System

A full-stack web application built with Laravel (Backend) and Vue.js (Frontend) for event management and user authentication.

## 🚀 Quick Start

### One-Command Startup
```bash
./start-app.sh
```

### One-Command Shutdown
```bash
./stop-app.sh
```

## 🌐 Application URLs

- **🖥️ Frontend (Main App)**: http://localhost:3005
- **🔧 Backend API**: http://localhost:8002
- **🗄️ Database**: Local MySQL (easypass_db)

## 📋 Features

- ✅ User Registration & Authentication
- ✅ JWT Token-based API Authentication
- ✅ Event Management System
- ✅ Responsive Vue.js Frontend
- ✅ RESTful Laravel API
- ✅ MySQL Database Integration

## 🛠️ Technology Stack

- **Frontend**: Vue.js 3, Vite, Axios
- **Backend**: Laravel 12, PHP 8.2+
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum + JWT

## 📡 API Endpoints

### Authentication
- `POST /api/auth/register` - User Registration
- `POST /api/auth/login` - User Login
- `POST /api/auth/logout` - User Logout
- `GET /api/auth/user` - Get Authenticated User

### Health Check
- `GET /api/health` - API Health Status

## 🔧 Manual Setup (Alternative)

If you prefer to run components individually:

### Backend
```bash
cd backend
php artisan serve --port=8002
```

### Frontend
```bash
cd frontend
npm run dev -- --port=3005
```

## 📝 Configuration

### Database Configuration (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=easypass_db
DB_USERNAME=root
DB_PASSWORD=root@123
```

### Frontend API Configuration
The frontend is configured to connect to the backend at `http://localhost:8002`

## 🐛 Troubleshooting

### Port Conflicts
If ports 3005 or 8002 are in use, the startup script will handle this automatically.

### Database Issues
Ensure MySQL is running and the database `easypass_db` exists with the correct credentials.

### View Logs
- Backend logs: `/tmp/laravel-f4.log`
- Frontend logs: `/tmp/vue-f4.log`

## 📊 Project Structure

```
f4/
├── backend/           # Laravel API
├── frontend/          # Vue.js Application  
├── start-app.sh       # Start everything
├── stop-app.sh        # Stop everything
├── docker-compose.yml # Docker configuration (optional)
└── README.md          # This file
```

## 🎯 Usage

1. Run `./start-app.sh` to start the application
2. Open http://localhost:3005 in your browser
3. Register a new account or login with existing credentials
4. Use the event management features
5. Run `./stop-app.sh` when done

## 🔐 Security

- JWT tokens for API authentication
- Password hashing with bcrypt
- CORS configuration for frontend-backend communication
- Input validation and sanitization

---

**🌟 Your F4 EasyPass Event Management System is ready to use!**

For support or questions, check the log files or review the API responses for debugging information.