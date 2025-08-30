# 🚀 Vercel Frontend Setup Checklist

## Pre-Deployment Setup

- [ ] **Vercel Account Created**
  - Signed up at vercel.com
  - Connected GitHub account

- [ ] **Project Import**
  - Clicked "New Project"
  - Selected EasyPass-EMS repository
  - **IMPORTANT**: Set Root Directory to `frontend`

## Build Configuration

Verify these settings in Vercel:

- [ ] **Framework Preset**: Vite
- [ ] **Root Directory**: `frontend` ⚠️ **Critical!**
- [ ] **Build Command**: `npm run build`
- [ ] **Output Directory**: `dist`
- [ ] **Install Command**: `npm install`
- [ ] **Node.js Version**: 18.x or higher

## Environment Variables

Add these in Vercel → Project Settings → Environment Variables:

### ✅ Required Variables
- [ ] `NODE_ENV=production`
- [ ] `VITE_API_URL=https://your-railway-app.up.railway.app` ⚠️ **Update with your Railway URL**
- [ ] `VITE_APP_NAME=EasyPass EMS`
- [ ] `VITE_APP_VERSION=1.0.0`

### 💳 Stripe Configuration (Required for payments)
- [ ] `VITE_STRIPE_PUBLISHABLE_KEY=pk_live_your_publishable_key` (same as Railway STRIPE_KEY)

### 🎯 Example Values
```bash
NODE_ENV=production
VITE_API_URL=https://easypass-backend-production.up.railway.app
VITE_APP_NAME=EasyPass EMS
VITE_APP_VERSION=1.0.0
VITE_STRIPE_PUBLISHABLE_KEY=pk_live_51234567890abcdef...
```

## Deployment Process

- [ ] **Initial Deploy**
  - Click "Deploy" in Vercel
  - Monitor build logs for errors
  - Get your Vercel URL (e.g., `https://easypass-frontend.vercel.app`)

- [ ] **Build Success Check**
  - No TypeScript errors
  - No build failures
  - Assets compiled successfully

## Post-Deployment Configuration

### 🌐 Update Backend CORS (Critical!)

Once you have your Vercel URL, update these in Railway:

- [ ] `SANCTUM_STATEFUL_DOMAINS=your-app.vercel.app` (without https://)
- [ ] `CORS_ALLOWED_ORIGINS=https://your-app.vercel.app` (with https://)
- [ ] `APP_URL=https://your-railway-app.up.railway.app` (your backend URL)

### 🔄 Redeploy Backend
- [ ] Trigger Railway redeploy after CORS update

## Testing Checklist

### 🏠 Frontend Tests
- [ ] **Homepage loads** - `https://your-app.vercel.app`
- [ ] **Navigation works** - All menu items functional
- [ ] **Responsive design** - Mobile/desktop views

### 🔗 API Integration Tests
- [ ] **Health check** - Network tab shows successful API calls
- [ ] **User registration** - Can create new account
- [ ] **User login** - Can authenticate successfully
- [ ] **Events loading** - Events display on homepage
- [ ] **Event details** - Individual event pages work

### 💳 Payment Flow Tests
- [ ] **Stripe integration** - Payment form loads
- [ ] **Test payment** - Use Stripe test cards
- [ ] **Ticket generation** - Tickets created after payment

## Custom Domain (Optional)

- [ ] **Add Custom Domain**
  - Go to Vercel → Project → Settings → Domains
  - Add your domain (e.g., `easypass.yourdomain.com`)
  - Update DNS records as instructed
  - Update backend CORS with new domain

## Performance Optimization

- [ ] **Lighthouse Score** - Check performance metrics
- [ ] **Bundle Size** - Monitor and optimize if needed
- [ ] **CDN Configuration** - Vercel handles this automatically

## 🔗 Important URLs

- **Vercel Dashboard**: https://vercel.com/dashboard
- **Your Frontend URL**: `https://your-app.vercel.app`
- **Your Backend URL**: `https://your-railway-app.up.railway.app`

## 🆘 Troubleshooting

### Build Fails
- Check Node.js version compatibility
- Verify all dependencies are in package.json
- Check TypeScript errors in build logs

### API Connection Issues
- Verify VITE_API_URL is correct
- Check CORS settings in backend
- Inspect Network tab in browser dev tools

### Stripe Issues
- Verify publishable key is correct
- Check Stripe dashboard for test/live mode
- Ensure webhook endpoints are configured

### 404 Errors on Refresh
- Vercel should handle SPA routing automatically via vercel.json
- Check if vercel.json exists in frontend folder

---

## 📋 Quick Commands

### Update Environment Variables
```bash
# In Vercel dashboard
Project Settings → Environment Variables → Add
```

### Check Build Logs
```bash
# In Vercel dashboard
Deployments → Click on deployment → View Function Logs
```

### Force Redeploy
```bash
# In Vercel dashboard
Deployments → Click "Redeploy" on latest deployment
```

---

**Next Step**: After Vercel deployment, update Railway CORS settings!
