# 🚀 EasyPass-EMS Deployment Guide

This guide covers multiple **FREE** deployment options for your Laravel + Vue.js application.

## 📋 Prerequisites

Before deploying, ensure you have:
- ✅ Git repository with your code
- ✅ GitHub/GitLab account
- ✅ Basic understanding of environment variables

## 🏆 Option 1: Railway (Recommended)

Railway offers the easiest Laravel deployment with generous free tier.

### Step 1: Setup Railway Account
1. Visit [railway.app](https://railway.app)
2. Sign up with GitHub
3. Install Railway CLI (optional): `npm install -g @railway/cli`

### Step 2: Deploy Backend
1. Click "New Project" → "Deploy from GitHub repo"
2. Select your EasyPass-EMS repository
3. Railway will auto-detect Laravel (via `composer.json` and `artisan` at root) and create services:
   - **Web Service** (your Laravel app)
   - **PostgreSQL Database** (automatically provisioned)

**Note**: If Railway shows a build error about not detecting the project type, make sure you have:
- `composer.json` at the root level ✅
- `artisan` file at the root level ✅
- `nixpacks.toml` configuration file ✅

### Step 3: Configure Environment Variables
In Railway dashboard, add these variables to your web service:
```env
APP_NAME=EasyPass EMS
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY
APP_URL=https://your-app-name.railway.app

# Database (auto-filled by Railway)
DB_CONNECTION=pgsql
DATABASE_URL=${{ Postgres.DATABASE_URL }}

# Sessions & Cache
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database

# CORS for frontend
SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com
SESSION_DOMAIN=.railway.app

# Bakong (add your credentials)
BAKONG_API_KEY=your_api_key
BAKONG_API_SECRET=your_api_secret
```

### Step 4: Deploy Frontend
**Option A: Same Railway Project**
1. Add another service → "Empty Service"
2. Connect same GitHub repo
3. Set build command: `cd frontend && npm ci && npm run build`
4. Set start command: `npx serve -s frontend/dist -p $PORT`

**Option B: Vercel (Recommended for frontend)**
1. Visit [vercel.com](https://vercel.com)
2. Import your GitHub repo
3. Set framework preset: "Vue.js"
4. Set root directory: `frontend`
5. Add environment variable: `VITE_API_URL=https://your-backend.railway.app`

### Step 5: Final Setup
1. Your backend will be available at: `https://your-app-name.railway.app`
2. Test API endpoint: `https://your-app-name.railway.app/api/health`
3. Update frontend API URL to point to your Railway backend

---

## 🥈 Option 2: Render.com

Render offers reliable free hosting with automatic deployments.

### Step 1: Setup Render Account
1. Visit [render.com](https://render.com)
2. Sign up with GitHub

### Step 2: Create PostgreSQL Database
1. Click "New" → "PostgreSQL"
2. Name: `easypass-db`
3. Select free tier
4. Note down the connection details

### Step 3: Deploy Backend
1. Click "New" → "Web Service"
2. Connect your GitHub repository
3. Configure:
   - **Name**: `easypass-backend`
   - **Environment**: `Docker`
   - **Region**: Choose closest to your users
   - **Branch**: `main`
   - **Dockerfile Path**: `./Dockerfile`

### Step 4: Environment Variables
Add these in Render dashboard:
```env
APP_NAME=EasyPass EMS
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY

# Database (from your Render PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=your-db-host.render.com
DB_PORT=5432
DB_DATABASE=easypass
DB_USERNAME=your-username
DB_PASSWORD=your-password

SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

### Step 5: Deploy Frontend
1. Create new "Static Site"
2. Connect same GitHub repo
3. Set:
   - **Build Command**: `cd frontend && npm ci && npm run build`
   - **Publish Directory**: `frontend/dist`
4. Add environment variable: `VITE_API_URL=https://your-backend.onrender.com`

---

## 🌐 Option 3: Vercel + Supabase

Best for frontend-heavy applications with simple backend needs.

### Step 1: Setup Supabase Database
1. Visit [supabase.com](https://supabase.com)
2. Create new project
3. Note database URL from Settings → Database

### Step 2: Deploy Backend to Vercel
1. Visit [vercel.com](https://vercel.com)
2. Import GitHub repo
3. Set framework: "Other"
4. Set root directory: `backend`
5. Add build command: `composer install --no-dev && php artisan config:cache`

### Step 3: Deploy Frontend
1. Create another Vercel project
2. Set framework: "Vue.js"
3. Set root directory: `frontend`
4. Add environment: `VITE_API_URL=https://your-backend.vercel.app`

---

## 🐳 Option 4: Self-Hosting with Docker

If you have access to a VPS or want to run locally for testing.

### Step 1: Clone and Setup
```bash
git clone https://github.com/yourusername/EasyPass-EMS.git
cd EasyPass-EMS
```

### Step 2: Configure Environment
```bash
# Copy environment file
cp backend/.env.production backend/.env

# Generate app key
cd backend
php artisan key:generate
```

### Step 3: Deploy with Docker
```bash
# Build and start services
docker-compose up --build -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Check status
docker-compose ps
```

Your application will be available at `http://localhost:8000`

---

## 🔧 Post-Deployment Checklist

After successful deployment:

### ✅ Backend Verification
- [ ] API health check: `GET /api/health`
- [ ] Database connection working
- [ ] Authentication endpoints responding
- [ ] File uploads working (if applicable)

### ✅ Frontend Verification  
- [ ] Application loads without errors
- [ ] API calls connecting to backend
- [ ] User registration/login working
- [ ] Event creation/ticket purchasing functional

### ✅ Security & Performance
- [ ] HTTPS enabled (automatic on most platforms)
- [ ] Environment variables secure
- [ ] CORS properly configured
- [ ] Database migrations applied
- [ ] Error logging configured

---

## 🐛 Common Issues & Solutions

### Issue: "Application Key Not Set"
```bash
# Generate new key in your deployment platform
php artisan key:generate --show
# Copy the output to APP_KEY environment variable
```

### Issue: Database Connection Failed
- Verify database credentials in environment variables
- Ensure database service is running
- Check if migrations have been applied

### Issue: CORS Errors
```env
# Add your frontend domain to SANCTUM_STATEFUL_DOMAINS
SANCTUM_STATEFUL_DOMAINS=localhost:3000,your-frontend-domain.com
```

### Issue: Frontend Can't Connect to Backend
- Verify `VITE_API_URL` in frontend environment
- Check if backend is accessible via provided URL
- Ensure API routes are properly defined

---

## 💰 Cost Breakdown

| Platform | Backend | Database | Frontend | Total/Month |
|----------|---------|----------|----------|-------------|
| **Railway** | Free (512MB) | Free (PostgreSQL) | $0-5 | **$0-5** |
| **Render** | Free (512MB) | Free (PostgreSQL) | Free | **$0** |
| **Vercel + Supabase** | Free (Functions) | Free (PostgreSQL) | Free | **$0** |
| **Self-hosted** | VPS cost | Included | Included | **$5-20** |

---

## 🚀 Quick Start Commands

Choose your preferred platform:

**Railway:**
```bash
npm install -g @railway/cli
railway login
railway link
railway up
```

**Render:**
- Use web dashboard (easiest)
- Or use `render.yaml` file provided

**Docker (local):**
```bash
docker-compose up --build -d
```

---

## 📞 Need Help?

- 📚 Check platform documentation
- 🐛 Review application logs
- 💬 Join platform community forums
- 📧 Contact support if using paid tiers

**Happy Deploying! 🎉**
