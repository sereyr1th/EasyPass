# EasyPass EMS Deployment Guide

This guide will help you deploy the EasyPass Event Management System using Railway for the backend and Vercel for the frontend.

## Prerequisites

- Railway account (https://railway.app)
- Vercel account (https://vercel.com)
- Stripe account for payment processing
- GitHub repository with your code

## Backend Deployment (Railway)

### 1. Create Railway Project

1. Go to [Railway](https://railway.app) and sign in
2. Click "New Project"
3. Choose "Deploy from GitHub repo"
4. Select your EasyPass-EMS repository
5. Railway will automatically detect it's a PHP/Laravel project

### 2. Add MySQL Database

1. In your Railway project dashboard, click "New"
2. Select "Database" → "Add MySQL"
3. Railway will create a MySQL instance and provide connection details

### 3. Configure Environment Variables

In your Railway project settings, add these environment variables:

```bash
# Laravel Configuration
APP_NAME="EasyPass EMS"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app
APP_TIMEZONE=UTC

# Database (Auto-populated by Railway MySQL)
DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

# Cache & Session
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
SESSION_LIFETIME=120

# Mail Configuration (Configure with your SMTP provider)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@easypass.com"
MAIL_FROM_NAME="EasyPass EMS"

# Stripe Configuration
STRIPE_KEY=pk_live_your_stripe_publishable_key
STRIPE_SECRET=sk_live_your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# CORS (Update with your Vercel domain)
SANCTUM_STATEFUL_DOMAINS=your-app.vercel.app
SESSION_DOMAIN=.up.railway.app
CORS_ALLOWED_ORIGINS=https://your-app.vercel.app

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=info
```

### 4. Generate Laravel App Key

1. In Railway, go to your project
2. Open the "Deploy Logs" tab
3. Once deployed, you can generate the app key by running:
   ```bash
   php artisan key:generate --show
   ```
4. Copy the generated key and update the `APP_KEY` environment variable

### 5. Deploy Configuration

Railway will use the `railway.json` configuration file automatically. The deployment process includes:

- Installing Composer dependencies
- Running database migrations
- Setting up Laravel Passport
- Seeding admin user
- Caching configuration

### 6. Custom Domain (Optional)

1. In Railway project settings, go to "Settings" → "Domains"
2. Add your custom domain
3. Update `APP_URL` environment variable with your custom domain

## Frontend Deployment (Vercel)

### 1. Create Vercel Project

1. Go to [Vercel](https://vercel.com) and sign in
2. Click "New Project"
3. Import your GitHub repository
4. Select the `frontend` folder as the root directory
5. Vercel will auto-detect it's a Vite project

### 2. Configure Build Settings

Vercel should automatically detect the configuration from `vercel.json`, but verify:

- **Framework Preset**: Vite
- **Root Directory**: `frontend`
- **Build Command**: `npm run build`
- **Output Directory**: `dist`
- **Install Command**: `npm install`

### 3. Environment Variables

In Vercel project settings → Environment Variables, add:

```bash
NODE_ENV=production
VITE_API_URL=https://your-railway-app.up.railway.app
VITE_APP_NAME="EasyPass EMS"
VITE_APP_VERSION="1.0.0"
VITE_STRIPE_PUBLISHABLE_KEY=pk_live_your_stripe_publishable_key
```

### 4. Deploy

1. Click "Deploy"
2. Vercel will build and deploy your frontend
3. You'll get a URL like `https://your-app.vercel.app`

### 5. Update Backend CORS

After deployment, update your Railway backend environment variables:

```bash
SANCTUM_STATEFUL_DOMAINS=your-app.vercel.app
CORS_ALLOWED_ORIGINS=https://your-app.vercel.app
```

## Stripe Configuration

### 1. Webhook Setup

1. In your Stripe Dashboard, go to "Developers" → "Webhooks"
2. Click "Add endpoint"
3. Set URL: `https://your-railway-app.up.railway.app/api/payments/webhook/stripe`
4. Select events:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `payment_intent.canceled`
5. Copy the webhook secret and update `STRIPE_WEBHOOK_SECRET` in Railway

### 2. API Keys

1. Get your Stripe API keys from "Developers" → "API keys"
2. Use live keys for production:
   - Publishable key → `STRIPE_KEY` (Railway) and `VITE_STRIPE_PUBLISHABLE_KEY` (Vercel)
   - Secret key → `STRIPE_SECRET` (Railway)

## Post-Deployment Setup

### 1. Admin User

The deployment automatically creates an admin user:
- **Email**: admin@easypass.com
- **Password**: admin123

⚠️ **Important**: Change this password immediately after first login!

### 2. Laravel Passport

The deployment automatically sets up Laravel Passport keys. If needed manually:

```bash
php artisan passport:keys --force
php artisan passport:client --personal --no-interaction
```

### 3. File Storage

Configure file storage for event images:
1. Set up cloud storage (AWS S3, Cloudinary, etc.)
2. Update `config/filesystems.php`
3. Set appropriate environment variables

## Monitoring & Maintenance

### Railway Monitoring

- Monitor logs in Railway dashboard
- Set up alerts for errors
- Monitor database performance

### Vercel Monitoring

- Check function logs in Vercel dashboard
- Monitor build times and deployment status
- Set up custom domains and SSL

### Database Backups

1. Set up automated backups in Railway
2. Consider implementing backup strategies
3. Test restore procedures regularly

## Troubleshooting

### Common Issues

1. **CORS Errors**: Verify `CORS_ALLOWED_ORIGINS` matches your Vercel domain
2. **Database Connection**: Check MySQL credentials in Railway
3. **Stripe Webhooks**: Verify webhook URL and secret
4. **File Permissions**: Ensure storage directories are writable
5. **Laravel Key**: Make sure `APP_KEY` is properly set

### Logs

- **Railway**: Check deploy logs and application logs
- **Vercel**: Check function logs and build logs
- **Laravel**: Use `php artisan logs` or check Railway logs

## Security Checklist

- [ ] Change default admin credentials
- [ ] Use strong `APP_KEY`
- [ ] Enable HTTPS only
- [ ] Configure proper CORS settings
- [ ] Use environment variables for all secrets
- [ ] Set up proper file permissions
- [ ] Enable rate limiting
- [ ] Configure CSP headers

## Performance Optimization

- [ ] Enable Laravel caching (config, routes, views)
- [ ] Use CDN for static assets
- [ ] Optimize database queries
- [ ] Enable Gzip compression
- [ ] Monitor and optimize bundle size
- [ ] Set up proper caching headers

---

## Support

For deployment issues:
1. Check the troubleshooting section
2. Review Railway and Vercel documentation
3. Check Laravel and Vue.js documentation
4. Contact support if needed

Happy deploying! 🚀
