# 🚀 Complete Railway Deployment Guide for EasyPass-EMS

## Overview
This guide will help you deploy your EasyPass-EMS application with:
- **Backend**: Laravel API on Railway
- **Frontend**: Vue.js app on Railway 
- **Database**: MySQL on Railway

## Prerequisites
- Railway account (https://railway.app)
- GitHub repository connected
- Stripe account for payments

---

## 🎯 Step 1: Create Railway Project

1. **Sign in to Railway**: https://railway.app
2. **Create New Project**:
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Choose your `EasyPass-EMS` repository
   - Railway will auto-detect Laravel

---

## 🗄️ Step 2: Add MySQL Database

1. **In your Railway project dashboard**:
   - Click "New Service"
   - Select "Database" → "Add MySQL"
   - Railway automatically creates connection variables

---

## ⚙️ Step 3: Configure Backend Service

### Set Root Directory
- Go to your backend service → Settings
- Set **Root Directory** to: `backend/`

### Environment Variables
Go to Variables tab and add these:

```bash
# Essential Variables
APP_NAME=EasyPass EMS
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:2HvSlIoYtgRUy1hBs4hTKYVIBy+q70fAOKwYESK7ngY=
APP_URL=https://your-backend-service.up.railway.app
APP_TIMEZONE=UTC

# Database (Railway auto-provides these reference variables)
DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

# Performance & Caching
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
SESSION_LIFETIME=120

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=info

# Required for Laravel Passport
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=
```

### Stripe Configuration (Required)
```bash
STRIPE_KEY=pk_live_your_publishable_key_here
STRIPE_SECRET=sk_live_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

### Mail Configuration (Optional)
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@easypass.com
MAIL_FROM_NAME=EasyPass EMS
```

---

## 🎨 Step 4: Add Frontend Service

1. **Add New Service**:
   - Click "New Service" in your Railway project
   - Select "GitHub Repo" → same repository
   - Set **Root Directory** to: `frontend/`

### Frontend Environment Variables
```bash
NODE_ENV=production
VITE_API_URL=https://your-backend-service.up.railway.app
VITE_APP_NAME=EasyPass EMS
VITE_APP_VERSION=1.0.0
VITE_STRIPE_PUBLISHABLE_KEY=pk_live_your_publishable_key_here
```

---

## 🔗 Step 5: Update CORS Configuration

After both services are deployed:

1. **Get your service URLs** from Railway dashboard
2. **Update backend CORS variables**:
```bash
CORS_ALLOWED_ORIGINS=https://your-frontend-service.up.railway.app
SANCTUM_STATEFUL_DOMAINS=your-frontend-service.up.railway.app
SESSION_DOMAIN=.up.railway.app
```

---

## ✅ Step 6: Verification

### Backend Health Check
Visit: `https://your-backend-service.up.railway.app/api/health`
Should return: `{"status":"success","message":"EasyPass API is running"}`

### Database Migration
Check Railway logs to ensure:
- Migrations ran successfully
- Admin user seeded (admin@easypass.com / admin123)
- Laravel Passport keys generated

### Frontend Access
- Visit your frontend URL
- Should load EasyPass login page
- Test admin login

---

## 💳 Step 7: Stripe Webhook Setup

1. **Go to Stripe Dashboard** → Webhooks
2. **Create New Webhook**:
   - **URL**: `https://your-backend-service.up.railway.app/api/payments/webhook/stripe`
   - **Events**: Select all payment-related events
3. **Copy webhook secret** and update `STRIPE_WEBHOOK_SECRET` in Railway

---

## 🔧 Build Configuration

Railway should auto-detect these, but if needed:

### Backend (Laravel)
- **Build Command**: `composer install --no-dev --optimize-autoloader`
- **Start Command**: `php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT`

### Frontend (Vue.js)
- **Build Command**: `npm ci && npm run build`
- **Start Command**: `npm run preview -- --host 0.0.0.0 --port $PORT`

---

## 🚨 Important Notes

1. **Replace placeholder URLs** with your actual Railway service URLs
2. **Use live Stripe keys** for production
3. **Keep secrets secure** - never commit them to git
4. **Test thoroughly** before going live
5. **Monitor logs** in Railway dashboard for any issues

---

## 🆘 Troubleshooting

### Build Failures
- Check all environment variables are set
- Verify APP_KEY format is correct
- Review build logs for specific errors

### Database Issues
- Ensure MySQL service is running
- Check database connection variables
- Verify migration files are valid

### CORS Errors
- Double-check CORS_ALLOWED_ORIGINS matches frontend URL
- Ensure SANCTUM_STATEFUL_DOMAINS is correct
- Clear browser cache and try again

---

## 📱 Default Admin Access

After successful deployment:
- **Email**: admin@easypass.com
- **Password**: admin123
- **⚠️ Change password immediately after first login!**

---

## 🎉 You're Done!

Your EasyPass-EMS application should now be fully deployed on Railway with:
- ✅ Backend API running
- ✅ Frontend application accessible  
- ✅ MySQL database connected
- ✅ Stripe payments configured
- ✅ Admin panel ready

Visit your frontend URL and start managing events! 🎫
