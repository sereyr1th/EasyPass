# 🚀 Railway Backend Setup Checklist

## Pre-Deployment Setup

- [ ] **Railway Account Created**
  - Signed up at railway.app
  - Connected GitHub account

- [ ] **Project Created**
  - Selected "Deploy from GitHub repo"
  - Connected EasyPass-EMS repository
  - Railway detected Laravel project

- [ ] **MySQL Database Added**
  - Added MySQL service to project
  - Database automatically configured

## Environment Variables Configuration

Copy these to Railway → Your Service → Variables:

### ✅ Essential Variables
- [ ] `APP_NAME=EasyPass EMS`
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY=base64:kkk4HTkbQ2YUKY3kvZEOEuxqqPhQjRcebGHD+RBjANw=`
- [ ] `APP_URL=https://your-project.up.railway.app` (update with actual URL)
- [ ] `APP_TIMEZONE=UTC`

### 🗄️ Database Variables (Auto-configured by Railway)
- [ ] `DB_CONNECTION=mysql`
- [ ] `DB_HOST=${{MYSQLHOST}}`
- [ ] `DB_PORT=${{MYSQLPORT}}`
- [ ] `DB_DATABASE=${{MYSQLDATABASE}}`
- [ ] `DB_USERNAME=${{MYSQLUSER}}`
- [ ] `DB_PASSWORD=${{MYSQLPASSWORD}}`

### ⚡ Performance Variables
- [ ] `CACHE_STORE=database`
- [ ] `SESSION_DRIVER=database`
- [ ] `QUEUE_CONNECTION=database`
- [ ] `SESSION_LIFETIME=120`
- [ ] `LOG_CHANNEL=stack`
- [ ] `LOG_LEVEL=info`

### 💳 Stripe Configuration (Required)
- [ ] `STRIPE_KEY=pk_live_your_key` (get from Stripe dashboard)
- [ ] `STRIPE_SECRET=sk_live_your_secret` (get from Stripe dashboard)
- [ ] `STRIPE_WEBHOOK_SECRET=whsec_your_webhook` (create webhook first)

### 📧 Mail Configuration (Optional)
- [ ] `MAIL_MAILER=smtp`
- [ ] `MAIL_HOST=smtp.gmail.com`
- [ ] `MAIL_PORT=587`
- [ ] `MAIL_USERNAME=your-email@gmail.com`
- [ ] `MAIL_PASSWORD=your-app-password`
- [ ] `MAIL_ENCRYPTION=tls`
- [ ] `MAIL_FROM_ADDRESS=noreply@easypass.com`
- [ ] `MAIL_FROM_NAME=EasyPass EMS`

### 🌐 CORS Configuration (Update after frontend deployment)
- [ ] `SANCTUM_STATEFUL_DOMAINS=your-frontend.vercel.app`
- [ ] `SESSION_DOMAIN=.up.railway.app`
- [ ] `CORS_ALLOWED_ORIGINS=https://your-frontend.vercel.app`

## Deployment Verification

- [ ] **Build Completed Successfully**
  - Check "Deployments" tab for green checkmark
  - No errors in build logs

- [ ] **Database Setup Completed**
  - Migrations ran successfully
  - Admin user seeded (admin@easypass.com / admin123)
  - Laravel Passport keys generated

- [ ] **API Health Check**
  - Visit: `https://your-project.up.railway.app/api/health`
  - Returns: `{"status":"success","message":"EasyPass API is running"}`

- [ ] **Domain Configuration**
  - Noted Railway URL for frontend setup
  - Updated `APP_URL` environment variable

## Post-Deployment Tasks

- [ ] **Security**
  - Change admin password on first login
  - Verify all environment variables are set correctly

- [ ] **Stripe Webhook Setup**
  - Create webhook in Stripe dashboard
  - Point to: `https://your-project.up.railway.app/api/payments/webhook/stripe`
  - Update `STRIPE_WEBHOOK_SECRET` variable

- [ ] **Testing**
  - Test API endpoints
  - Verify database connectivity
  - Check error logs

## 🔗 Important URLs

- **Railway Dashboard**: https://railway.app/dashboard
- **Your API Base URL**: `https://your-project.up.railway.app`
- **Health Check**: `https://your-project.up.railway.app/api/health`
- **Admin Login**: Will be configured after frontend deployment

## 🆘 Troubleshooting

### Build Fails
- Check environment variables are set correctly
- Verify `APP_KEY` is properly formatted
- Check build logs for specific errors

### Database Connection Issues
- Ensure MySQL service is running
- Verify database environment variables
- Check Railway MySQL service logs

### Migration Errors
- Check database permissions
- Verify migration files are valid
- Look for foreign key constraint errors

---

**Next Step**: Deploy frontend to Vercel and update CORS settings!
