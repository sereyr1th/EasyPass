# Railway Environment Variables Setup

Copy and paste these environment variables in your Railway project:

## Essential Variables (Required)

```bash
APP_NAME=EasyPass EMS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-project-name.up.railway.app
APP_TIMEZONE=UTC

# Database (Railway auto-provides these)
DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

# Session & Cache
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
SESSION_LIFETIME=120

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=info
```

## App Key Generation

You need to generate a Laravel application key. Use one of these methods:

### Method 1: Online Generator
1. Go to https://generate-random.org/laravel-key-generator
2. Copy the generated key (starts with `base64:`)
3. Add as `APP_KEY` variable

### Method 2: Local Generation (if you have PHP)
```bash
cd backend
php artisan key:generate --show
```

## Stripe Configuration (Required for payments)

```bash
STRIPE_KEY=pk_live_your_publishable_key_here
STRIPE_SECRET=sk_live_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

## Mail Configuration (Optional but recommended)

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

## CORS Configuration (Update after frontend deployment)

```bash
SANCTUM_STATEFUL_DOMAINS=your-frontend.vercel.app
SESSION_DOMAIN=.up.railway.app
CORS_ALLOWED_ORIGINS=https://your-frontend.vercel.app
```

---

⚠️ **Important Notes:**
- Replace `your-project-name` with your actual Railway project URL
- Update CORS settings after you deploy the frontend
- Use live Stripe keys for production
- Keep all secrets secure and never commit them to git
