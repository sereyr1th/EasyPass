# 🚨 GET YOUR REAL STRIPE KEYS

## The Issue
The demo keys I used are **invalid**. You need **real test keys** from Stripe.

## 🚀 Quick Setup (2 minutes)

### Step 1: Create Stripe Account
1. Go to **https://dashboard.stripe.com/register**
2. Sign up with your email (it's free)
3. **Skip** business verification for now

### Step 2: Get Your Test Keys
1. In Stripe Dashboard, go to **Developers** → **API keys**
2. Make sure you're in **Test mode** (toggle in top-left)
3. Copy these two keys:
   - **Publishable key** (starts with `pk_test_`)
   - **Secret key** (starts with `sk_test_`)

### Step 3: Update Environment Files

#### Backend (`.env` file):
```bash
# Replace these lines in backend/.env
STRIPE_KEY=pk_test_YOUR_REAL_PUBLISHABLE_KEY_HERE
STRIPE_SECRET=sk_test_YOUR_REAL_SECRET_KEY_HERE
STRIPE_WEBHOOK_SECRET=whsec_test_demo_webhook_secret
```

#### Frontend (`.env` file):
```bash
# Replace this line in frontend/.env  
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_YOUR_REAL_PUBLISHABLE_KEY_HERE
```

### Step 4: Restart Everything
```bash
# Backend
cd backend
php artisan config:clear
php artisan serve

# Frontend (new terminal)
cd frontend
npm run dev
```

## 🧪 Test Cards (Once Keys Are Set)
- **Success**: `4242424242424242`
- **Decline**: `4000000000000002`
- **Expiry**: `12/25`, **CVC**: `123`

## ✅ You'll Know It Works When...
- No more "Invalid API Key" errors in Laravel logs
- Payment form loads without errors
- Test transactions complete successfully

---

**💡 The payment system is fully built and ready - you just need real Stripe keys!**
